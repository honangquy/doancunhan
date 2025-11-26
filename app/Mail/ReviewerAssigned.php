<?php

namespace App\Mail;

use App\Models\ReviewerAssignment;
use App\Models\BaiBao;
use App\Models\NguoiDung;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewerAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $assignment;
    public $paper;
    public $reviewer;

    /**
     * Create a new message instance.
     */
    public function __construct(ReviewerAssignment $assignment)
    {
        $this->assignment = $assignment;
        $this->paper = BaiBao::find($assignment->paper_id);
        $this->reviewer = NguoiDung::find($assignment->user_id);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Bạn được phân công phản biện bài báo mới')
                    ->view('emails.reviewer-assigned')
                    ->with([
                        'reviewerName' => $this->reviewer->full_name,
                        'paperTitle' => $this->paper->title,
                        'paperId' => $this->paper->paper_id,
                        'assignmentId' => $this->assignment->id,
                        'assignedAt' => $this->assignment->assigned_at,
                        'dueDate' => now()->addDays(14), // 2 weeks to review
                    ]);
    }
}