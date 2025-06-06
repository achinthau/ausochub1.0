import './bootstrap';

import moment from 'moment';

moment().format();

window.moment = moment;

import Echo from 'laravel-echo';
import {livewire_hot_reload} from 'virtual:livewire-hot-reload'


import Pusher from 'pusher-js';
window.Pusher = Pusher;
 
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: 0
});

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'
 
Alpine.plugin(focus);
window.Alpine = Alpine;

Alpine.start();
livewire_hot_reload();

import.meta.glob([
    '../images/**',
    '../fonts/**',
  ]);

var channel = window.Echo.channel('chat');
channel.listen('.my‑channel', function (data) {

    var userId = document.querySelector('meta[name="user-extension"]').content;
    console.log(data.lead.id);
    console.log(userId);
    if (userId == data.lead.extension) {
        var win = window.open('/leads/'+data.lead.id, '_blank');
        if (win) {
            win.focus();
        }
        //console.log("test");
        //window.location.replace("/leads");

    }
});
channel.listen('.notify-order', function (data) {
    console.log(data.lead.id);
    alert('new one came');
});


// Socket.io listener
// const socket = io('http://127.0.0.1:3000'); // Use your socket server URL and port
const socket = io({
        path: "/socket.io",
        transports: ['websocket'],
    });

socket.on('connect', () => {
    console.log('Socket connected:', socket.id);
});

// Listen for the same event emitted from server
// socket.on('call.answered', function(leadId) {
//     var userId = document.querySelector('meta[name="user-extension"]').content;

//     // For demonstration, just open the lead detail:
//     var win = window.open('/leads/' + leadId, '_blank');
//     if (win) win.focus();
// });



// Store reference to the opened tab
let leadWindow = null;

// Listen for the event from the server
socket.on('call.answered', function (leadId) {
    // const userId = document.querySelector('meta[name="user-extension"]').content;
    var userId = document.querySelector('meta[name="user-extension"]').content;
    if (userId == data.lead.extension) {

    // If the window is already open and not closed
    if (leadWindow && !leadWindow.closed) {
        // Just update the URL in the existing tab
        leadWindow.location.href = '/leads/' + leadId;
        leadWindow.focus();
    } else {
        // Open a new tab and store the reference
        leadWindow = window.open('/leads/' + leadId, '_blank');
    }
}
});


