@extends('layouts.admin')

@section('title', 'Create New Post')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 fw-bold">
                    <i class="bi bi-file-earmark-plus"></i> Create New Post
                </h4>
            </div>
            <div class="card-body p-4">
                @include('posts.partials.form', [
                    'post' => null,
                    'method' => 'POST',
                    'action' => route('posts.store'),
                    'buttonText' => 'Create Post'
                ])
            </div>
        </div>
    </div>
</div>
@endsection