<div class="w-full h-[80vh] px-4 pt-1 break-words">
    @if ($receiver)
        <div class="py-2 pl-0 bg-gray-200 rounded-md">
            <h1 class=" text-xl pl-4 font-bold">
                {{ $receiver ? $receiver->name : '' }}
            </h1>
        </div>
    @endif


    <div id="messagesContainer" class=" h-[55vh] overflow-y-auto pb-0 pt-2 mt-2">
        {{-- @if (isset($receiver) && $receiver)
    @livewire('chat.messages')
@endif --}}

        @if ($receiver)

        <span class="block text-center cursor-pointer font-italic" wire:click="getOlderMessages({{ auth()->id() }}, {{ $receiver }})">older</span>

            @if (!empty($messages) && is_array($messages))
                @foreach ($messages as $message)
                    @if (isset($message['sender'], $message['text']))
                        <!-- Ensure valid message -->
                        <div class="mb-2">
                            @if ($message['sender'] == auth()->id())
                                <div class="text-right">
                                    <span class="bg-blue-100 text-black px-3 py-1 rounded-lg">
                                        <span class="pb-2 text-md">{{ $message['text'] }}</span>
                                        <span class="text-[9px] pl-4 italic">
                                            {{ \Carbon\Carbon::createFromTimestamp($message['timestamp'])->format('h:i A') }}
                                        </span>
                                    </span>
                                </div>
                            @endif
                            @if ($message['receiver'] == auth()->id())
                                <div class="text-left">
                                    <span class="bg-gray-200 text-black px-3 py-1 rounded-lg">

                                        <span class="pb-2">{{ $message['text'] }}</span>
                                        <span class="text-[9px] pl-4 italic">
                                            {{ \Carbon\Carbon::createFromTimestamp($message['timestamp'])->format('h:i A') }}
                                        </span>

                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            @else
                <p>No messages yet.</p>
            @endif
        @endif


        {{-- <div class="pt-4 flex justify-end"> --}}

        {{-- <div id="messages" class="min-h-[10px] p-0 m-0" style="height: 5%"></div> --}}


        {{-- </div> --}}

    </div>

    {{-- @if ($receiver) --}}
    <div class="absolute bottom-8 mb-4 pr-4 pl-4 flex">
        {{-- <input type="text" placeholder="Type your message" id="messageInput" class="w-[12cm]"> --}}
        <div class="pr-4">
            <textarea name="" id="messageInput" cols="92" rows="2" class="pr-4 rounded-md"></textarea>
            <input type="hidden" id="receiver" value="{{ $receiver ? $receiver->id : '' }}">
        </div>
        <div class="pt-5 pr-8">
            {{-- <button class="pl-8 pr-8" onclick="sendMessage()">Send</button> --}}
            <svg class="w-8 h-8 cursor-pointer" onclick="sendMessage()" xmlns="http://www.w3.org/2000/svg"
                width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z">
                </path>
            </svg>
        </div>
    </div>
    {{-- @endif --}}

    {{-- only for data save in redis --}}
    <livewire:chat.messages /> 



    {{-- send message from enter press --}}
    <script>
        document.getElementById("messageInput").addEventListener("keydown", function(event) {
            if (event.key === "Enter" && !event.shiftKey) { // Prevent shift+enter from submitting
                event.preventDefault(); // Prevent new line in textarea
                sendMessage(); // Call the function to send the message
            }
        });

        function sendMessage() {
            let message = document.getElementById("messageInput").value.trim();

            if (message !== "") {
                Livewire.emit("sendMessage", message); // Emit event to Livewire
                document.getElementById("messageInput").value = ""; // Clear input after sending
            }
        }
    </script>


    {{-- scrololl to latest message --}}

    <script>
        document.addEventListener('scrollToBottom', function() {
            let container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight; // Scroll to bottom
            }
        });
    </script>




    <script>
        // const socket = io('http://localhost:3000');
        const socket = io({
        path: "/socket.io",
        transports: ['websocket'],
    });
        const loggedInUserId = "{{ auth()->id() }}";

        // Listen for incoming messages
        document.addEventListener("DOMContentLoaded", function() {
            if (!window.socketInitialized) {
                window.socketInitialized = true; // Prevent duplicate bindings

                // const socket = io('http://localhost:3000');
                const loggedInUserId = "{{ auth()->id() }}";

                socket.on('receive_message', (data) => {
                    console.log('Received:', data);
                    console.log('Logged-in user:', loggedInUserId);

                    const sender = data.from;
                    const receiver = document.getElementById('receiver').value;

                    if (data.to == loggedInUserId || data.from == loggedInUserId) {
                        if (data.from !== loggedInUserId && data.from == receiver) {
                            displayMessage_1(data.from, data.text, "received");
                        } else {
                            Livewire.emitTo("chat.users-panel", "highlightUser", sender);
                        }
                    }
                });
            }
        });



        async function displayMessage_1(sender, text, type) {
            const messagesDiv = document.getElementById('messagesContainer');

            console.log(sender, text, type);
            // console.trace("Function called from:");

            // Fetch user details from the database
            try {
                
                    const messageWrapper = document.createElement("div"); // Create a wrapper div
                    messageWrapper.style.marginBottom = "4px"; // Adds spacing between messages

                    // Create the message element
                    const messageElement = document.createElement("span");
                    if (type == "received") {
                        messageWrapper.classList.add("text-left")
                    messageElement.classList.add("bg-gray-200", "text-black", "px-3", "py-1", "rounded-lg");
                    }

                    if (type == "sent") {
                        messageWrapper.classList.add("text-right")
                    messageElement.classList.add("bg-blue-100", "text-black", "px-3", "py-1", "rounded-lg");
                    }

                    messageElement.style.display = "inline-block";

                    // Create the message text span
                    const messageText = document.createElement("span");
                    messageText.innerText = text;
                    messageText.style.paddingBottom = "2px";

                    // Create the timestamp span
                    const timestampElement = document.createElement("span");
                    timestampElement.classList.add("text-[9px]", "pl-4", "italic");

                    // Format timestamp to "h:i A" (12-hour format)
                    const formattedTime = new Date().toLocaleTimeString("en-US", {
                        hour: "2-digit",
                        minute: "2-digit",
                        hour12: true
                    });

                    timestampElement.innerText = formattedTime;

                    // Append elements
                    messageElement.appendChild(messageText);
                    messageElement.appendChild(timestampElement);
                    messageWrapper.appendChild(messageElement);

                    // Append to container
                    document.getElementById("messagesContainer").appendChild(messageWrapper);

                    // Auto-scroll to the latest message
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                

            } catch (error) {
                console.error('Error fetching user details:', error);
            }
        }

        // Send a message
        function sendMessage() {
            const username = loggedInUserId;
            const receiver = document.getElementById('receiver').value;
            const messageInput = document.getElementById('messageInput');
            const text = messageInput.value.trim();

            if (!username || !text) {
                alert("Enter a username and a message!");
                return;
            }

            const message = {
                from: username,
                to: receiver,
                text
            };



            socket.emit('send_message', message);
            displayMessage_1("Me", text, "sent"); // Show message instantly for sender
            // Livewire.emitTo("chat.messages-panel", "saveData", username, receiver, text);
            Livewire.emitTo("chat.messages", "saveData", username, receiver, text);
            
            messageInput.value = '';


        }
    </script>

    <style>
        #messages {
            /* border: 1px solid #000; */
            padding: 10px;
            height: 600px;
            overflow-y: auto;
            width: 100%;
        }

        .message {
            padding: 3px 2px;
            margin: 5px 0;
            border-radius: 5px;
        }

        .sent {
            background-color: #3b82f6;
            text-align: right;
        }

        .received {
            background-color: #d6d3d1;
            text-align: left;
        }
    </style>
</div>
