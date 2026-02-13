const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
require('dotenv').config();

const app = express();
app.use(express.json({ limit: '100mb' }));
app.use(express.urlencoded({ limit: '100mb', extended: true }));

const axios = require('axios');
const { generateReply } = require('./whatsapp_gemini');

// Initialize WhatsApp client
const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: 'shell',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu',
            '--disable-features=IsolateOrigins,site-per-process'
        ],
        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    },
});

// Display QR code for login
client.on('qr', (qr) => {
    console.log('Scan the QR code below to login to WhatsApp:');
    qrcode.generate(qr, { small: true });
});

client.on('ready', () => {
    console.log('WhatsApp WebJS Client is ready!');
});

client.on('authenticated', () => {
    console.log('Authenticated successfully!');
});

client.on('auth_failure', (msg) => {
    console.error('Authentication failure:', msg);
});

client.on('disconnected', (reason) => {
    console.log('WhatsApp Client was logged out', reason);
    // Attempt to re-initialize
    client.initialize();
});

// Webhook for all message activity (sent and received)
client.on('message_create', async (msg) => {
    // Only process if it's not a newsletter (often seen in logs as @newsletter)
    if (msg.from.includes('@newsletter')) return;

    console.log(`Message event (${msg.fromMe ? 'Sent' : 'Received'}): from ${msg.from} to ${msg.to}: ${msg.body.substring(0, 50)}...`);
    
    try {
        const contact = await msg.getContact();
        const chat = await msg.getChat();
        
        await axios.post('http://localhost/api/whatsapp/webhook', {
            event: 'message_received', // Keep the event name same for backend compatibility
            data: {
                from: msg.from,
                to: msg.to,
                body: msg.body,
                timestamp: msg.timestamp,
                hasMedia: msg.hasMedia,
                fromMe: msg.fromMe,
                contact: {
                    name: contact.name || contact.pushname,
                    number: contact.number
                },
                chat: {
                    id: chat.id._serialized,
                    name: chat.name,
                    isGroup: chat.isGroup
                }
            }
        });

        // Gemini Auto-Reply Integration
        const isStatus = msg.from === 'status@broadcast';
        const isNewsletter = msg.from.includes('@newsletter');
        const EXCLUDED_NUMBERS = [
    // '94786070240@c.us'
    // Tip: Do NOT use just "919876543210" — always include @c.us
];
        const allowedGroupNames = ["LVA-develo", "🧍‍♂️ගමේ කොල්ලෝ🧍‍♂️"];
        
        if (process.env.WHATSAPP_AUTO_REPLY_ENABLED === 'true' && !msg.fromMe 
             && !isStatus && !isNewsletter && !EXCLUDED_NUMBERS.includes(msg.from)
            && (!chat.isGroup || allowedGroupNames.includes(chat.name?.trim() || ""))
        ) {
            console.log(`- Gemini Auto-Reply enabled. Generating reply for: ${msg.body.substring(0, 50)}...`);
            const reply = await generateReply(msg.body);
            if (reply) {
                console.log(`- Sending Gemini auto-reply: ${reply.substring(0, 50)}...`);
                // Use a small delay to feel more natural and avoid immediate bot detection if any
                setTimeout(async () => {
                    await client.sendMessage(msg.from, reply);
                }, 2000);
            }
        }
    } catch (error) {
        console.error('Failed to send message webhook:', error.message);
    }
});

client.initialize();

