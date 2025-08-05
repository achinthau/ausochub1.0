{{-- <div wire:poll.10s="updateReminderStatus" class="pt-1 cursor-pointer" wire:click="showNotifications"> --}}
<div wire:poll.10s="updateReminderStatus" class="pt-1 cursor-pointer" >
  <a href="{{ route('reminder.clear') }}">
    @if($isReminder)
        {{-- Reminder active - green icon --}}
        <svg class="w-8 h-8 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2c.15-2.65 1.51-4.97 3.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43c2.02 1.45 3.39 3.77 3.54 6.42zM18 11c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2v-5zm-6 11c.14 0 .27-.01.4-.04.65-.14 1.18-.58 1.44-1.18.1-.24.15-.5.15-.78h-4c.01 1.1.9 2 2.01 2z"></path>
        </svg>
    @else
        {{-- Default bell icon --}}
        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"></path>
        </svg>
    @endif
  </a>
</div>



{{-- <script src="https://cdn.socket.io/4.6.1/socket.io.min.js"></script>
<script>
  // Make sure this is set from your app (e.g. from server-rendered blade)
  const userId = @json(auth()->id()); // or set manually for testing

  // Connect to socket.io server
  const socket = io('http://localhost:3000', {
    path: '/socket.io',
    transports: ['websocket']
  });

  // Tell server which user just connected
  socket.emit('user_connected', userId);

  // Listen for notification event for this user
  socket.on('callback_notify', (data) => {
    console.log("Notification received:", data.message);
    alert(data.message);  // Show alert popup when notification arrives
  });
</script> --}}