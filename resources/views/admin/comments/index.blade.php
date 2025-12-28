@extends('layouts.admin')

@section('title', 'All Comments')
@push('styles')
    <style>
        .card {
            height: 60vh;
            overflow-y: scroll;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">
                <i class="bi bi-chat-quote"></i> All Comments
            </h3>
            <a href="{{ route('admin.comments.pending') }}" class="btn btn-outline-warning">
                <i class="bi bi-hourglass-split"></i> Pending
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @if($comments->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Comment</th>
                                    <th>On Post</th>
                                    <th>Submitter</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comments as $comment)
                                    <tr>
                                        <td>
                                            <div class="truncate" style="max-width: 300px;">
                                                {{ Str::limit($comment->body, 100) }}
                                            </div>
                                            @if($comment->parent_id)
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-reply"></i> Reply
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ $comment->commentable->url }}" target="_blank">
                                                {{ Str::limit($comment->commentable->title, 40) }}
                                            </a>
                                        </td>
                                        <td>
                                            <strong>{{ $comment->display_name }}</strong><br>
                                            @if($comment->author)
                                                <small class="text-muted">{{ $comment->author->email }}</small>
                                            @else
                                                <small class="text-muted">{{ $comment->guest_email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $comment->created_at->diffForHumans() }}
                                        </td>
                                        <td>
                                            @if($comment->approved)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(!$comment->approved)
                                                <form action="{{ route('admin.comments.approve', $comment) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="confirmApprove(this)">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmReject({{ $comment->id }})">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-chat-square-x display-1 text-muted"></i>
                        <h4 class="mt-3">No comments found!</h4>
                        <p class="text-muted">It's quiet in here.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        {{ $comments->links() }}
    </div>
</div>

@push('scripts')
<script>
function confirmApprove(button) {
    if (confirm('Approve this comment?')) {
        button.form.submit();
    }
}

function confirmReject(commentId) {
    if (confirm('Delete this comment?')) {
        fetch(`/admin/comments/${commentId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then(() => location.reload());
    }
}
</script>
@endpush
@endsection