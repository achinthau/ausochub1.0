<div>
    <x-jet-dialog-modal wire:model="createUserBreakModal">
        <x-slot name="title">
            Start Break
            <hr>
        </x-slot>

        <x-slot name="content">
            <div class="py-8">
                <div class="space-y-6">

                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-secondary-700 dark:text-gray-400">
                            Break Type
                        </label>
                        <div class="flex space-x-8">
                            @foreach ($breakTypes as $_breakType)
                                <div>
                                    <label for="breakType" class="flex items-center ">
                                        <input type="radio"
                                            class="form-radio rounded-full transition ease-in-out duration-100 w-5 h-5
                                                    border-secondary-300 text-primary-600 focus:ring-primary-600 focus:border-primary-400
                                                    dark:border-secondary-500 dark:checked:border-secondary-600 dark:focus:ring-secondary-600
                                                    dark:focus:border-secondary-500 dark:bg-secondary-600 dark:text-secondary-600
                                                    dark:focus:ring-offset-secondary-800"
                                            id="breakType" value="{{ $_breakType->id }}" wire:model="breakType"
                                            name="breakType">

                                        <label
                                            class="block text-sm font-medium text-secondary-700 dark:text-gray-400 ml-2"
                                            for="breakType">
                                            {{ $_breakType->title }}
                                        </label>
                                    </label>

                                </div>
                            @endforeach
                        </div>
                        @error('breakType')
                            <p class="mt-2 text-sm text-negative-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @if ($breakType == 3)
                        <div>
                            <x-textarea label="Decription" placeholder="write your decription" wire:model.defer="description"/>
                        </div>
                    @endif
                </div>


            </div>


        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('createUserBreakModal')" wire:loading.attr="disabled">
                Close
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="save" wire:loading.attr="disabled">
                Start
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
