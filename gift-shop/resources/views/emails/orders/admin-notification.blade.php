@component('mail::message')
# New Order Received

A new order has been placed on your store.

**Order Number:** #{{ $order->order_number }}  
**Customer:** {{ $order->shipping_name }} ({{ $order->shipping_email }})  
**Date:** {{ $order->created_at->format('F j, Y g:i A') }}

## Order Details

@component('mail::table')
| Product | Quantity | Price |
|:--------|:---------|:------|
@foreach($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | {{ \App\Helpers\CurrencyHelper::format($item->subtotal) }} |
@endforeach
@endcomponent

**Subtotal:** {{ \App\Helpers\CurrencyHelper::format($order->total_amount - $order->shipping_cost) }}  
**Shipping:** {{ $order->shipping_cost > 0 ? \App\Helpers\CurrencyHelper::format($order->shipping_cost) : 'Free' }}  
**Total:** {{ \App\Helpers\CurrencyHelper::format($order->total_amount) }}

## Customer Details

**Shipping Address:**  
{{ $order->shipping_name }}  
{{ $order->shipping_address }}  
{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}  
{{ $order->shipping_country }}

**Payment Method:** {{ ucfirst($order->payment_method) }}  
**Order Status:** {{ ucfirst($order->status) }}

@component('mail::button', ['url' => route('admin.orders.show', $order)])
View Order Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 