// API endpoint to send message
app.post('/send-message', async (req, res) => {
    const { to, message, attachment } = req.body;
    console.log(`Incoming request to /send-message for ${to}`);

    if (!to || (!message && !attachment)) {
        return res.status(400).json({ status: 'error', message: 'Missing "to" or content parameters' });
    }

    try {
        let chatId = to;
        if (!to.includes('@')) {
            const numberId = await client.getNumberId(to);
            if (numberId) {
                chatId = numberId._serialized;
            } else {
                chatId = `${to}@c.us`;
            }
        }

        let sentMsg;
        if (attachment && attachment.base64 && attachment.mimetype) {
            console.log(`- Sending attachment: ${attachment.filename}, size: ${Math.round(attachment.base64.length / 1024)} KB`);
            const media = new MessageMedia(
                attachment.mimetype,
                attachment.base64,
                attachment.filename || 'attachment'
            );
            // Added sendSeen: false to avoid markedUnread error
            sentMsg = await client.sendMessage(chatId, media, { caption: message, sendSeen: false });
        } else {
            console.log(`- Sending text message: "${message.substring(0, 50)}..."`);
            // Added sendSeen: false to avoid markedUnread error
            sentMsg = await client.sendMessage(chatId, message, { sendSeen: false });
        }
        
        console.log('Message sent successfully');
        res.json({ status: 'success', data: sentMsg });
    } catch (error) {
        console.error('Failed to send WhatsApp message:', error.message);
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Get all chats
app.get('/chats', async (req, res) => {
    if (!client.info) {
        return res.status(503).json({ status: 'error', message: 'WhatsApp client is not ready yet' });
    }
    console.log('Fetching chats...');
    try {
        const allChats = await client.getChats();
        // Limit to 50 most recent chats to ensure performance
        const chats = allChats.slice(0, 50);
        
        const chatData = await Promise.all(chats.map(async (chat) => {
            try {
                // Safely attempt to get contact details
                let name = chat.name;
                let number = '';
                let contact = null;
                
                try {
                    contact = await chat.getContact();
                    name = name || contact.name || contact.pushname;
                    number = contact.number;
                } catch (contactError) {
                    console.warn(`Failed to get contact for chat ${chat.id._serialized}: ${contactError.message}`);
                    number = chat.id.user;
                }
                
                // Fetch profile pic with timeout to prevent blocking
                let profilePicUrl = null;
                if (contact) {
                    try {
                        profilePicUrl = await Promise.race([
                            contact.getProfilePicUrl(),
                            new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 2000))
                        ]);
                    } catch (e) {
                        // Ignore DP errors
                    }
                }

                return {
                    id: chat.id._serialized,
                    name: name,
                    number: number,
                    unreadCount: chat.unreadCount,
                    timestamp: chat.timestamp,
                    lastMessage: chat.lastMessage ? {
                        body: chat.lastMessage.body,
                        timestamp: chat.lastMessage.timestamp,
                        fromMe: chat.lastMessage.fromMe
                    } : null,
                    profilePicUrl: profilePicUrl
                };
            } catch (innerError) {
                console.error(`Error processing chat ${chat.id._serialized}:`, innerError.message);
                return null;
            }
        }));
        
        const validChatData = chatData.filter(chat => chat !== null);
        console.log(`Returning ${validChatData.length} chats`);
        res.json(validChatData);
    } catch (error) {
        console.error('Failed to get chats:', error);
        // Return structured error instead of failing request effectively
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Get total unread count for navbar
app.get('/unread-count', async (req, res) => {
    try {
        const chats = await client.getChats();
        const totalUnread = chats.reduce((sum, chat) => sum + (chat.unreadCount || 0), 0);
        res.json({ count: totalUnread });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Get messages for a chat
app.get('/messages/:chatId', async (req, res) => {
    try {
        const chat = await client.getChatById(req.params.chatId);
        const messages = await chat.fetchMessages({ limit: 50 });
        const messageData = messages.map(msg => ({
            id: msg.id._serialized,
            body: msg.body,
            from: msg.from,
            to: msg.to,
            timestamp: msg.timestamp,
            fromMe: msg.fromMe,
            hasMedia: msg.hasMedia,
            type: msg.type
        }));
        res.json(messageData);
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Get profile picture
app.get('/profile-pic/:userId', async (req, res) => {
    try {
        const contact = await client.getContactById(req.params.userId);
        const url = await contact.getProfilePicUrl();
        res.json({ url });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// Get media for a message
app.get('/message/media', async (req, res) => {
    const { msgId } = req.query;
    if (!msgId) {
        return res.status(400).json({ status: 'error', message: 'Missing msgId parameter' });
    }

    try {
        console.log(`Fetching media for message: ${msgId}`);
        let msg;
        try {
            if (client.getMessageById) {
                 msg = await client.getMessageById(msgId);
            }
        } catch (e) {}

        if (!msg) {
            // Fallback: Parse chat ID from msg ID
            // ID format: fromMe_remote_id
            const parts = msgId.split('_');
            if (parts.length >= 2) {
                const chatId = parts[1];
                try {
                    const chat = await client.getChatById(chatId);
                    const messages = await chat.fetchMessages({ limit: 50 }); 
                    msg = messages.find(m => m.id._serialized === msgId);
                } catch (e) {
                   console.warn('Failed to fetch chat or messages for media:', e.message);
                }
            }
        }

        if (!msg) {
            return res.status(404).json({ status: 'error', message: 'Message not found' });
        }

        if (!msg.hasMedia) {
            return res.status(400).json({ status: 'error', message: 'Message has no media' });
        }

        const media = await msg.downloadMedia();
        if (!media) {
            return res.status(404).json({ status: 'error', message: 'Failed to download media' });
        }

        res.json({
            mimetype: media.mimetype,
            data: media.data,
            filename: media.filename
        });

    } catch (error) {
        console.error('Failed to download media:', error);
        res.status(500).json({ status: 'error', message: error.message });
    }
});

app.get('/status', (req, res) => {
    res.json({ 
        status: 'online', 
        client_ready: client.info ? true : false,
        client_info: client.info || null
    });
});

const PORT = 3001;
app.listen(PORT, () => {
    console.log(`WhatsApp WebJS API server running on port ${PORT}`);
});
