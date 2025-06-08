@component('mail::message')
# Order Confirmation

Dear {{ $order->shipping_name }},

Thank you for your order! We're pleased to confirm that we've received your order #{{ $order->order_number }}.

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

## Shipping Address
{{ $order->shipping_name }}  
{{ $order->shipping_address }}  
{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}  
{{ $order->shipping_country }}

You can track your order status by logging into your account.

@component('mail::button', ['url' => route('profile.orders.show', $order)])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 