<x-app-layout>
    @can('is-agent')
        @livewire('dashboard.index')
    @endcan

    @canany(['is-admin', 'client-admin'])
        @livewire('dashboard.admin.index')
    @endcanany


    @if (config('auso.phone_auto_register') && auth()->check()
    && !empty(auth()->user()->extension))
    <!-- Softphone Registration Modal -->
    <div id="softphoneModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden items-center justify-center">

        <div class="bg-transparent rounded-full p-6 w-full relative">

            {{-- <button id="closeModal" class="px-4 py-2 bg-gray-300 rounded mr-2">
                Close
            </button> --}}
            <button id="closeModal"
                class="absolute bottom-2 left-0 bg-transparent hover:bg-slate-100 text-black px-3 py-1 rounded">
                .
            </button>

            {{-- <h2 class="text-lg font-semibold mb-4">Register Your Softphone</h2> --}}

            {{-- <label class="block text-sm mb-1">Extension:</label> --}}

            @php

                $phoneType = strtolower(config('auso.phone_type'));

                if (str_contains($phoneType, 'microsip')) {
                    $softphone = 'microsip';
                } elseif (str_contains($phoneType, 'zoiper')) {
                    $softphone = 'zoiper';
                } else {
                    $softphone = '';
                }
            @endphp


            <input type="text" id="exten" class="border rounded w-full p-2 mb-3" value="{{ auth()->user()->extension }}"
                hidden>

            <input type="text" id="exten_type" class="border rounded w-full p-2 mb-3"
                value="{{ auth()->user()->extensionData->exten_type }}" hidden>

            <input type="text" id="softphone" value={{$softphone}} hidden>


            {{-- <label class="block text-sm mb-1">Choose Softphone:</label> --}}
            {{-- <select id="softphone" class="border rounded w-full p-2 mb-3" hidden>
                <option value="zoiper5">Zoiper 5</option>

                <option value="microsip">MicroSIP</option>
            </select> --}}

            <p id="softphoneStatus" class="text-sm mt-2"></p>

            <div class="flex justify-center mt-4">


                {{-- <button id="registerPhone" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Register
                </button> --}}



                @if ($softphone == 'microsip')

                    <button id="registerPhone" class="px-4 py-4 bg-transparent text-white rounded">
                        <img src="/images/CallStartMicroSIP2.png" class="w-48 h-48" alt="Phone">
                    </button>
                @elseif ($softphone == 'zoiper')

                    <button id="registerPhone" class="px-4 py-4 bg-transparent text-white rounded">
                        <img src="/images/CallStartZoiper2.png" class="w-48 h-48" alt="Phone">
                    </button>
                @endif

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const modal = document.getElementById("softphoneModal");
            const closeModal = document.getElementById("closeModal");
            const registerBtn = document.getElementById("registerPhone");
            const statusBox = document.getElementById("softphoneStatus");

            // 1️⃣ CHECK REDIS STATUS
            fetch("/redis/check")
                .then(res => res.json())
                .then(data => {
                    if (!data.registered) {
                        modal.classList.remove("hidden");
                        modal.classList.add("flex");
                    }
                });

            // 2️⃣ CLOSE MODAL
            closeModal.addEventListener("click", () => {
                modal.classList.add("hidden");
            });

            // 3️⃣ REGISTER PHONE
            registerBtn.addEventListener("click", () => {

                const button = this;
                // button.disabled = true;
                // button.innerHTML = 'Processing...';
                modal.classList.add("hidden");

                const exten = document.getElementById("exten").value;
                const extenType = document.getElementById("exten_type").value;
                const softphone = document.getElementById("softphone").value;

                let url = softphone === "microsip"
                    ? "http://127.0.0.1:5001/update/microsip"
                    : "http://127.0.0.1:5001/update/zoiper5";

                let data = {
                    exten: exten,
                    server: "123.231.74.22",
                    password: softphone === "microsip"
                        ? "@u5051p"
                        : "vCkSoFyUNjxbVy7bm6TJdA==\n",
                    aa: "1",
                    autoanswerdelay: "3",
                    protocol: extenType
                };

                fetch(url, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                })
                    .then(r => {
                        if (!r.ok) throw new Error("Softphone update failed");
                        return r.json().catch(() => ({})); // ignore body
                    })
                    .then(() => {

                        statusBox.innerHTML = "Softphone registered!";
                        statusBox.classList.add("text-green-600");

                        // 4️⃣ UPDATE REDIS
                        return fetch("/redis/set", {
                            method: "POST",
                            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        });
                    })
                    .then(() => {
                        setTimeout(() => modal.classList.add("hidden"), 800);
                    })
                    .catch(err => {
                        statusBox.innerHTML = "Error: " + err;
                        statusBox.classList.add("text-red-600");
                    });

            });

        });
    </script>
    @endif

</x-app-layout>