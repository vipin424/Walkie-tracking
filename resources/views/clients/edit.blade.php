@extends('layouts.app')
@section('content')
<h4>Edit Client</h4>
<form method="post" action="{{ route('clients.update',$client) }}" class="card p-3">
@csrf @method('PUT')
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Name</label>
    <input name="name" value="{{ $client->name }}" class="form-control" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Contact Number</label>
    <input name="contact_number" value="{{ $client->contact_number }}" class="form-control" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Company</label>
    <input name="company_name" value="{{ $client->company_name }}" class="form-control">
  </div>
</div>
<div class="mt-3">
  <button class="btn btn-warning">Update</button>
  <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>
@endsection
