<x-modal.card title="Create Order" blur fullscreen=true wire:model="creatingOrder">
    <div class="space-y-5 pb-32">
        <div class="flex space-x-2">
            <div class="w-1/2 space-y-2 border rounded-md py-2">
                <div class="grid grid-cols-4">

                    <div class="px-2">
                        <x-native-select label="Delivery Type" wire:model="ticket.ticket_sub_category_id">
                            <option value="0">Select Delivery Type</option>
                            @foreach ($subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}">{{ $subCategory->title }}</option>
                            @endforeach
                        </x-native-select>

                    </div>
                    <div class="px-2">
                        <x-select label="Payment Type" placeholder="Select Payment Type" multiselect :options="$tags"
                            wire:model="ticket.tags" />

                    </div>
                    <div class="px-2">
                        <x-native-select label="Outlet" wire:model="ticket.outlet_id">
                            <option value="0" disabled>Select Outlet</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->title }}</option>
                            @endforeach
                        </x-native-select>

                    </div>
                    <div class="px-2">
                        <x-datetime-picker label="Pickup at" placeholder="Select Pickup Time"
                            parse-format="YYYY-MM-DD HH:mm:ss" wire:model.defer="ticket.due_at" min-time="11:00"
                            interval="15" max-time="22:45" />
                    </div>


                </div>


                <div class="px-2">
                    <x-textarea label="Special Instruction" wire:model.lazy="ticket.description"
                        placeholder="write customer's instruction here" />
                </div>
            </div>
            <div class="w-1/2 border rounded-md py-2">
                <div class="p-4 text-6xl font-bold text-right">
                    Total Amount : {{ number_format($this->total, 2) }}
                </div>
            </div>
        </div>

        @if ($ticket->ticket_category_id == 3)
            <hr>
            <div class="px-2 flex">
                <div class="w-2/3">
                    <div>
                        <x-toggle left-label="POS" label="CRM" wire:model="ticket.crm" />
                    </div>

                    @if ($ticket->crm)
                        <div class="space-y-2">
                            <div class="flex mt-4 font-semibold text-sm">
                                <div class="w-1/4 text-center">Item</div>
                                <div class="w-1/12 text-center">Unit Price</div>
                                <div class="w-1/12 text-center">Qty</div>
                                <div class="w-1/12 text-center">Line total</div>
                                <div class="w-1/12 text-center"></div>
                            </div>
                            <hr>
                            @foreach ($ticketItems as $index => $ticketItem)
                                @php
                                    $key = 'item-' . $index;
                                @endphp
                                <x-order.raw :ticketItem=$ticketItem :index=$index :extra=0 :key=$key />

                                @foreach ($ticketItem['extras'] as $subIndex => $extra)
                                    @php
                                        $key = 'item-' . $index . '-extra' . $subIndex;
                                    @endphp
                                    <x-order.extra-raw :ticketItem=$extra :index=$index :subIndex=$subIndex :extra=true
                                        :key=$key />
                                @endforeach
                            @endforeach
                            <div>
                                <a href="#" class="text-blue-600 text-sm" wire:click.prevent="addItem">Add Item
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mt-4">
                            <x-input label="Order Ref" wire:model.lazy="ticket.order_ref"
                                placeholder="Please enter Order Ref" />
                        </div>
                    @endif
                </div>
                <div>
                    Menu Section
                </div>
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            {{--
            <x-button flat negative label="Delete" wire:click="delete" /> --}}
            <div></div>
            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Save" wire:click="save" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
