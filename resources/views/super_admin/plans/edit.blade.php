@extends('super_admin.layout')
@section('title','Edit Plan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:500px">
  <div class="card-header bg-white"><strong>Edit Plan: {{ $plan->name }}</strong></div>
  <div class="card-body">
    <form method="POST" action="{{ route('super.plans.update', $plan) }}">
      @csrf @method('PUT')
      @include('super_admin.plans._form')
      <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('super.plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
