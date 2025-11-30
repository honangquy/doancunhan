<?php

namespace App\View\Composers;

use App\Models\YeuCauHoiThao;
use App\Models\HoiThao;
use App\Models\JoinRequest;
use App\Models\News;
use Illuminate\View\View;

class AdminStatsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $adminStats = [
            'pending_conference_requests' => YeuCauHoiThao::where('status', 'PENDING')->count(),
            'pending_configured_conferences' => HoiThao::where('status', '!=', 'ACTIVE')->count(),
            'pending_join_requests' => JoinRequest::where('status', 'PENDING')->count(),
            'pending_news' => News::where('status', 'PENDING')->count(),
        ];

        $view->with('adminStats', $adminStats);
    }
}
