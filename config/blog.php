<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blog Settings
    |--------------------------------------------------------------------------
    */

    // Allow guest comments (not logged in users)
    'allow_guest_comments' => true,

    // Auto-approve comments from authenticated users with these roles
    'auto_approve_roles' => ['Admin', 'Editor'],

    // Maximum nesting level for replies (0 = no replies, 3 = replies to replies to replies)
    'max_comment_nesting_depth' => 3,

    // Number of comments per page
    'comments_per_page' => 20,

    // Enable comment notifications
    'comment_notifications' => true,
];