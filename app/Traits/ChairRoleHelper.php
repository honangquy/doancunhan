<?php

namespace App\Traits;

use App\Models\VaiTroNguoiDung;
use Illuminate\Support\Facades\DB;

trait ChairRoleHelper
{
    /**
     * Get the conference_id for the current CHAIR user
     *
     * @return int
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function getChairConferenceId()
    {
        $userId = auth()->id();

        // Get CHAIR role with conference_id (filter out NULL conference_id)
        $chairRole = VaiTroNguoiDung::where('user_id', $userId)
                                    ->where('role_code', 'CHAIR')
                                    ->whereNotNull('conference_id')
                                    ->first();

        if (!$chairRole || !$chairRole->conference_id) {
            abort(403, 'Bạn chưa được gán làm Chair cho hội thảo nào.');
        }

        return $chairRole->conference_id;
    }

    /**
     * Get all conference IDs where user is CHAIR
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getChairConferenceIds()
    {
        $userId = auth()->id();

        return DB::table('vaitronguoidung')
            ->where('user_id', $userId)
            ->where('role_code', 'CHAIR')
            ->whereNotNull('conference_id')
            ->pluck('conference_id');
    }

    /**
     * Check if current user is CHAIR for specific conference
     *
     * @param int $conferenceId
     * @return bool
     */
    protected function isChairForConference($conferenceId)
    {
        $userId = auth()->id();

        return VaiTroNguoiDung::where('user_id', $userId)
                              ->where('role_code', 'CHAIR')
                              ->where('conference_id', $conferenceId)
                              ->exists();
    }
}
