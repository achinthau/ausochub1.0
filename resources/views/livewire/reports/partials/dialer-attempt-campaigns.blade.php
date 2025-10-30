<div class="mb-4">
    <select wire:model="selectedCampaign" class="form-controm rounded-md">
        <option value="0">All Campaigns</option>
        @foreach($campaigns as $campaign)
        <option value="{{$campaign->id}}">{{ $campaign->name }}</option>
        @endforeach
    </select>
</div>
