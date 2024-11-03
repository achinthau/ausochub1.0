<div>
    <div class="max-w-7xl mx-auto py-10 px-6 space-y-4">
        <div class="bg-white ">
            <div class="px-4 py-2 font-semibold text-lg text-gray-700">Reports</div>
            <hr>
            <div class="bg-white rounded-md p-4 grid grid-cols-3 gap-3">
                @can('is-admin')
            <a href="{{ route('reports.abandoned-call') }}"
                class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                <div class="p-2 bg-gray-100 rounded-md max-h-12">
                    <svg class="w-8 h-8 text-gray-600" name="user" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 1024 1024" fill="currentColor">
                        <path
                            d="M511.728 64c108.672 0 223.92 91.534 223.92 159.854v159.92c0 61.552-25.6 179.312-94.256 233.376a63.99 63.99 0 0 0-23.968 57.809c2.624 22.16 16.592 41.312 36.848 50.625l278.496 132.064c2.176.992 26.688 5.104 26.688 39.344l.032 62.464L64 959.504V894.56c0-25.44 19.088-33.425 26.72-36.945l281.023-132.624c20.16-9.248 34.065-28.32 36.769-50.32 2.72-22-6.16-43.84-23.456-57.712-66.48-53.376-97.456-170.704-97.456-233.185v-159.92C287.615 157.007 404.016 64 511.728 64zm0-64.002c-141.312 0-288.127 117.938-288.127 223.857v159.92c0 69.872 31.888 211.248 121.392 283.088l-281.04 132.64S.001 827.999.001 863.471v96.032c0 35.344 28.64 63.968 63.951 63.968h895.552c35.344 0 63.968-28.624 63.968-63.968v-96.032c0-37.6-63.968-63.968-63.968-63.968L681.008 667.439c88.656-69.776 118.656-206.849 118.656-283.665v-159.92c0-105.92-146.64-223.855-287.936-223.855z">
                        </path>
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-semibold text-gray-700">Abandoned Call </div>
                    <div
                        class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                        All abandoned call logs
                    </div>
                </div>
            </a>
            <a href="{{ route('reports.agent-break-summary-report') }}"
                class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                <div class="p-2 bg-gray-100 rounded-md max-h-12">
                    <svg class="w-8 h-8 text-gray-600" name="user" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 1024 1024" fill="currentColor">
                        <path
                            d="M511.728 64c108.672 0 223.92 91.534 223.92 159.854v159.92c0 61.552-25.6 179.312-94.256 233.376a63.99 63.99 0 0 0-23.968 57.809c2.624 22.16 16.592 41.312 36.848 50.625l278.496 132.064c2.176.992 26.688 5.104 26.688 39.344l.032 62.464L64 959.504V894.56c0-25.44 19.088-33.425 26.72-36.945l281.023-132.624c20.16-9.248 34.065-28.32 36.769-50.32 2.72-22-6.16-43.84-23.456-57.712-66.48-53.376-97.456-170.704-97.456-233.185v-159.92C287.615 157.007 404.016 64 511.728 64zm0-64.002c-141.312 0-288.127 117.938-288.127 223.857v159.92c0 69.872 31.888 211.248 121.392 283.088l-281.04 132.64S.001 827.999.001 863.471v96.032c0 35.344 28.64 63.968 63.951 63.968h895.552c35.344 0 63.968-28.624 63.968-63.968v-96.032c0-37.6-63.968-63.968-63.968-63.968L681.008 667.439c88.656-69.776 118.656-206.849 118.656-283.665v-159.92c0-105.92-146.64-223.855-287.936-223.855z">
                        </path>
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-semibold text-gray-700">Agent Break Summary </div>
                    <div
                        class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                        All agent break summary
                    </div>
                </div>
            </a>
            <a href="{{ route('reports.agent-call-summary-report') }}"
                class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                <div class="p-2 bg-gray-100 rounded-md max-h-12">
                    <svg class="w-8 h-8 text-gray-600" name="user" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 1024 1024" fill="currentColor">
                        <path
                            d="M511.728 64c108.672 0 223.92 91.534 223.92 159.854v159.92c0 61.552-25.6 179.312-94.256 233.376a63.99 63.99 0 0 0-23.968 57.809c2.624 22.16 16.592 41.312 36.848 50.625l278.496 132.064c2.176.992 26.688 5.104 26.688 39.344l.032 62.464L64 959.504V894.56c0-25.44 19.088-33.425 26.72-36.945l281.023-132.624c20.16-9.248 34.065-28.32 36.769-50.32 2.72-22-6.16-43.84-23.456-57.712-66.48-53.376-97.456-170.704-97.456-233.185v-159.92C287.615 157.007 404.016 64 511.728 64zm0-64.002c-141.312 0-288.127 117.938-288.127 223.857v159.92c0 69.872 31.888 211.248 121.392 283.088l-281.04 132.64S.001 827.999.001 863.471v96.032c0 35.344 28.64 63.968 63.951 63.968h895.552c35.344 0 63.968-28.624 63.968-63.968v-96.032c0-37.6-63.968-63.968-63.968-63.968L681.008 667.439c88.656-69.776 118.656-206.849 118.656-283.665v-159.92c0-105.92-146.64-223.855-287.936-223.855z">
                        </path>
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-semibold text-gray-700">Agent | Queue Summary </div>
                    <div
                        class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                        Agent wise queue report
                    </div>
                </div>
            </a>
            <a href="{{ route('reports.ivr-detail') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                    {{-- <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z"/>
                        <!-- <path d="M10 13.5a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-6a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm-2.5.5a.5.5 0 0 1-.5-.5v-4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5zm-3 0a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5z"/> -->
                    </svg> --}}
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 0 24 24" width="32px" fill="#5f6368"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 19c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM6 1c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12-8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-6 8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">IVR Detail Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            IVR details
                        </div>
                    </div>
                </a>
                <a href="{{ route('reports.agent-missed-call-summary') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                    {{-- <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M6,7.49a1,1,0,0,0,1-1V5.9L9.88,8.78a3,3,0,0,0,4.24,0l4.59-4.59a1,1,0,0,0,0-1.41,1,1,0,0,0-1.42,0L12.71,7.36a1,1,0,0,1-1.42,0L8.41,4.49H9a1,1,0,0,0,0-2H6a1,1,0,0,0-.92.61A1.09,1.09,0,0,0,5,3.49v3A1,1,0,0,0,6,7.49Zm15.94,7.36a16.27,16.27,0,0,0-19.88,0,2.69,2.69,0,0,0-1,2,2.66,2.66,0,0,0,.78,2.07L3.6,20.72A2.68,2.68,0,0,0,7.06,21l.47-.32a8.13,8.13,0,0,1,1-.55,1.85,1.85,0,0,0,1-2.3l-.09-.24a10.49,10.49,0,0,1,5.22,0l-.09.24a1.85,1.85,0,0,0,1,2.3,8.13,8.13,0,0,1,1,.55l.47.32a2.58,2.58,0,0,0,1.54.5,2.72,2.72,0,0,0,1.92-.79l1.81-1.82A2.66,2.66,0,0,0,23,16.83,2.69,2.69,0,0,0,21.94,14.85ZM20.8,17.49,19,19.3a.68.68,0,0,1-.86.1c-.19-.14-.38-.27-.59-.4a11.65,11.65,0,0,0-1.09-.61l.4-1.09a1,1,0,0,0-.6-1.28,12.42,12.42,0,0,0-8.5,0,1,1,0,0,0-.6,1.28l.4,1.1a9.8,9.8,0,0,0-1.1.6l-.58.4A.66.66,0,0,1,5,19.3L3.2,17.49A.67.67,0,0,1,3,17a.76.76,0,0,1,.28-.53,14.29,14.29,0,0,1,17.44,0A.76.76,0,0,1,21,17,.67.67,0,0,1,20.8,17.49Z"/>
                    </svg> --}}
                    <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#5f6368">
                        <path d="m136-144-92-90q-12-12-12-28t12-28q88-95 203-142.5T480-480q118 0 232.5 47.5T916-290q12 12 12 28t-12 28l-92 90q-11 11-25.5 12t-26.5-8l-116-88q-8-6-12-14t-4-18v-114q-38-12-78-19t-82-7q-42 0-82 7t-78 19v114q0 10-4 18t-12 14l-116 88q-12 9-26.5 8T136-144Zm104-202q-29 15-56 34.5T128-268l40 40 72-56v-62Zm480 2v60l72 56 40-38q-29-26-56-45t-56-33Zm-480-2Zm480 2ZM478-506 280-704v104h-80v-240h240v80H336l141 141 226-226 57 57-282 282Z"/>
                    </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Agent Missed Call Summary</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Agent missed call details
                        </div>
                    </div>
                </a>
                {{-- <a href="{{ route('reports.asterisk-event') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                    {{-- <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M6,7.49a1,1,0,0,0,1-1V5.9L9.88,8.78a3,3,0,0,0,4.24,0l4.59-4.59a1,1,0,0,0,0-1.41,1,1,0,0,0-1.42,0L12.71,7.36a1,1,0,0,1-1.42,0L8.41,4.49H9a1,1,0,0,0,0-2H6a1,1,0,0,0-.92.61A1.09,1.09,0,0,0,5,3.49v3A1,1,0,0,0,6,7.49Zm15.94,7.36a16.27,16.27,0,0,0-19.88,0,2.69,2.69,0,0,0-1,2,2.66,2.66,0,0,0,.78,2.07L3.6,20.72A2.68,2.68,0,0,0,7.06,21l.47-.32a8.13,8.13,0,0,1,1-.55,1.85,1.85,0,0,0,1-2.3l-.09-.24a10.49,10.49,0,0,1,5.22,0l-.09.24a1.85,1.85,0,0,0,1,2.3,8.13,8.13,0,0,1,1,.55l.47.32a2.58,2.58,0,0,0,1.54.5,2.72,2.72,0,0,0,1.92-.79l1.81-1.82A2.66,2.66,0,0,0,23,16.83,2.69,2.69,0,0,0,21.94,14.85ZM20.8,17.49,19,19.3a.68.68,0,0,1-.86.1c-.19-.14-.38-.27-.59-.4a11.65,11.65,0,0,0-1.09-.61l.4-1.09a1,1,0,0,0-.6-1.28,12.42,12.42,0,0,0-8.5,0,1,1,0,0,0-.6,1.28l.4,1.1a9.8,9.8,0,0,0-1.1.6l-.58.4A.66.66,0,0,1,5,19.3L3.2,17.49A.67.67,0,0,1,3,17a.76.76,0,0,1,.28-.53,14.29,14.29,0,0,1,17.44,0A.76.76,0,0,1,21,17,.67.67,0,0,1,20.8,17.49Z"/>
                    </svg>
                    <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#5f6368">
                        <path d="M280-400q17 0 28.5-11.5T320-440q0-17-11.5-28.5T280-480q-17 0-28.5 11.5T240-440q0 17 11.5 28.5T280-400Zm0-160q17 0 28.5-11.5T320-600q0-17-11.5-28.5T280-640q-17 0-28.5 11.5T240-600q0 17 11.5 28.5T280-560Zm80 160h360v-80H360v80Zm0-160h360v-80H360v80Zm-40 440v-80H160q-33 0-56.5-23.5T80-280v-480q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v480q0 33-23.5 56.5T800-200H640v80H320ZM160-280h640v-480H160v480Zm0 0v-480 480Z"/>
                    </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Live Call Dashboard</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Live Caller Details
                        </div>
                    </div>
                </a> --}}
                @endcan
                @can('can-view-reports')
                <a href="{{ route('reports.cdr-detail') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                    <svg class="w-8 h-8 text-gray-600 bi-file-earmark-bar-graph" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zM9.5 3v2a1 1 0 0 0 1 1h2l-3-3zm-3 9V8h-1v4h1zm2 0V7h-1v5h1zm2 0V9h-1v3h1z"/>
                    </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Call Detail Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Call details and recodings
                        </div>
                    </div>
                </a>
                @endif
            </div>
        </div>
        
    </div>
</div>