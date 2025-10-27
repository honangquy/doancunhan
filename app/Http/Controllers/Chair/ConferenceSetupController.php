<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\YeuCauHoiThao;
use App\Models\HoiThao;
use App\Models\TieuBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ConferenceSetupController extends Controller
{
    /**
     * Display approved conference requests for chair configuration
     */
    public function index()
    {
        $userEmail = auth()->user()->email;
        
        // Get approved conference requests for current chair by email
        $approvedRequests = DB::table('yeucauhoithao')
            ->where('chair_email', $userEmail)
            ->where('status', 'APPROVED')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('hoithao')
                      ->whereColumn('hoithao.title', 'yeucauhoithao.title');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get already configured conferences
        $configuredConferences = DB::table('hoithao')
            ->join('yeucauhoithao', function($join) use ($userEmail) {
                $join->on('hoithao.title', '=', 'yeucauhoithao.title')
                     ->where('yeucauhoithao.chair_email', '=', $userEmail)
                     ->where('yeucauhoithao.status', '=', 'APPROVED');
            })
            ->select('hoithao.*', 'yeucauhoithao.request_id', 'yeucauhoithao.status as request_status')
            ->orderBy('hoithao.conference_id', 'desc')
            ->get();
        
        return view('chair.conferences.index', compact('approvedRequests', 'configuredConferences'));
    }

    /**
     * Show form to configure conference
     */
    public function configure($requestId)
    {
        $request = YeuCauHoiThao::where('request_id', $requestId)
            ->where('user_id', auth()->user()->user_id)
            ->where('status', 'APPROVED')
            ->whereDoesntHave('conference')
            ->firstOrFail();
            
        return view('chair.conferences.configure', compact('request'));
    }

    /**
     * Store conference configuration
     */
    public function store(Request $request, $requestId)
    {
        // Debug log
        \Log::info('Conference setup store method called', [
            'requestId' => $requestId,
            'userId' => auth()->user()->user_id,
            'requestData' => $request->all(),
            'method' => $request->method()
        ]);

        $conferenceRequest = YeuCauHoiThao::where('request_id', $requestId)
            ->where('user_id', auth()->user()->user_id)
            ->where('status', 'APPROVED')
            ->whereNull('conference_id')
            ->firstOrFail();

        $validatedData = $request->validate([
            // Basic info
            'conference_name' => 'required|string|max:255',
            'acronym' => 'required|string|max:50',
            'year' => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 5),
            'location' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'detailed_description' => 'required|string|max:2000',
            'submission_guidelines' => 'nullable|string|max:1000',
            'cfp_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB PDF
            
            // Dates
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'deadline_submission' => 'required|date|before:start_date',
            'deadline_review' => 'required|date|after:deadline_submission|before:start_date',
            'deadline_camera_ready' => 'required|date|after:deadline_review|before_or_equal:start_date',
            'result_announcement_deadline' => 'nullable|date|before_or_equal:start_date',
            
            // Contact info
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'chair_name' => 'required|string|max:255',
            'chair_email' => 'required|email|max:255',
            
            // Configuration
            'reviewers_per_paper' => 'required|integer|min:1|max:10',
            'enable_coi_check' => 'boolean',
            
            // Files and committees
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'cfp_file' => 'nullable|file|mimes:pdf|max:10240', // CFP file PDF, max 10MB
            'committees' => 'nullable|array',
            'committees.*.name' => 'required_with:committees|string|max:255',
            'committees.*.description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            // Handle banner upload
            $bannerPath = null;
            if ($request->hasFile('banner')) {
                $bannerPath = $request->file('banner')->store('conference-banners', 'public');
            }

            // Handle CFP file upload
            $cfpFilePath = null;
            if ($request->hasFile('cfp_file')) {
                $cfpFilePath = $request->file('cfp_file')->store('conference-cfp', 'public');
            }

            // Create conference
            $conference = HoiThao::create([
                // Basic info
                'title' => $validatedData['conference_name'],
                'acronym' => $validatedData['acronym'],
                'year' => $validatedData['year'],
                'location' => $validatedData['location'],
                'keywords' => $validatedData['keywords'],
                'description' => $validatedData['description'],
                'detailed_description' => $validatedData['detailed_description'],
                'submission_guidelines' => $validatedData['submission_guidelines'],
                'cfp_file_path' => $cfpFilePath,
                
                // Dates
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'deadline_submission' => $validatedData['deadline_submission'],
                'deadline_review' => $validatedData['deadline_review'],
                'deadline_camera_ready' => $validatedData['deadline_camera_ready'],
                'result_announcement_deadline' => $validatedData['result_announcement_deadline'],
                
                // Contact info
                'contact_email' => $validatedData['contact_email'],
                'contact_phone' => $validatedData['contact_phone'],
                
                // Configuration
                'reviewers_per_paper' => $validatedData['reviewers_per_paper'],
                'enable_coi_check' => $validatedData['enable_coi_check'] ?? false,
                
                // System fields
                'banner_path' => $bannerPath,
                'chair_id' => auth()->user()->user_id,
                'status' => 'PENDING_ADMIN_APPROVAL', // Needs admin approval to go live
                'level_code' => $conferenceRequest->level_code,
                'faculty_id' => $conferenceRequest->faculty_name,
                'conference_request_id' => $conferenceRequest->request_id,
                'acronym' => $validatedData['acronym'],
            ]);

            // Update conference request with conference_id
            $conferenceRequest->update([
                'conference_id' => $conference->conference_id
            ]);

            // Create committees (tieuban)
            if (isset($validatedData['committees'])) {
                foreach ($validatedData['committees'] as $committee) {
                    TieuBan::create([
                        'conference_id' => $conference->conference_id,
                        'title' => $committee['name'], // title là required
                        'committee_name' => $committee['name'],
                        'description' => $committee['description'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('chair.conferences.index')
                ->with('success', 'Hội thảo đã được cấu hình thành công. Đang chờ admin phê duyệt để kích hoạt.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Log exception
            \Log::error('Conference setup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'requestId' => $requestId,
                'userId' => auth()->user()->user_id
            ]);
            
            // Delete uploaded files if database operation failed
            if ($bannerPath) {
                Storage::disk('public')->delete($bannerPath);
            }
            if ($cfpFilePath) {
                Storage::disk('public')->delete($cfpFilePath);
            }
            
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cấu hình hội thảo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show configured conference details
     */
    public function show($conferenceId)
    {
        $userEmail = auth()->user()->email;
        
        $conference = DB::table('hoithao')
            ->join('yeucauhoithao', function($join) use ($userEmail) {
                $join->on('hoithao.title', '=', 'yeucauhoithao.title')
                     ->where('yeucauhoithao.chair_email', '=', $userEmail)
                     ->where('yeucauhoithao.status', '=', 'APPROVED');
            })
            ->select('hoithao.*', 'yeucauhoithao.request_id', 'yeucauhoithao.status as request_status')
            ->where('hoithao.conference_id', $conferenceId)
            ->first();
            
        if (!$conference) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cấu hình hội thảo: Hội thảo không tồn tại hoặc bạn không có quyền truy cập.']);
        }
            
        return view('chair.conferences.show', compact('conference'));
    }

    /**
     * Edit conference configuration (only if not approved by admin yet)
     */
    public function edit($conferenceId)
    {
        $userEmail = auth()->user()->email;
        
        $conference = DB::table('hoithao')
            ->join('yeucauhoithao', function($join) use ($userEmail) {
                $join->on('hoithao.title', '=', 'yeucauhoithao.title')
                     ->where('yeucauhoithao.chair_email', '=', $userEmail)
                     ->where('yeucauhoithao.status', '=', 'APPROVED');
            })
            ->select('hoithao.*', 'yeucauhoithao.request_id', 'yeucauhoithao.status as request_status')
            ->where('hoithao.conference_id', $conferenceId)
            ->where('hoithao.status', '!=', 'ACTIVE') // Can only edit if not active
            ->first();
            
        if (!$conference) {
            return back()->withErrors(['error' => 'Không thể chỉnh sửa hội thảo này hoặc hội thảo không tồn tại.']);
        }
            
        return view('chair.conferences.edit', compact('conference'));
    }

    /**
     * Update conference configuration
     */
    public function update(Request $request, $conferenceId)
    {
        $userEmail = auth()->user()->email;
        
        // Check if user can edit this conference
        $canEdit = DB::table('hoithao')
            ->join('yeucauhoithao', function($join) use ($userEmail) {
                $join->on('hoithao.title', '=', 'yeucauhoithao.title')
                     ->where('yeucauhoithao.chair_email', '=', $userEmail)
                     ->where('yeucauhoithao.status', '=', 'APPROVED');
            })
            ->where('hoithao.conference_id', $conferenceId)
            ->where('hoithao.status', '!=', 'ACTIVE') // Can only edit if not active
            ->exists();
            
        if (!$canEdit) {
            return back()->withErrors(['error' => 'Không thể chỉnh sửa hội thảo này.']);
        }

        $validatedData = $request->validate([
            'description' => 'nullable|string|max:1000',
            'start_date' => 'required|date|after:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'reviewers_per_paper' => 'required|integer|min:2|max:5',
            'submission_deadline' => 'required|date|after:today',
            'review_deadline' => 'required|date|after:submission_deadline',
        ]);

        DB::beginTransaction();
        
        try {
            // Update conference
            DB::table('hoithao')
                ->where('conference_id', $conferenceId)
                ->update([
                    'description' => $validatedData['description'],
                    'start_date' => $validatedData['start_date'],
                    'end_date' => $validatedData['end_date'],
                    'location' => $validatedData['location'],
                    'max_participants' => $validatedData['max_participants'],
                    'reviewers_per_paper' => $validatedData['reviewers_per_paper'],
                    'submission_deadline' => $validatedData['submission_deadline'],
                    'review_deadline' => $validatedData['review_deadline'],
                    'updated_at' => now(),
                ]);

            DB::commit();

            return redirect()->route('chair.conferences.show', $conferenceId)
                ->with('success', 'Cấu hình hội thảo đã được cập nhật thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật hội thảo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Generate acronym from conference name
     */
    private function generateAcronym($name)
    {
        $words = explode(' ', $name);
        $acronym = '';
        foreach ($words as $word) {
            if (strlen(trim($word)) > 0) {
                $acronym .= strtoupper(substr(trim($word), 0, 1));
            }
        }
        return $acronym;
    }
}



