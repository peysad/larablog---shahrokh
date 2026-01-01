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
                                            <div class="btn-group" role="group">
                                                @if(!$comment->approved)
                                                    <!-- Approve Button (Modal Trigger) -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#approveModal{{ $comment->id }}"
                                                            title="Approve Comment">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                @endif
                                                
                                                <!-- Reject/Delete Button (Modal Trigger) -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal{{ $comment->id }}"
                                                        title="Reject Comment">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </div>

                                            <!-- Approve Modal (Green Style) -->
                                            @if(!$comment->approved)
                                                <div class="modal fade" id="approveModal{{ $comment->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-success text-white">
                                                                    <h5 class="modal-title">Approve Comment</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Are you sure you want to approve this comment? It will be visible to the public.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-success">Approve</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Reject Modal (Red Style - Force Delete Type) -->
                                            <div class="modal fade" id="rejectModal{{ $comment->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <!-- Note: Reject uses JS fetch, not a standard form submit, but styling is applied to the modal -->
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">Reject Comment</h5>
                                                            <button type="button" class="btn-close btn-close-white" style="margin-right: 0;" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-danger"><strong>Warning:</strong> Are you sure you want to reject this comment? It will be deleted.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="button" class="btn btn-danger" onclick="submitRejectForm({{ $comment->id }})">Reject</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
@endsection

@push('scripts')
<script>
// Function to handle Reject (Delete) via AJAX
function submitRejectForm(commentId) {
    if (!commentId) {
        console.error('Comment ID is missing.');
        return;
    }

    fetch(`/admin/comments/${commentId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            // Reload page on success
            window.location.reload();
        } else {
            console.error('Action failed');
            alert('Failed to reject comment.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    });
}
</script>
@endpush