@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Order {{ $order->order_code }}</h3>

    <form action="{{ route('orders.update', $order) }}" method="POST" id="order-form">
        @csrf @method('PUT')
        @include('orders.partials.form', ['order' => $order])

        <button class="btn btn-primary">Update Order</button>
    </form>
</div>
@endsection
