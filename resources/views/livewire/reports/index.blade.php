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
                {{-- <a href="{{ route('reports.cdr-listen-calls') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12c1.93 0 3.5-1.57 3.5-3.5S10.93 5 9 5 5.5 6.57 5.5 8.5 7.07 12 9 12zm0-5c.83 0 1.5.67 1.5 1.5S9.83 10 9 10s-1.5-.67-1.5-1.5S8.17 7 9 7zm.05 10H4.77c.99-.5 2.7-1 4.23-1 .11 0 .23.01.34.01.34-.73.93-1.33 1.64-1.81-.73-.13-1.42-.2-1.98-.2-2.34 0-7 1.17-7 3.5V19h7v-1.5c0-.17.02-.34.05-.5zm7.45-2.5c-1.84 0-5.5 1.01-5.5 3V19h11v-1.5c0-1.99-3.66-3-5.5-3zm1.21-1.82c.76-.43 1.29-1.24 1.29-2.18C19 9.12 17.88 8 16.5 8S14 9.12 14 10.5c0 .94.53 1.75 1.29 2.18.36.2.77.32 1.21.32s.85-.12 1.21-.32z"></path></svg>
                    <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#5f6368">
                        <path d="M4 12H7C8.10457 12 9 12.8954 9 14V19C9 20.1046 8.10457 21 7 21H4C2.89543 21 2 20.1046 2 19V12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12V19C22 20.1046 21.1046 21 20 21H17C15.8954 21 15 20.1046 15 19V14C15 12.8954 15.8954 12 17 12H20C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12Z"> </path>
                    </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Call Supervised Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Call Supervised Report
                        </div>
                    </div>
                </a> --}}
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
                @can('can-view-cdr-reports')
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

                 @endcan
                @can('can-view-reports')

                <a href="{{ route('reports.daily-calls-summary-report') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        
                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M6.54 5c.06.89.21 1.76.45 2.59l-1.2 1.2c-.41-1.2-.67-2.47-.76-3.79h1.51m9.86 12.02c.85.24 1.72.39 2.6.45v1.49c-1.32-.09-2.59-.35-3.8-.75l1.2-1.19M7.5 3H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.49c0-.55-.45-1-1-1-1.24 0-2.45-.2-3.57-.57-.1-.04-.21-.05-.31-.05-.26 0-.51.1-.71.29l-2.2 2.2c-2.83-1.45-5.15-3.76-6.59-6.59l2.2-2.2c.28-.28.36-.67.25-1.02C8.7 6.45 8.5 5.25 8.5 4c0-.55-.45-1-1-1z"></path></svg>

                    <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#5f6368">
                        <path d="M4 12H7C8.10457 12 9 12.8954 9 14V19C9 20.1046 8.10457 21 7 21H4C2.89543 21 2 20.1046 2 19V12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12V19C22 20.1046 21.1046 21 20 21H17C15.8954 21 15 20.1046 15 19V14C15 12.8954 15.8954 12 17 12H20C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12Z"> </path>
                    </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Daily Call Summary Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Daily Call Summary Report
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.daily-queue-summary-report') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor"><path d="M32,64a8,8,0,0,1,8-8H216a8,8,0,0,1,0,16H40A8,8,0,0,1,32,64Zm104,56H40a8,8,0,0,0,0,16h96a8,8,0,0,0,0-16Zm0,64H40a8,8,0,0,0,0,16h96a8,8,0,0,0,0-16Zm112-24a8,8,0,0,1-3.76,6.78l-64,40A8,8,0,0,1,168,200V120a8,8,0,0,1,12.24-6.78l64,40A8,8,0,0,1,248,160Zm-23.09,0L184,134.43v51.14Z"></path></svg>
                    <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#5f6368">
                        <path d="M4 12H7C8.10457 12 9 12.8954 9 14V19C9 20.1046 8.10457 21 7 21H4C2.89543 21 2 20.1046 2 19V12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12V19C22 20.1046 21.1046 21 20 21H17C15.8954 21 15 20.1046 15 19V14C15 12.8954 15.8954 12 17 12H20C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12Z"> </path>
                    </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Daily Queue Summary Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Daily Queue Summary Report
                        </div>
                    </div>
                </a>


                <a href="{{ route('reports.agent-login-logout-report') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        
                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill="none" stroke="currentColor" stroke-width="2" d="M9,15 L9,22 L22,22 L22,2 L9,2 L9,9 M18,12 L0,12 M13,7 L18,12 L13,17"></path></svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Agent Login Logout Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Agent Login Logout Report
                        </div>
                    </div>
                </a>


                <a href="{{ route('reports.nuisance-customers-report') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        
                        <svg class="w-8 h-8 text-gray-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" id="mdi-badge-account-alert-outline" viewBox="0 0 24 24"><path d="M15,3H12V5H15V21H5V5H8V3H5A2,2 0 0,0 3,5V21A2,2 0 0,0 5,23H15A2,2 0 0,0 17,21V5A2,2 0 0,0 15,3M10,7A2,2 0 0,1 12,9A2,2 0 0,1 10,11A2,2 0 0,1 8,9A2,2 0 0,1 10,7M14,15H6V14C6,12.67 8.67,12 10,12C11.33,12 14,12.67 14,14V15M14,18H6V17H14V18M10,20H6V19H10V20M11,5H9V1H11V5M19,13V7H21V13H19M19,17V15H21V17H19Z"></path></svg>                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Nuisance Customers Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Nuisance Customers Report
                        </div>
                    </div>
                </a>


                <a href="{{ route('reports.unsatisfied-customers-report') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        
