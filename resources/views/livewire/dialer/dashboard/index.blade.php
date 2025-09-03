<div>
    <x-slot name="header">
        <div class="">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Agent Call Dashboard')  }}
            </h2>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Four Cards -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Row 1 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4">
                    <!-- Card 1: Total Assigned Campaigns -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-700">Total Assigned Campaigns</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalAssignedCampaigns }}</p>
                    </div>
                    <!-- Card 2: Active Assigned Campaigns -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-700">Active Assigned Campaigns</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $activeAssignedCampaigns }}</p>
                    </div>
                    <!-- Card 3: Total Calls Today -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-700">Total Calls Today</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $totalCallsToday }}</p>
                    </div>
                    <!-- Card 4: Answered Calls Today -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-700">Answered Calls Today</h3>
                        <p class="text-3xl font-bold text-teal-600">{{ $answeredCallsToday }}</p>
                    </div>
                </div>
                <!-- Row 2: Campaign Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($campaigns as $campaign)
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-base font-semibold text-gray-700">{{ $campaign->name }}</h3>
                            <div class="mt-2 space-y-1">
                                <div>
                                    <span class="text-sm font-bold text-gray-700">Type:</span>
                                    <span class="text-sm">{{ $campaign->types?->name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-gray-700">Status:</span>
                                    <span class="text-sm">{{ $campaign->status }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-gray-700">Total Numbers:</span>
                                    <span class="text-sm">{{ $campaign->contact_count ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-gray-700">Dialed Count:</span>
                                    <span class="text-sm">{{ $campaign->dialed_count ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-gray-700">Answered Count:</span>
                                    <span class="text-sm">{{ $campaign->answered_count ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-gray-700">Agents Count:</span>
                                    <span class="text-sm">{{ $campaign->agents_count ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Right Side: Agents Panel -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Agents</h3>
                <ul class="space-y-3">
                    @forelse ($agents as $agent)
                        <li class="flex flex-row space-x-3">
                            <span class="w-10 h-10 text-white rounded-full flex items-center justify-center">
                                {{-- {{ strtoupper(substr($agent->name, 0, 1)) }} --}}

                                <svg class="mx-auto object-cover rounded-full h-10 w-10" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 256 256"
                                    xml:space="preserve">

                                    <defs>
                                    </defs>
                                    <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;"
                                        transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                                        <path
                                            d="M 64.643 15.053 C 62.216 6.384 54.264 0 44.831 0 S 27.445 6.384 25.019 15.053 c -1.695 0.217 -3.022 1.659 -3.022 3.411 v 9.872 c 0 1.9 1.555 3.455 3.455 3.455 s 3.455 -1.555 3.455 -3.455 v -9.872 c 0 -1.189 -0.609 -2.243 -1.531 -2.865 c 2.176 -7.596 9.169 -13.177 17.454 -13.177 c 8.286 0 15.279 5.581 17.454 13.177 c -0.921 0.622 -1.53 1.676 -1.53 2.865 v 9.872 c 0 1.307 0.744 2.437 1.821 3.023 c -0.698 3.214 -2.242 6.114 -4.396 8.453 c -0.148 -0.029 -0.299 -0.046 -0.455 -0.046 c -1.32 0 -2.399 1.08 -2.399 2.399 s 1.08 2.399 2.399 2.399 s 2.399 -1.08 2.399 -2.399 c 0 -0.259 -0.052 -0.504 -0.129 -0.737 c 2.467 -2.688 4.225 -6.031 4.991 -9.733 c 1.528 -0.356 2.679 -1.726 2.679 -3.359 v -9.872 C 67.665 16.712 66.339 15.27 64.643 15.053 z"
                                            style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(240,88,47); fill-rule: nonzero; opacity: 1;"
                                            transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                        <path
                                            d="M 55.893 35.514 c 0.655 -0.905 1.196 -1.885 1.611 -2.917 c -0.802 -1.252 -1.249 -2.724 -1.249 -4.26 v -9.872 c 0 -1.313 0.323 -2.582 0.918 -3.708 c -2.231 -4.703 -7.034 -7.834 -12.342 -7.834 s -10.11 3.131 -12.342 7.835 c 0.595 1.126 0.918 2.395 0.918 3.708 v 9.872 c 0 2.391 -1.065 4.533 -2.74 5.993 c 2.703 5.183 8.111 8.754 14.333 8.754 c 2.059 0 4.028 -0.395 5.842 -1.105 C 50.927 38.892 53.034 36.303 55.893 35.514 z"
                                            style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(110,177,225); fill-rule: nonzero; opacity: 1;"
                                            transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                        <path
                                            d="M 61.749 47.762 c -1.134 0.818 -2.523 1.303 -4.024 1.303 c -2.299 0 -4.334 -1.135 -5.588 -2.87 c -2.219 0.862 -4.622 1.345 -7.136 1.345 c -3.281 0 -6.375 -0.816 -9.113 -2.234 c -12.235 1.826 -21.7 12.444 -21.7 25.167 v 16.741 c 0 1.532 1.253 2.786 2.786 2.786 h 56.054 c 1.532 0 2.786 -1.254 2.786 -2.786 V 70.474 C 75.813 60.568 70.064 51.96 61.749 47.762 z"
                                            style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(110,177,225); fill-rule: nonzero; opacity: 1;"
                                            transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                    </g>
                                </svg>
                            </span>


                            <span class="text-gray-600">{{ $agent->name }}</span>
                        </li>
                    @empty
                        <li class="text-gray-600">No agents found</li>
                    @endforelse
                </ul>
            </div>
        </div>





               
            {{-- </div> --}}
        </div>
    </div>

</div>