@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Create Quotation</h3>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('quotations.store') }}" method="POST" id="quotation-form">
        @csrf
        @include('quotations.partials.form', ['quotation' => null])

        <button class="btn btn-primary">Save Quotation</button>
    </form>
</div>
@endsection
