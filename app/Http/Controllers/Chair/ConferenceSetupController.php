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
        $userId = auth()->user()->user_id;
        
        // Get approved conference requests for current chair
        $approvedRequests = YeuCauHoiThao::where('user_id', $userId)
            ->where('status', 'APPROVED')
            ->whereDoesntHave('conference') // Not yet configured
            ->orderBy('approved_at', 'desc')
            ->get();
            
        // Get already configured conferences
        $configuredConferences = HoiThao::whereHas('conferenceRequest', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('conferenceRequest')->orderBy('conference_id', 'desc')->get();
        
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
            'cfp_url' => 'nullable|url|max:500',
            
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
            'committees' => 'array',
            'committees.*.name' => 'required|string|max:255',
            'committees.*.description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            // Handle banner upload
            $bannerPath = null;
            if ($request->hasFile('banner')) {
                $bannerPath = $request->file('banner')->store('conference-banners', 'public');
            }

            // Create conference
            $conference = HoiThao::create([
                // Basic info
                'title' => $validatedData['conference_name'],
                'conference_name' => $validatedData['conference_name'],
                'acronym' => $validatedData['acronym'],
                'year' => $validatedData['year'],
                'location' => $validatedData['location'],
                'keywords' => $validatedData['keywords'],
                'description' => $validatedData['description'],
                'detailed_description' => $validatedData['detailed_description'],
                'submission_guidelines' => $validatedData['submission_guidelines'],
                'cfp_url' => $validatedData['cfp_url'],
                
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
                'chair_name' => $validatedData['chair_name'],
                'chair_email' => $validatedData['chair_email'],
                
                // Configuration
                'reviewers_per_paper' => $validatedData['reviewers_per_paper'],
                'enable_coi_check' => $validatedData['enable_coi_check'] ?? false,
                
                // System fields
                'banner_path' => $bannerPath,
                'chair_id' => auth()->user()->user_id,
                'status' => 'PENDING_ADMIN_APPROVAL', // Needs admin approval to go live
                'field' => $conferenceRequest->field,
                'level_code' => $conferenceRequest->level_code,
                'faculty_id' => $conferenceRequest->faculty_name,
                'conference_request_id' => $conferenceRequest->request_id,
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
                        'committee_name' => $committee['name'],
                        'description' => $committee['description'] ?? null,
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('chair.conferences.index')
                ->with('success', 'Hội thảo đã được cấu hình thành công. Đang chờ admin phê duyệt để kích hoạt.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Delete uploaded banner if database operation failed
            if ($bannerPath) {
                Storage::disk('public')->delete($bannerPath);
            }
            
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cấu hình hội thảo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show configured conference details
     */
    public function show($conferenceId)
    {
        $conference = HoiThao::with(['conferenceRequest', 'committees'])
            ->where('chair_id', auth()->user()->user_id)
            ->findOrFail($conferenceId);
            
        return view('chair.conferences.show', compact('conference'));
    }

    /**
     * Edit conference configuration (only if not approved by admin yet)
     */
    public function edit($conferenceId)
    {
        $conference = HoiThao::with(['conferenceRequest', 'committees'])
            ->where('chair_id', auth()->user()->user_id)
            ->where('status', '!=', 'ACTIVE') // Can only edit if not active
            ->findOrFail($conferenceId);
            
        return view('chair.conferences.edit', compact('conference'));
    }

    /**
     * Update conference configuration
     */
    public function update(Request $request, $conferenceId)
    {
        $conference = HoiThao::where('chair_id', auth()->user()->user_id)
            ->where('status', '!=', 'ACTIVE') // Can only edit if not active
            ->findOrFail($conferenceId);

        $validatedData = $request->validate([
            'conference_name' => 'required|string|max:255',
            'conference_date' => 'required|date|after:today',
            'reviewers_per_paper' => 'required|integer|min:1|max:10',
            'submission_deadline' => 'required|date|after:today',
            'review_deadline' => 'required|date|after:submission_deadline',
            'camera_ready_deadline' => 'required|date|after:review_deadline',
            'result_announcement_deadline' => 'required|date|after:camera_ready_deadline',
            'enable_coi_check' => 'boolean',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'committees' => 'array',
            'committees.*.name' => 'required|string|max:255',
            'committees.*.description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            // Handle banner upload
            if ($request->hasFile('banner')) {
                // Delete old banner
                if ($conference->banner_path) {
                    Storage::disk('public')->delete($conference->banner_path);
                }
                $validatedData['banner_path'] = $request->file('banner')->store('conference-banners', 'public');
            }

            // Update conference
            $conference->update([
                'conference_name' => $validatedData['conference_name'],
                'conference_acronym' => $this->generateAcronym($validatedData['conference_name']),
                'conference_date' => $validatedData['conference_date'],
                'submission_deadline' => $validatedData['submission_deadline'],
                'review_deadline' => $validatedData['review_deadline'],
                'camera_ready_deadline' => $validatedData['camera_ready_deadline'],
                'result_announcement_deadline' => $validatedData['result_announcement_deadline'],
                'reviewers_per_paper' => $validatedData['reviewers_per_paper'],
                'enable_coi_check' => $validatedData['enable_coi_check'] ?? false,
                'banner_path' => $validatedData['banner_path'] ?? $conference->banner_path,
                'updated_at' => now(),
            ]);

            // Update committees
            if (isset($validatedData['committees'])) {
                // Delete existing committees
                $conference->committees()->delete();
                
                // Create new committees
                foreach ($validatedData['committees'] as $committee) {
                    TieuBan::create([
                        'conference_id' => $conference->conference_id,
                        'committee_name' => $committee['name'],
                        'description' => $committee['description'] ?? null,
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('chair.conferences.show', $conference->conference_id)
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