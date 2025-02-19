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

server.listen(SOCKET_PORT, () => {
    console.log(`Socket.IO server running on url  port ${SOCKET_PORT}`);
});
