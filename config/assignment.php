<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reviewer Assignment Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho hệ thống phân công phản biện
    |
    */

    // Số reviewer tối đa cho mỗi bài báo
    'max_reviewers_per_paper' => env('MAX_REVIEWERS_PER_PAPER', 3),

    // Số bài báo tối đa mỗi reviewer có thể nhận
    'max_papers_per_reviewer' => env('MAX_PAPERS_PER_REVIEWER', 5),

    // Điểm bidding tối thiểu để được xem xét phân công tự động
    'min_bidding_value_for_auto_assign' => env('MIN_BIDDING_VALUE', 1),

    // Trọng số cho thuật toán phân công tự động
    'auto_assign_weights' => [
        'bidding_value' => 0.5,      // 50% weight cho mức độ bidding
        'workload' => 0.3,            // 30% weight cho workload hiện tại
        'keyword_match' => 0.2,       // 20% weight cho keyword match
    ],

    // Có cho phép phân công tự động khi reviewer chưa bidding không
    'allow_auto_assign_without_bid' => false,

    // Thời gian chờ reviewer phản hồi (days)
    'reviewer_response_deadline_days' => 7,

    // Gửi email thông báo khi phân công
    'send_assignment_notification' => true,

    // Gửi email nhắc nhở trước deadline
    'send_reminder_before_deadline_days' => 2,
];
