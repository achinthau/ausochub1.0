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

// io.on("connection", (socket) => {
//     console.log("A user connected:", socket.id);
// });

app.post("/emit", (req, res) => {
    console.log(`post request received`);

    const { event, data } = req.body;
    // io.emit(event, data.lead_id);
    io.emit(event, { lead: data });

    console.log(`Event emitted: ${event}`, data);
    res.json({ status: "Event sent", event, data });
});



const connectedUsers = {};

io.on("connection", (socket) => {
    console.log("A user connected:", socket.id);


socket.on("user_connected", (userId) => {
        console.log(`User ${userId} connected via socket ${socket.id}`);
        connectedUsers[socket.id] = userId;
    });


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
    // socket.on("disconnect", () => {
    //     console.log("User disconnected:", socket.id);
    // });

    socket.on("disconnect", async () => {
    const userId = connectedUsers[socket.id];

    if (userId) {
        console.log(`User ${userId} disconnected.`);

        delete connectedUsers[socket.id];

        // Delay logout  (due to page reload)
        setTimeout(async () => {
            const isStillDisconnected = !Object.values(connectedUsers).includes(userId);

            if (isStillDisconnected) {
                const axios = require("axios");
                try {
                    await axios.post(`${process.env.APP_URL}/api/logout-socket`, {
                        //port should mention
                        user_id: userId,
                    });
                    console.log(`User ${userId} logout recorded in Laravel`);
                } catch (error) {
                    console.error("Failed to notify Laravel:", error.message);
                }
            } else {
                console.log(`User ${userId} reconnected before timeout, no logout triggered.`);
            }

            
        }, 5000); // 5s delay
    } else {
        console.log("Disconnected socket without user info:", socket.id);
    }
});


});

server.listen(SOCKET_PORT, () => {
    console.log(`Socket.IO server running on url  port ${SOCKET_PORT}`);
});
