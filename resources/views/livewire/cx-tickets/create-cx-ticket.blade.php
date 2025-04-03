<x-modal.card title="Add Cx Ticket" blur align="center" wire:model="creatingCxTicket">
    <div class="max-h-[500px] overflow-y-auto px-2">
    <div class="grid grid-cols-1  gap-4">
        

        <div class="border p-4 rounded-lg shadow-md mt-4">
            <h1 class="p-2 pl-0 font-bold text-lg">Product Details</h1>

        <x-native-select
        label="Category"
        placeholder="Select Category"
       :options="$categories->map(fn($category) => ['id' => $category->name, 'title' => $category->name])"
        option-label="title"
        option-value="id"
        wire:model="category"
    />
    
    <x-input label="Product" placeholder="Enter the product" wire:model="product" />             
    <x-input label="Model" placeholder="Enter the product model" wire:model="model" />
    <x-input label="Work Order No" placeholder="Enter the Work Order No" wire:model="work_order_no"/>
    {{-- <x-input label="Service Center" placeholder="Enter the Service Center" wire:model="service_center"/>  --}}
    <x-native-select
        label="Service Center"
        placeholder="Select Service Center"
        :options="$serviceCenters->map(fn($center) => ['id' => $center->name, 'title' => $center->name])"
        option-label="title"
        option-value="id"
        wire:model="service_center"
    />

    <x-native-select
        label="Warranty Status"
        placeholder="Select Category"
        :options="[['id'=>'Under Warranty','title'=>'Under Warranty'],['id'=>'Out of Warranty','title'=>'Out of Warranty']]"
        option-label="title"
        option-value="id"
        wire:model="warranty_status"
    />
    <x-input type="date" label="Sold Date" wire:model="sold_date" />
    
    {{-- <x-input label="Customer Name" placeholder="Enter the Customer Name" wire:model="customer_name" />  
    <x-input label="Customer Address" placeholder="Enter the Customer Address" wire:model="customer_address" />  
    <x-input label="Customer Contact 01" placeholder="Enter the Customer Contact 01" wire:model="customer_contact_01" />  
    <x-input label="Customer Contact 02" placeholder="Enter the Customer Contact 02" wire:model="customer_contact_02" />   --}}
    
    {{-- <x-input label="Technician Name" placeholder="Enter the Technician Name" wire:model="technician_name" />   
    <x-input label="Technician Contact" placeholder="Enter the Technician Contact" wire:model="technician_contact" />  
    <x-input label="Supervisor Name" placeholder="Enter the Supervisor Name" wire:model="supervisor_name" />   
    <x-input label="Supervisor Contact" placeholder="Enter the Supervisor Contact" wire:model="supervisor_contact" />   --}}

      
    </div>
    </div>

    <div class="border p-4 rounded-lg shadow-md mt-4">
        <h1 class="p-4 pl-0 font-bold text-lg">Customer Details</h1>

    <div class="grid grid-cols-1 gap-4">

        <x-input label="Customer Name" placeholder="Enter the Customer Name" wire:model="customer_name" />  
    <x-input label="Customer Address" placeholder="Enter the Customer Address" wire:model="customer_address" />  
    <x-input label="Customer Contact 01" placeholder="Enter the Customer Contact 01" wire:model="customer_contact_01" />  
    <x-input label="Customer Contact 02" placeholder="Enter the Customer Contact 02" wire:model="customer_contact_02" />  
      
    </div>
    </div>

    <div class="border p-4 rounded-lg shadow-md mt-4">
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
 
            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Save" wire:click="save" />
            </div>
        </div>
    </x-slot>
    </div>
</x-modal.card>