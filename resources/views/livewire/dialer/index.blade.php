<div>
    <x-slot name="header">
        <div class="">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Dialer ')  }}
            </h2>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8  space-y-4">
            <div class="bg-white ">
            <div class="px-4 py-2 font-semibold text-sm text-gray-700">Settings</div>
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
                                Feed Management
                            </div>
                            <div
                                class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                                All Feed Management Settings
                            </div>
                        </div>


                    </div>
                </a>
            </div>
            </div>


            
                <div class="bg-white ">
            <div class="px-4 py-2 font-semibold text-sm text-gray-700">Dashboard</div>
            <hr>
            <div class="bg-white rounded-md p-4 grid grid-cols-3 gap-3">
                <a href="{{ route('dialer.dashboard.index') }}">
                <div class="flex hover:cursor-pointer">
                    
                    <div class="p-2 bg-gray-100 rounded-md max-h-12">
                        <svg class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor">
                            <defs></defs>
                            <title>dashboard</title>
                            <rect x="24" y="21" width="2" height="5"></rect>
                            <rect x="20" y="16" width="2" height="10"></rect>
                            <path d="M11,26a5.0059,5.0059,0,0,1-5-5H8a3,3,0,1,0,3-3V16a5,5,0,0,1,0,10Z"></path>
                            <path
                                d="M28,2H4A2.002,2.002,0,0,0,2,4V28a2.0023,2.0023,0,0,0,2,2H28a2.0027,2.0027,0,0,0,2-2V4A2.0023,2.0023,0,0,0,28,2Zm0,9H14V4H28ZM12,4v7H4V4ZM4,28V13H28.0007l.0013,15Z">
                            </path>
                            <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1 text-gray-500"
                                width="32" height="32" style="fill:none"></rect>
                        </svg>
                    </div>
                    
                    
                    <div class="pl-1">
                        <div class="text-lg font-semibold text-gray-700">
                            Dashboard
                        </div>
                        <div
                            class="text-sm text-gray-600 font-thin text-ellipsis whitespace-nowrap overflow-hidden w-60 hover:w-full hover:whitespace-normal transition transform">
                            Dialer Dashboard
                        </div>
                    </div>
                    

                </div>
                </a>
            </div>
            </div>
            </div>
        </div>
        
            </div>
    </div>

</div>