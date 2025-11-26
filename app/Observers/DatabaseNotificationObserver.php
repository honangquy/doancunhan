<?php

namespace App\Observers;

use Illuminate\Notifications\DatabaseNotification;

class DatabaseNotificationObserver
{
    /**
     * Handle the DatabaseNotification "creating" event.
     * Extract title and message from data array before saving
     */
    public function creating(DatabaseNotification $notification): void
    {
        $data = $notification->data;

        // Extract title and message if they exist in data
        if (isset($data['title'])) {
            $notification->title = $data['title'];
        }

        if (isset($data['message'])) {
            $notification->message = $data['message'];
        }

        // Keep only the 'data' sub-array in the data column
        if (isset($data['data'])) {
            $notification->data = $data['data'];
        }
    }
}
