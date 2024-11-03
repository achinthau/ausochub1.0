<x-mail::message>
@if ($isSuccess)
Pickup Order # : {{$ticket->bill_no}}
@else        
# Pickup Order Failed
@endif

## Order Details
- **Order Number:** {{ $ticket->order_ref }}
- **Order Date:** {{ $ticket->created_at }}
- **Order Pickup:** {{ $ticket->due_at }}
- **Outlet:** {{ $ticket->outlet->title }} - {{ $ticket->outlet->contact_no }}
- **Customer Name:** {{ $ticket->lead->full_name }}
- **Contact Number:** {{ $ticket->lead->contact_number }}

## Products Ordered
<x-mail::table>
| Product Name | Quantity | Unit Price | Total Price |
|--------------|----------|------------|-------------|
@foreach ($ticket->items as $item)
| {{$item->item->descr}}    | {{$item->qty}}        | Rs {{ number_format($item->unit_price, 2) }}     | Rs {{ number_format($item->unit_price * $item->qty, 2) }}      |
@endforeach
</x-mail::table>

## Order Total
- **Total:** Rs {{ number_format($ticket->order_total, 2) }}
</x-mail::message>


