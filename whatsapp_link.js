const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
require('dotenv').config();

const app = express();
app.use(express.json());

// Initialize WhatsApp client
const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ],
        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    },
    webVersionCache: {
        type: 'remote',
        remotePath: 'https://raw.githubusercontent.com/wppconnect-team/wa-version/main/html/2.2412.54.html',
    }
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

client.initialize();

// API endpoint to send message
app.post('/send-message', async (req, res) => {
    const { to, message } = req.body;
    console.log(`Received request to send message to: ${to}`);

    if (!to || !message) {
        return res.status(400).json({ status: 'error', message: 'Missing "to" or "message" parameters' });
    }

    try {
        let chatId;
        if (to.includes('@c.us') || to.includes('@g.us')) {
            chatId = to;
        } else {
            // Resolve the number to the correct ID
            const numberId = await client.getNumberId(to);
            if (numberId) {
                chatId = numberId._serialized;
            } else {
                // Fallback to manual formatting if getNumberId fails
                chatId = `${to}@c.us`;
                console.log(`Could not resolve number ID for ${to}, using fallback: ${chatId}`);
            }
        }
        
        console.log(`Sending message to ${chatId}: ${message}`);
        await client.sendMessage(chatId, message, { sendSeen: false });
        
        res.json({ status: 'success' });
    } catch (error) {
        console.error('Failed to send message:', error);
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
