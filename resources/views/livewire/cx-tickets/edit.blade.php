<x-modal.card title="Update Cx Ticket" blur align="center" wire:model="updateTicketModal">
    <div class="max-h-[500px] overflow-y-auto p-4">
        
    <div class="grid grid-cols-1 gap-4 border p-4 rounded-lg shadow-md">
        <h1 class="p-2 pl-0 font-bold text-lg">Product Details</h1>

        <x-native-select
        label="Category"
        placeholder="Select Category"
        :options="[['id'=>'service','title'=>'Service'],['id'=>'repair','title'=>'Repair'],['id'=>'installation','title'=>'Installation']]"
        option-label="title"
        option-value="id"
        wire:model="category"
        class="pointer-events-none"
    />
    
    <x-input label="Product" placeholder="Enter the product" wire:model="product" class="pointer-events-none"  />             
    <x-input label="Model" placeholder="Enter the product model" wire:model="model" class="pointer-events-none" />
    <x-input label="Work Order No" placeholder="Enter the Work Order No" wire:model="work_order_no" class="pointer-events-none" />
    <x-native-select
        label="Service Center"
        placeholder="Select Service Center"
        :options="[['id'=>'Kurunegala','title'=>'Kurunegala'],['id'=>'Alawwa','title'=>'Alawwa'],['id'=>'Narammala','title'=>'Narammala'],['id'=>'Kegalle','title'=>'Kegalle'],['id'=>'Polgahawela','title'=>'Polgahawela']]"
        option-label="title"
        option-value="id"
        wire:model="service_center"
        class="pointer-events-none"
    />

    <x-native-select
        label="Warranty Status"
        placeholder="Select Category"
        :options="[['id'=>'Under Warranty','title'=>'Under Warranty'],['id'=>'Out of Warranty','title'=>'Out of Warranty'],['id'=>'installation','title'=>'Installation']]"
        option-label="title"
        option-value="id"
        wire:model="warranty_status"
        class="pointer-events-none"
    />
    <x-input type="date" label="Sold Date" wire:model="sold_date" class="pointer-events-none" />
     
    </div>

    <div class="border p-4 rounded-lg mt-4 shadow-md">
        <h1 class="p-4 pl-0 font-bold text-lg">Customer Details</h1>

    <div class="grid grid-cols-1 gap-4">

        <x-input label="Customer Name" placeholder="Enter the Customer Name" wire:model="customer_name" class="pointer-events-none" />  
    <x-input label="Customer Address" placeholder="Enter the Customer Address" wire:model="customer_address" class="pointer-events-none" />  
    <x-input label="Customer Contact 01" placeholder="Enter the Customer Contact 01" wire:model="customer_contact_01" class="pointer-events-none" />  
    <x-input label="Customer Contact 02" placeholder="Enter the Customer Contact 02" wire:model="customer_contact_02" class="pointer-events-none" /> 
      
    </div>
    </div>

    <div class="border p-4 rounded-lg mt-4 shadow-md">
        <h1 class="p-4 pl-0 font-bold text-lg">Technician Details</h1>

    <div class="grid grid-cols-1 gap-4">

        <x-input label="Technician Name" placeholder="Enter the Technician Name" wire:model="technician_name" />   
        <x-input label="Technician Contact" placeholder="Enter the Technician Contact" wire:model="technician_contact" />  
        <x-input label="Supervisor Name" placeholder="Enter the Supervisor Name" wire:model="supervisor_name" />   
        <x-input label="Supervisor Contact" placeholder="Enter the Supervisor Contact" wire:model="supervisor_contact" />  
      
    </div>
    </div>
 
    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            {{-- <x-button flat negative label="Delete" wire:click="delete" /> --}}
            <div>

            </div>
 
            
            <div class="flex justify-between w-full">
                <div class="flex items-center">
                    @if($status == 'Rated')
                        <div class="border p-1 bg-slate-300 rounded-md flex items-center space-x-2">
                            <span class="font-semibold">Satisfaction Rate:</span>
                            <div class="text-yellow-500 text-xl">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $satisfaction_rate)
                                        ★  <!-- Filled Star -->
                                    @else
                                        ☆  <!-- Empty Star -->
                                    @endif
                                @endfor
                            </div>
                        </div>
                    @endif
                    @if($status == 'Canceled')
                    <div class=" p-1 text-red-400 flex items-center space-x-2">
                        <span class="font-semibold">Cancelled Survey</span>
                    </div>
                    @endif
                </div>
                
                
                <div class="flex items-center space-x-2">
                    <x-button flat label="Cancel" x-on:click="close" />
                    
                    @if($status == "Open")
                        <x-button primary label="Close Ticket" wire:click="closeTicket" />
                    @endif
            
                    <x-button primary label="Update" wire:click="update" />
                </div>
            </div>
            
        </div>
    </x-slot>
    </div>
</x-modal.card>