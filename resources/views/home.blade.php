@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Dashboard</h2>
        <a href="{{ Route('product.create') }}" class="btn btn-info">Add New Product</a>
    </div>
@endsection

@push('body-scripts')

@endpush
