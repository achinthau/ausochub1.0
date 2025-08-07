<x-modal.card title="Service Ticket Rating Panel" blur align="center" wire:model="cxTicketRatingModal">

    <div>

        @if(!$isCancel)
        <div>
            <style>
                .emoji {
                    transition: transform 0.2s, filter 0.2s;
                    font-size: 3rem;
                    /* Adjust size if needed */
                }

                .emoji:hover {
                    transform: scale(1.2);
                }

                .bw {
                    filter: grayscale(100%);
                }

                .colorful {
                    filter: none;
                }
            </style>

            <div class="flex items-center pb-4">
                @foreach (['😥', '😟', '😐', '🙂', '😀'] as $index => $emoji)
                    <span
                        class="text-4xl ms-3 cursor-pointer emoji {{ $index + 1 == ($hoverRating ?: $rating) ? 'colorful' : 'bw' }}"
                        wire:click="setRating({{ $index + 1 }})" wire:mouseover="setHoverRating({{ $index + 1 }})"
                        wire:mouseleave="resetHoverRating">
                        {{ $emoji }}
                    </span>
                @endforeach

                <span class=" pt-1 flex pl-[64px] text-cyan-600 text-lg">{{ $ratingLabel }} </span>
                
            </div>
            @if ($errors->has('rating'))
                    <span class="text-red-500">{{ $errors->first('rating') }}</span>
                @endif

            {{-- <livewire:cx-tickets.survey.rating-reasons /> --}}

            <div class="p-4">
                <h3 class="text-lg font-bold mb-2">Select Satisfaction Reason</h3>
                <div class="flex gap-2 items-center">
                    <select wire:model="selectedSatisfactionReason" class="border p-2 rounded-md w-[470px]">
                        <option value="">-- Select a reason --</option>
                        @foreach($satisfactionReasons as $reason)
                            <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                    </select>
                </div>
            
                <h3 class="text-lg font-bold mt-4 mb-2">Select Dissatisfaction Reason</h3>
                <div class="flex gap-2 items-center">
                    <select wire:model="selectedDissatisfactionReason" class="border p-2 rounded-md w-[470px]">
                        <option value="">-- Select a reason --</option>
                        @foreach($dissatisfactionReasons as $reason)
                            <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                    </select>
                </div>
            
                <h3 class="text-lg font-bold mt-4">Selected Reasons</h3>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($selectedReasons as $reason)
                        @php
                            $colorClass = in_array($reason, $satisfactionReasons) ? 'bg-green-200 text-black-700' : 'bg-red-200 text-black-700';
                        @endphp
                        <span class="px-3 py-1 rounded-md flex items-center {{ $colorClass }}">
                            {{ $reason }}
                            <button wire:click="removeReason('{{ $reason }}')" class="ml-2 text-black font-bold">&times;</button>
                        </span>
                    @endforeach
                </div>
            </div>
            

        </div>
        @endif

        @if($isCancel)

        <div class="p-4">
            <h3 class="text-lg font-bold mb-2">Select Cancelling Reason</h3>
            <div class="flex gap-2 items-center">
                <select wire:model="selectedCancellingReason" class="border p-2 rounded-md w-[470px]">
                    <option value="">-- Select a reason --</option>
                    @foreach($cancelReasons as $reason)
                        <option value="{{ $reason }}">{{ $reason }}</option>
                    @endforeach
                </select>
            </div>

            <h3 class="text-lg font-bold mt-4">Selected Reasons</h3>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($selectedReasons as $reason)
                        @php
                            $colorClass = in_array($reason, $cancelReasons) ? 'bg-green-200 text-black-700' : 'bg-red-200 text-black-700';
                        @endphp
                        <span class="px-3 py-1 rounded-md flex items-center {{ $colorClass }}">
                            {{ $reason }}
                            <button wire:click="removeReason('{{ $reason }}')" class="ml-2 text-black font-bold">&times;</button>
                        </span>
                    @endforeach
                </div>
        </div>

        @endif


        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                {{-- <x-button flat negative label="Delete" wire:click="delete" /> --}}
                <div>

                </div>

                <div class="flex">
                    <x-button flat label="Cancel" x-on:click="close" />
                    @if(!$isCancel)
                    <x-button primary label="Rate" wire:click="rate" />
                    @else
                    <x-button primary label="Cancle Survey" wire:click="cancelRatings" />
                    @endif
                </div>
            </div>
        </x-slot>
    </div>
</x-modal.card>
