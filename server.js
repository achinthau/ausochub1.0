const express = require("express");
require("dotenv").config();
const app = express();
const server = require("http").createServer(app);
const { Server } = require("socket.io");
// const io = require('socket.io')(server, {
//     cors: {
//         origin: "*",  // Replace with your actual client's URL
//         methods: ["GET", "POST"]
//     }
// });
const io = new Server(server, {
    path: "/socket.io",
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
    },
});
app.use(express.json());

const SOCKET_PORT = process.env.SOCKET_SERVER_PORT || 3000;
const SOCKET_URL = process.env.SOCKET_SERVER_URL;

io.on("connection", (socket) => {
    console.log("A user connected:", socket.id);
});

app.post("/emit", (req, res) => {
    console.log(`post request received`);

    const { event, data } = req.body;
    // io.emit(event, data.lead_id);
    io.emit(event, { lead: data });

    console.log(`Event emitted: ${event}`, data);
    res.json({ status: "Event sent", event, data });
});

io.on("connection", (socket) => {
    console.log("A user connected:", socket.id);

    // Listen for messages from clients
    socket.on("send_message", (data) => {
        console.log("Data received:", data);
        socket.broadcast.emit("receive_message", data);


        // Custom reusable chat component event
        socket.on("custom_chat_send", (data) => {
            console.log("Custom Chat Message:", data);
            socket.broadcast.emit("custom_chat_receive", data);
        });
    });

    socket.on('hand_raised', (data) => {
        console.log('Hand raised by:', data);

        // Emit to all connected clients (admins)
        io.emit('show_svg', { from: data.from });
    });

    // Handle user disconnection
    socket.on("disconnect", () => {
        console.log("User disconnected:", socket.id);
    });
});

server.listen(SOCKET_PORT, () => {
    console.log(`Socket.IO server running on url  port ${SOCKET_PORT}`);
});
