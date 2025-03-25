const express = require('express');
require('dotenv').config();
const app = express();
const server = require('http').createServer(app);
const io = require('socket.io')(server, {
    cors: {
        origin: "*",  // Replace with your actual client's URL
        methods: ["GET", "POST"]
    }
});
app.use(express.json());

const SOCKET_PORT = process.env.SOCKET_SERVER_PORT || 3000;
const SOCKET_URL = process.env.SOCKET_SERVER_URL ;

io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);
});

app.post('/emit', (req, res) => {

    console.log(`post request received`);

    const { event, data } = req.body;
    io.emit(event, data.lead_id);

    
    console.log(`Event emitted: ${event}`, data);
    res.json({ status: 'Event sent', event, data });
});


io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    // Listen for messages from clients
    socket.on('send_message', (data) => {
        console.log('Data received:', data);
        // Broadcast the message to the intended recipient
        // const c = io.emit('receive_message', data);
        socket.broadcast.emit('receive_message', data);

        // if(c)
        // {
        //     console.log('Message broadcasted:', data);
        // }
        // else
        // {
        //     console.log('Message not broadcasted:', data);
        // }
    });

    // Handle user disconnection
    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});


server.listen(SOCKET_PORT, () => {
    console.log(`Socket.IO server running on url  port ${SOCKET_PORT}`);
});
