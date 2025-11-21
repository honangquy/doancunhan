<?php

namespace App\Observers;

use App\Models\HoiThao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HoiThaoObserver
{
    /**
     * Handle the HoiThao "created" event.
     *
     * @param  \App\Models\HoiThao  $hoiThao
     * @return void
     */
    public function created(HoiThao $hoiThao)
    {
        // Auto-create CHAIR role when conference is created
        if ($hoiThao->chair_id) {
            $this->ensureChairRole($hoiThao);
        }
    }

    /**
     * Handle the HoiThao "updated" event.
     *
     * @param  \App\Models\HoiThao  $hoiThao
     * @return void
     */
    public function updated(HoiThao $hoiThao)
    {
        // If chair_id changed, ensure new chair has role
        if ($hoiThao->isDirty('chair_id') && $hoiThao->chair_id) {
            $this->ensureChairRole($hoiThao);
        }
    }

    /**
     * Ensure CHAIR role exists for the conference's chair
     *
     * @param  \App\Models\HoiThao  $hoiThao
     * @return void
     */
    protected function ensureChairRole(HoiThao $hoiThao)
    {
        try {
            // Check if role already exists
            $existingRole = DB::table('vaitronguoidung')
                ->where('user_id', $hoiThao->chair_id)
                ->where('role_code', 'CHAIR')
                ->where('conference_id', $hoiThao->conference_id)
                ->first();

            if (!$existingRole) {
                DB::table('vaitronguoidung')->insert([
                    'user_id' => $hoiThao->chair_id,
                    'role_code' => 'CHAIR',
                    'conference_id' => $hoiThao->conference_id,
                ]);

                Log::info("[HoiThaoObserver] Auto-created CHAIR role", [
                    'user_id' => $hoiThao->chair_id,
                    'conference_id' => $hoiThao->conference_id,
                    'conference_title' => $hoiThao->title,
                ]);
            } else {
                Log::info("[HoiThaoObserver] CHAIR role already exists", [
                    'user_id' => $hoiThao->chair_id,
                    'conference_id' => $hoiThao->conference_id,
                    'role_id' => $existingRole->user_role_id,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate key error (race condition)
            if ($e->getCode() == 23000) {
                Log::warning("[HoiThaoObserver] CHAIR role already exists (race condition)", [
                    'user_id' => $hoiThao->chair_id,
                    'conference_id' => $hoiThao->conference_id,
                    'error' => $e->getMessage(),
                ]);
            } else {
                Log::error("[HoiThaoObserver] Failed to create CHAIR role", [
                    'user_id' => $hoiThao->chair_id,
                    'conference_id' => $hoiThao->conference_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Re-throw to ensure transaction rollback
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("[HoiThaoObserver] Unexpected error creating CHAIR role", [
                'user_id' => $hoiThao->chair_id,
                'conference_id' => $hoiThao->conference_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Re-throw to ensure transaction rollback
            throw $e;
        }
    }
}
