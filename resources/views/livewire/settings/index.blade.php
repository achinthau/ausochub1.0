<div>
    <div class="max-w-7xl mx-auto py-10 px-6 space-y-4">
        <div class="bg-white ">
            <div class="px-4 py-2 font-semibold text-sm text-gray-700">Administration</div>
            <hr>
            <div class="bg-white rounded-md p-4 grid grid-cols-3 gap-3">
                <a href="{{ route('settings.users.index') }}"
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
                        <div class="text-lg font-semibold text-gray-700">Users Management </div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            All user management opeations.
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="bg-white ">
            <div class="px-4 py-2 font-semibold text-sm text-gray-700">Call Server Configurations</div>
            <hr>
            <div class="bg-white rounded-md p-4 grid grid-cols-3 gap-3">
                <a href="{{ route('settings.extensions.index') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M5 4a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM5 9a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM5 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5-6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Extensions </div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Manage and assign extensions.
                        </div>
                    </div>
                </a>
                <a href="{{ route('settings.moh.index') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M10.147 2.02211C9.99539 1.97545 9.83058 2.00354 9.70295 2.09781C9.57531 2.19208 9.5 2.34133 9.5 2.5V12.4048C8.91605 11.8444 8.12325 11.5 7.25 11.5C5.45507 11.5 4 12.9551 4 14.75C4 16.5449 5.45507 18 7.25 18C9.04493 18 10.5 16.5449 10.5 14.75C10.5 14.6897 10.4984 14.6297 10.4951 14.5702C10.4983 14.5473 10.5 14.5238 10.5 14.5V7.17698L16.353 8.97789C16.5046 9.02456 16.6694 8.99647 16.7971 8.9022C16.9247 8.80793 17 8.65868 17 8.5V5.97715C17 4.87964 16.2842 3.91047 15.2352 3.58771L10.147 2.02211Z"
                                fill="currentColor"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">MOH </div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Manage music on hold.
                        </div>
                    </div>
                </a>
                <a href="{{ route('settings.skills.index') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        

                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"
                            fill="currentColor">
                            <defs></defs>
                            <rect x="26" y="2" width="4" height="4"></rect>
                            <rect x="26" y="8" width="4" height="4"></rect>
                            <rect x="20" y="2" width="4" height="4"></rect>
                            <rect x="20" y="8" width="4" height="4"></rect>
                            <path
                                d="M25,30h-.17C5.18,28.87,2.39,12.29,2,7.23A3,3,0,0,1,4.7611,4.0088Q4.88,4,5,4h5.27a2,2,0,0,1,1.86,1.26L13.65,9a2,2,0,0,1-.44,2.16l-2.13,2.15a9.36,9.36,0,0,0,7.58,7.6l2.17-2.15A2,2,0,0,1,23,18.35l3.77,1.51A2,2,0,0,1,28,21.72V27A3,3,0,0,1,25,30ZM5,6a1,1,0,0,0-1.0032.9968c0,.0278.001.0555.0032.0832C4.46,13,7.41,27,24.94,28a1,1,0,0,0,1.0581-.9382Q26,27.0309,26,27V21.72l-3.77-1.51-2.87,2.85L18.88,23C10.18,21.91,9,13.21,9,13.12l-.06-.48,2.84-2.87L10.28,6Z"
                                transform="translate(0 0)"></path>
                            <rect id="_Transparent_Rectangle_" data-name="<Transparent Rectangle>" class="cls-1"
                                width="32" height="32" style="fill: none"></rect>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Skills </div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Call skills management.
                        </div>
                    </div>
                </a>


                <a href="{{ route('settings.tickets.departments.index') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        

                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"
                            fill="currentColor">
                            <defs></defs>
                            <rect x="26" y="2" width="4" height="4"></rect>
                            <rect x="26" y="8" width="4" height="4"></rect>
                            <rect x="20" y="2" width="4" height="4"></rect>
                            <rect x="20" y="8" width="4" height="4"></rect>
                            <path
                                d="M25,30h-.17C5.18,28.87,2.39,12.29,2,7.23A3,3,0,0,1,4.7611,4.0088Q4.88,4,5,4h5.27a2,2,0,0,1,1.86,1.26L13.65,9a2,2,0,0,1-.44,2.16l-2.13,2.15a9.36,9.36,0,0,0,7.58,7.6l2.17-2.15A2,2,0,0,1,23,18.35l3.77,1.51A2,2,0,0,1,28,21.72V27A3,3,0,0,1,25,30ZM5,6a1,1,0,0,0-1.0032.9968c0,.0278.001.0555.0032.0832C4.46,13,7.41,27,24.94,28a1,1,0,0,0,1.0581-.9382Q26,27.0309,26,27V21.72l-3.77-1.51-2.87,2.85L18.88,23C10.18,21.91,9,13.21,9,13.12l-.06-.48,2.84-2.87L10.28,6Z"
                                transform="translate(0 0)"></path>
                            <rect id="_Transparent_Rectangle_" data-name="<Transparent Rectangle>" class="cls-1"
                                width="32" height="32" style="fill: none"></rect>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Departments </div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Departments management.
                        </div>
                    </div>
                </a>
                <a href="{{ route('settings.tickets.serv-center.index') }}"
                    class="flex p-2 space-x-2 transform transition duration-500 hover:scale-105 hover:bg-[#5E81F4]/[.1]  hover:text-[#5E81F4] rounded-md ">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        

                        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"
                            fill="currentColor">
                            <defs></defs>
                            <rect x="26" y="2" width="4" height="4"></rect>
                            <rect x="26" y="8" width="4" height="4"></rect>
                            <rect x="20" y="2" width="4" height="4"></rect>
                            <rect x="20" y="8" width="4" height="4"></rect>
                            <path
                                d="M25,30h-.17C5.18,28.87,2.39,12.29,2,7.23A3,3,0,0,1,4.7611,4.0088Q4.88,4,5,4h5.27a2,2,0,0,1,1.86,1.26L13.65,9a2,2,0,0,1-.44,2.16l-2.13,2.15a9.36,9.36,0,0,0,7.58,7.6l2.17-2.15A2,2,0,0,1,23,18.35l3.77,1.51A2,2,0,0,1,28,21.72V27A3,3,0,0,1,25,30ZM5,6a1,1,0,0,0-1.0032.9968c0,.0278.001.0555.0032.0832C4.46,13,7.41,27,24.94,28a1,1,0,0,0,1.0581-.9382Q26,27.0309,26,27V21.72l-3.77-1.51-2.87,2.85L18.88,23C10.18,21.91,9,13.21,9,13.12l-.06-.48,2.84-2.87L10.28,6Z"
                                transform="translate(0 0)"></path>
                            <rect id="_Transparent_Rectangle_" data-name="<Transparent Rectangle>" class="cls-1"
                                width="32" height="32" style="fill: none"></rect>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-700">Service Centers </div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Service Centers management.
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="bg-white ">
            <div class="px-4 py-2 font-semibold text-sm text-gray-700">Dialer Settings</div>
            <hr>
            <div class="bg-white rounded-md p-4 grid grid-cols-3 gap-3">
                {{-- <a href="{{ route('dialer.settings.index') }}">
                <div class="flex hover:cursor-pointer">
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        <svg class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            fill="currentColor">
                            <path
                                d="M256,176a80,80,0,1,0,80,80A80.24,80.24,0,0,0,256,176Zm172.72,80a165.53,165.53,0,0,1-1.64,22.34l48.69,38.12a11.59,11.59,0,0,1,2.63,14.78l-46.06,79.52a11.64,11.64,0,0,1-14.14,4.93l-57.25-23a176.56,176.56,0,0,1-38.82,22.67l-8.56,60.78A11.93,11.93,0,0,1,302.06,486H209.94a12,12,0,0,1-11.51-9.53l-8.56-60.78A169.3,169.3,0,0,1,151.05,393L93.8,416a11.64,11.64,0,0,1-14.14-4.92L33.6,331.57a11.59,11.59,0,0,1,2.63-14.78l48.69-38.12A174.58,174.58,0,0,1,83.28,256a165.53,165.53,0,0,1,1.64-22.34L36.23,195.54a11.59,11.59,0,0,1-2.63-14.78l46.06-79.52A11.64,11.64,0,0,1,93.8,96.31l57.25,23a176.56,176.56,0,0,1,38.82-22.67l8.56-60.78A11.93,11.93,0,0,1,209.94,26h92.12a12,12,0,0,1,11.51,9.53l8.56,60.78A169.3,169.3,0,0,1,361,119L418.2,96a11.64,11.64,0,0,1,14.14,4.92l46.06,79.52a11.59,11.59,0,0,1-2.63,14.78l-48.69,38.12A174.58,174.58,0,0,1,428.72,256Z">
                            </path>
                        </svg>
                    </div>
                    <div class="pl-1">
                        <div class="text-lg font-semibold text-gray-700">
                            Settings
                        </div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            All Dialer Settings
                        </div>
                    </div>

                </div>
                </a> --}}

                <a href="{{ route('dialer.settings.camp.index') }}">
                    <div class="flex hover:cursor-pointer">
                        <div class="p-2 bg-gray-100 rounded-md max-h-12">
                            <svg class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" role="img"
                                viewBox="0 0 24 24" fill="currentColor">
                                <title>Campaign Monitor</title>
                                <path
                                    d="M23.836 4.27c-.29-.413-.86-.515-1.273-.226L.163 19.73c.167.235.437.39.747.39h22.18c.503 0 .91-.41.91-.914V4.78c-.004-.176-.058-.352-.164-.51zm-22.4-.226c-.413-.29-.982-.19-1.272.226-.107.154-.162.332-.164.51v14.45l10.664-8.736-9.227-6.45v-.002z">
                                </path>
                            </svg>
                        </div>
                        <div class="pl-1">
                            <div class="text-lg font-semibold text-gray-700">
                                Campaign Management
                            </div>
                            <div
                                class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                                All Campaign Settings
                            </div>
                        </div>

                    </div>
                </a>

                <a href="{{ route('dialer.settings.feed.index') }}">
                    <div class="flex hover:cursor-pointer">

                        <div class="p-2 bg-gray-100 rounded-md max-h-12">
                            <svg class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path d="M8,8H6v7c0,1.1,0.9,2,2,2h9v-2H8V8z"></path>
                                <path
                                    d="M20,3h-8c-1.1,0-2,0.9-2,2v6c0,1.1,0.9,2,2,2h8c1.1,0,2-0.9,2-2V5C22,3.9,21.1,3,20,3z M20,11h-8V7h8V11z">
                                </path>
                                <path d="M4,12H2v7c0,1.1,0.9,2,2,2h9v-2H4V12z"></path>
                            </svg>
                        </div>


                        <div class="pl-1">
                            <div class="text-lg font-semibold text-gray-700">
                                Lead Management
                            </div>
                            <div
                                class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                                All Lead Management Settings
                            </div>
                        </div>


                    </div>
                </a>
            </div>
            </div>

    </div>
</div>