<svg class="w-8 h-8  text-gray-600" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 36 36" preserveAspectRatio="xMidYMid meet" fill="currentColor"><title>id-badge-outline-alerted</title><path d="M18,22a4.23,4.23,0,1,0-4.23-4.23A4.23,4.23,0,0,0,18,22Zm0-6.86a2.63,2.63,0,1,1-2.63,2.63A2.63,2.63,0,0,1,18,15.14Z" class="clr-i-outline--alerted clr-i-outline-path-1--alerted"></path><path d="M10.26,27a1.13,1.13,0,0,0-.26.73V30h1.6V27.87A8.33,8.33,0,0,1,18,25.29a8.33,8.33,0,0,1,6.4,2.59V30H26V27.7a1.12,1.12,0,0,0-.26-.73A9.9,9.9,0,0,0,18,23.69,9.9,9.9,0,0,0,10.26,27Z" class="clr-i-outline--alerted clr-i-outline-path-2--alerted"></path><path d="M19,9.89,19.56,9H16V4h4V8.24l2-3.46V4a2,2,0,0,0-2-2H16a2,2,0,0,0-2,2v7h4.64A3.66,3.66,0,0,1,19,9.89Z" class="clr-i-outline--alerted clr-i-outline-path-3--alerted"></path><path d="M28,15.4V32H8V8h4V6H8A2,2,0,0,0,6,8V32a2,2,0,0,0,2,2H28a2,2,0,0,0,2-2V15.4Z" class="clr-i-outline--alerted clr-i-outline-path-4--alerted"></path><path d="M26.85,1.14,21.13,11A1.28,1.28,0,0,0,22.23,13H33.68A1.28,1.28,0,0,0,34.78,11L29.06,1.14A1.28,1.28,0,0,0,26.85,1.14Z" class="clr-i-outline--alerted clr-i-outline-path-5--alerted clr-i-alert"></path><rect x="0" y="0" width="36" height="36" fill-opacity="0"></rect></svg>                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Unsatisfied Customers Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Unsatisfied Customers Report
                        </div>
                    </div>
                </a>


                <a href="{{ route('reports.agent-performance-report') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        
<svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M15,22.00676H5v-2H15ZM22,4,17.265,9.955l-.22.27-5.63,7.19a2.00112,2.00112,0,1,1-2.83-2.83ZM2.6449,7.2345A10.84274,10.84274,0,0,0,1.19055,15H2V14A9.685,9.685,0,0,1,3.69037,8.48041ZM12,2A10.95848,10.95848,0,0,0,3.88129,5.5965L5.025,6.95953A7.42814,7.42814,0,0,1,10,5a7.43245,7.43245,0,0,1,4.99689,1.97791l3.5509-2.802A10.93584,10.93584,0,0,0,12,2Zm6.83008,9.19971-.2334.28759-.72791.92963A10.11814,10.11814,0,0,1,18,14v1h4.80945a10.879,10.879,0,0,0-1.18237-7.31775Z"></path></svg></div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Call Center Performance Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Call Center Performance Report
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.agent-performance-metrics-report') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none"><path d="M16.75 3C16.9489 3 17.1397 3.07902 17.2803 3.21967C17.421 3.36032 17.5 3.55109 17.5 3.75V8.25C17.5 8.66421 17.1642 9 16.75 9C16.3358 9 16 8.66421 16 8.25V5.45035L11.5056 9.55388C11.2094 9.82424 10.7531 9.81387 10.4695 9.53034L8.99987 8.06068L4.28034 12.7803C3.98745 13.0732 3.51257 13.0732 3.21968 12.7803C2.92678 12.4874 2.92677 12.0126 3.21966 11.7197L8.46953 6.46969C8.61018 6.32903 8.80094 6.25001 8.99986 6.25001C9.19877 6.25001 9.38954 6.32903 9.53019 6.46968L11.0234 7.96292L14.8162 4.50001L12.2499 4.50001C11.8357 4.50002 11.4999 4.16423 11.4999 3.75002C11.4999 3.3358 11.8356 3.00002 12.2499 3.00001L16.75 3ZM19 14.5C19 16.9853 16.9853 19 14.5 19C12.0147 19 10 16.9853 10 14.5C10 12.0147 12.0147 10 14.5 10C16.9853 10 19 12.0147 19 14.5ZM17.3796 12.6746C17.1999 12.4649 16.8843 12.4407 16.6746 12.6204L13.5262 15.319L12.3536 14.1464C12.1583 13.9512 11.8417 13.9512 11.6464 14.1464C11.4512 14.3417 11.4512 14.6583 11.6464 14.8536L13.1464 16.3536C13.3312 16.5383 13.627 16.5497 13.8254 16.3796L17.3254 13.3796C17.5351 13.1999 17.5593 12.8843 17.3796 12.6746Z" fill="currentColor"></path></svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Agent Performance Metrics</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Daily agent performance metrics report
                        </div>
                    </div>
                </a>
                @endcan

                @can('can-view-dialer-reports')
                <a href="{{ route('reports.dialer-contact-attempt-report') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        
<svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill="currentColor" d="M19 17v4c-2.758 0-5.07-.495-7-1.325-3.841-1.652-6.176-4.63-7.5-7.675C3.4 9.472 3 6.898 3 5h4l1 4-3.5 3c1.324 3.045 3.659 6.023 7.5 7.675L15 16l4 1z"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19.675c1.93.83 4.242 1.325 7 1.325v-4l-4-1-3 3.675zm0 0C8.159 18.023 5.824 15.045 4.5 12m0 0C3.4 9.472 3 6.898 3 5h4l1 4-3.5 3zM14 4h.01M17 4h.01M20 4h.01M14 7h.01M17 7h.01M20 7h.01M14 10h.01M17 10h.01M20 10h.01"></path></svg></div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Dialer call Status Report</div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Dialer call Status Report
                        </div>
                    </div>
                </a>
                @endcan



            </div>
        </div>
        
    </div>
</div>