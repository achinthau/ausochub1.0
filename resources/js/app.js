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



