<x-filament::page>
    <div class="mb-4">
        <form method="GET" class="flex flex-wrap gap-4 items-center bg-white p-4 rounded shadow">
            <div class="flex flex-col">
                <label for="mode" class="font-medium text-gray-700 text-sm mb-1">Mode:</label>
                <select name="mode" id="mode" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black text-sm" onchange="this.form.submit()">
                    <option value="inbound" {{ $mode == 'inbound' ? 'selected' : '' }}>Inbound</option>
                    <option value="outbound" {{ $mode == 'outbound' ? 'selected' : '' }}>Outbound</option>
                </select>
            </div>
            
            @if($mode === 'outbound')
            <div class="flex flex-col">
                <label for="campaignId" class="font-medium text-gray-700 text-sm mb-1">Campaign:</label>
                <select name="campaignId" id="campaignId" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black text-sm" onchange="this.form.submit()">
                    <option value="">All Campaigns</option>
                    @foreach($campaigns as $id => $name)
                        <option value="{{ $id }}" {{ $campaignId == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex flex-col">
                <label for="startDate" class="font-medium text-gray-700 text-sm mb-1">Start Date:</label>
                <input type="date" name="startDate" id="startDate" value="{{ $startDate }}" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black text-sm" onchange="this.form.submit()">
            </div>

            <div class="flex flex-col">
                <label for="endDate" class="font-medium text-gray-700 text-sm mb-1">End Date:</label>
                <input type="date" name="endDate" id="endDate" value="{{ $endDate }}" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black text-sm" onchange="this.form.submit()">
            </div>
            
            <!-- <div class="flex items-end pb-0.5">
                 <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 text-sm">Filter</button>
            </div> -->
        </form>
    </div>

    @if($agents->isEmpty())
        <div class="p-4 bg-white rounded shadow text-gray-500">
            No agents found for your company.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($agents as $agent)
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800">{{ $agent->username }}</h3>
                        <span class="text-sm bg-gray-100 px-2 py-1 rounded text-gray-600">Ext: {{ $agent->extension }}</span>
                    </div>
                    
                    <div class="h-64">
                        @if($mode === 'inbound')
                            {{-- Inbound Chart --}}
                            @livewire(\App\Filament\Widgets\AgentInboundPieChart::class, ['extension' => $agent->extension, 'startDate' => $startDate, 'endDate' => $endDate], key('inbound-'.$agent->id))
                        @else
                            {{-- Outbound Chart (Pass Agent ID primarily, extension fallback) --}}
                            @livewire(\App\Filament\Widgets\AgentOutboundPieChart::class, ['agentId' => $agent->id, 'startDate' => $startDate, 'endDate' => $endDate, 'campaignId' => $campaignId], key('outbound-'.$agent->id))
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament::page>
