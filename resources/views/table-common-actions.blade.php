<div class="flex justify-between">
<div class="flex space-x-1 justify-around">
    <a href="#" onclick="Livewire.emitTo('settings.users.show','show',{{$id}})"
        class="p-1 text-teal-600 hover:bg-teal-600 hover:text-white rounded">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd"
                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                clip-rule="evenodd"></path>
        </svg>
    </a>

    
</div>

<div class="flex space-x-1 justify-around">
    <a href="#"    onclick="if (confirm('Are you sure you want to delete this user?')) Livewire.emitTo('settings.users.show','delete', {{ $id }})"

        class="p-1 text-red-600 hover:bg-red-600 hover:text-white rounded">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"><path d="M7 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2h4a1 1 0 1 1 0 2h-1.069l-.867 12.142A2 2 0 0 1 17.069 22H6.93a2 2 0 0 1-1.995-1.858L4.07 8H3a1 1 0 0 1 0-2h4V4zm2 2h6V4H9v2zM6.074 8l.857 12H17.07l.857-12H6.074zM10 10a1 1 0 0 1 1 1v6a1 1 0 1 1-2 0v-6a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 1 1-2 0v-6a1 1 0 0 1 1-1z" fill="currentColor"></path></svg>
    </a>

    {{-- <script>
    window.addEventListener('notification', event => {
        alert(event.detail.message); // simple browser alert
    });
</script> --}}



{{-- <script>
    window.addEventListener('notification', event => {
        Swal.fire({
            icon: event.detail.type || 'info',
            title: event.detail.title || 'Warning..',
            text: event.detail.message || '',
            width: '350px', // 👈 Make it smaller (default is 500px)
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'custom-swal-popup'
            }
        }).then((result) => {
            if (result.isConfirmed && event.detail.confirmEvent) {
                Livewire.emit(event.detail.confirmEvent, event.detail.id);
            }
        });
    });
</script> --}}

<script>
window.addEventListener('notification', event => {
  Swal.fire({
    title: event.detail.title || 'Notice',
    text: event.detail.message || '',
    icon: event.detail.type || 'info',
    showConfirmButton: false,
    allowOutsideClick: true,
    // timer: 3000
  });
});
</script>





</div>
</div>