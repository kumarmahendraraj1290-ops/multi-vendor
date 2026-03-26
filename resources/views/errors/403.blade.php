@extends('layouts.app')
@section('title', '403 - Forbidden')
@section('content')
<div class="text-center py-5">
    <i class="bi bi-shield-exclamation text-danger" style="font-size:4rem"></i>
    <h3 class="mt-3">403 — Forbidden</h3>
    <p class="text-muted">{{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}</p>
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Go Back</a>
</div>
@endsection
