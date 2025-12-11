@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Quotation {{ $quotation->code }}</h3>

    <form action="{{ route('quotations.update', $quotation) }}" method="POST" id="quotation-form">
        @csrf @method('PUT')
        @include('quotations.partials.form', ['quotation' => $quotation])
        @include('quotations.partials.modals')

        <button class="btn btn-primary">Update Quotation</button>
    </form>
</div>
@endsection
