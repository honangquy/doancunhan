<?php

namespace App\Policies;

use App\Models\NguoiDung;
use App\Models\NotificationOutbox;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\NguoiDung  $nguoiDung
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(NguoiDung $nguoiDung)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\NguoiDung  $nguoiDung
     * @param  \App\Models\NotificationOutbox  $notificationOutbox
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(NguoiDung $nguoiDung, NotificationOutbox $notificationOutbox)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\NguoiDung  $nguoiDung
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(NguoiDung $nguoiDung)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\NguoiDung  $nguoiDung
     * @param  \App\Models\NotificationOutbox  $notificationOutbox
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(NguoiDung $nguoiDung, NotificationOutbox $notificationOutbox)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\NguoiDung  $nguoiDung
     * @param  \App\Models\NotificationOutbox  $notificationOutbox
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(NguoiDung $nguoiDung, NotificationOutbox $notificationOutbox)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\NguoiDung  $nguoiDung
     * @param  \App\Models\NotificationOutbox  $notificationOutbox
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(NguoiDung $nguoiDung, NotificationOutbox $notificationOutbox)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\NguoiDung  $nguoiDung
     * @param  \App\Models\NotificationOutbox  $notificationOutbox
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(NguoiDung $nguoiDung, NotificationOutbox $notificationOutbox)
    {
        //
    }
}
