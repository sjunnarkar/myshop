@component('mail::message')
# New Order Received

A new order has been placed on your store.

**Order Number:** {{ $order->order_number }}  
**Order Date:** {{ $order->created_at->format('F j, Y g:i A') }}  
**Total Amount:** ${{ number_format($order->total_amount, 2) }}

## Customer Information

**Name:** {{ $order->billing_name }}  
**Email:** {{ $order->billing_email }}  
**Phone:** {{ $order->billing_phone }}

## Order Details

@component('mail::table')
| Product | Quantity | Price |
|:--------|:--------:| -----:|
@foreach($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ${{ number_format($item->price * $item->quantity, 2) }} |
@endforeach
|  |  |  |
| **Subtotal** |  | ${{ number_format($order->total_amount, 2) }} |
| **Shipping** |  | Free |
| **Total** |  | ${{ number_format($order->total_amount, 2) }} |
@endcomponent

## Shipping Address

{{ $order->shipping_name }}  
{{ $order->shipping_address }}  
{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}  
{{ $order->shipping_country }}  
**Phone:** {{ $order->shipping_phone }}

## Additional Information

@if($order->payment_method === 'credit_card')
**Payment Method:** Credit Card
@else
**Payment Method:** PayPal
@endif

@foreach($order->items as $item)
@if($item->customization)
### Customization for {{ $item->product->name }}:
@foreach($item->customization as $area => $details)
- {{ $area }}: {{ $details['text'] }} ({{ $details['font'] }})
@endforeach
@endif

@if($item->options)
### Options for {{ $item->product->name }}:
@foreach($item->options as $option => $value)
- {{ $option }}: {{ $value }}
@endforeach
@endif
@endforeach

@component('mail::button', ['url' => route('admin.orders.show', $order->id)])
View Order Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 