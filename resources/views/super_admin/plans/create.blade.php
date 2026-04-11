@extends('super_admin.layout')
@section('title','Create Plan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:500px">
  <div class="card-header bg-white"><strong>New Plan</strong></div>
  <div class="card-body">
    <form method="POST" action="{{ route('super.plans.store') }}">
      @csrf
      @include('super_admin.plans._form')
      <button class="btn btn-primary mt-3">Create Plan</button>
    </form>
  </div>
</div>
@endsection
