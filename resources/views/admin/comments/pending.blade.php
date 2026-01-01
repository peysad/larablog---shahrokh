@extends('layouts.admin')

@section('title', 'Pending Comments')

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
                <i class="bi bi-hourglass-split"></i> Pending Comments
            </h3>
            <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> All Comments
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
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <!-- Approve Button (Modal Trigger) -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#approveModal"
                                                        onclick="setupAction('{{ route('admin.comments.approve', $comment) }}')">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>

                                                <!-- Reject Button (Modal Trigger) -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal"
                                                        onclick="setupAction('{{ route('admin.comments.reject', $comment) }}')">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle display-1 text-success"></i>
                        <h4 class="mt-3">No pending comments!</h4>
                        <p class="text-muted">All caught up.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        {{ $comments->links() }}
    </div>
</div>

<!-- Approve Modal (Green Style) -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="bi bi-check-circle"></i> Approve Comment
                </h5>
                <button type="button" class="btn-close btn-close-white" style="margin-right: 0;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this comment? It will be visible to the public.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="executeAction()">Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal (Red Style - Force Delete Type) -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-x-circle"></i> Reject Comment
                </h5>
                <button type="button" class="btn-close" style="margin-right: 0;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger"><strong>Warning:</strong> Are you sure you want to reject this comment? It will be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="executeAction()">Reject</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentActionUrl = '';

// Set the action URL before opening the modal
function setupAction(url) {
    currentActionUrl = url;
}

// Execute the action using Fetch (AJAX)
function executeAction() {
    if (!currentActionUrl) {
        console.error('No action URL set.');
        return;
    }

    fetch(currentActionUrl, {
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
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endpush