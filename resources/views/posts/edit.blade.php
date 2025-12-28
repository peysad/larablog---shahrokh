@extends('layouts.admin')

@section('title', 'Edit Post: ' . $post->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-pencil-square"></i> Edit Post
                    </h4>
                    <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye"></i> View
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                @include('posts.partials.form', [
                    'post' => $post,
                    'method' => 'PUT',
                    'action' => route('posts.update', $post),
                    'buttonText' => 'Update Post'
                ])
            </div>
        </div>
    </div>
</div>
@endsection