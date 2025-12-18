@extends('layouts.app')
@section('title','Orders')
@section('content')
<div class="container">
    <h3>Create Order</h3>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST" id="order-form">
        @csrf
        @include('orders.partials.form', ['order' => null])

        <button class="btn btn-primary">Save Order</button>
    </form>
</div>
@endsection
