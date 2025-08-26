<div>
    <x-slot name="header">
        <div class="">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Dialer ')  }}
            </h2>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-md p-4 grid grid-cols-3 gap-3">
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
    </div>

</div>