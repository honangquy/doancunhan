# 🧪 PowerShell API Test Script
# HUIT Conference Management System - Phase 3

# Base URL
$BASE_URL = "http://localhost/qly_hthao/qlyhoithao/public/api"

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "🧪 HUIT CONFERENCE API TEST SUITE" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: Health Check
Write-Host "1️⃣ Testing Health Check..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/health" -Method GET
    Write-Host "✅ Health Check: " -ForegroundColor Green -NoNewline
    Write-Host $response.message
    Write-Host ""
} catch {
    Write-Host "❌ Health Check Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 2: List Conferences
Write-Host "2️⃣ Testing List Conferences..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/conferences" -Method GET
    Write-Host "✅ List Conferences: Found " -ForegroundColor Green -NoNewline
    Write-Host "$($response.data.total) conferences"
    foreach($conf in $response.data.data) {
        Write-Host "   - ID: $($conf.conference_id) | $($conf.title) | Status: $($conf.status)" -ForegroundColor Gray
    }
    Write-Host ""
} catch {
    Write-Host "❌ List Conferences Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 3: Login as Admin
Write-Host "3️⃣ Testing Login (Admin)..." -ForegroundColor Yellow
try {
    $loginData = @{
        email = "admin@huit.edu.vn"
        password = "admin123"
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $token = $response.data.token
    Write-Host "✅ Login Success!" -ForegroundColor Green
    Write-Host "   User: $($response.data.user.full_name)" -ForegroundColor Gray
    Write-Host "   Email: $($response.data.user.email)" -ForegroundColor Gray
    Write-Host "   Roles: $($response.data.user.roles -join ', ')" -ForegroundColor Gray
    Write-Host "   Token: $($token.Substring(0, 50))..." -ForegroundColor Gray
    Write-Host ""
} catch {
    Write-Host "❌ Login Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
    exit
}

# Test 4: Get Profile (Protected)
Write-Host "4️⃣ Testing Get Profile (Protected)..." -ForegroundColor Yellow
try {
    $headers = @{
        "Authorization" = "Bearer $token"
    }
    $response = Invoke-RestMethod -Uri "$BASE_URL/auth/profile" -Method GET -Headers $headers
    Write-Host "✅ Get Profile Success!" -ForegroundColor Green
    Write-Host "   Name: $($response.data.full_name)" -ForegroundColor Gray
    Write-Host "   Faculty: $($response.data.khoa.faculty_name)" -ForegroundColor Gray
    Write-Host ""
} catch {
    Write-Host "❌ Get Profile Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 5: Get Conference Details
Write-Host "5️⃣ Testing Get Conference Details..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/conferences/1" -Method GET
    Write-Host "✅ Get Conference Details Success!" -ForegroundColor Green
    Write-Host "   Title: $($response.data.title)" -ForegroundColor Gray
    Write-Host "   Year: $($response.data.year)" -ForegroundColor Gray
    Write-Host "   Status: $($response.data.status)" -ForegroundColor Gray
    Write-Host "   Total Tracks: $($response.statistics.total_tracks)" -ForegroundColor Gray
    Write-Host "   Total Papers: $($response.statistics.total_papers)" -ForegroundColor Gray
    Write-Host ""
} catch {
    Write-Host "❌ Get Conference Details Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 6: Get Conference Statistics
Write-Host "6️⃣ Testing Get Conference Statistics..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/conferences/1/statistics" -Method GET
    Write-Host "✅ Get Conference Statistics Success!" -ForegroundColor Green
    Write-Host "   Submission open: $($response.data.conference_info.is_submission_open)" -ForegroundColor Gray
    Write-Host "   Review open: $($response.data.conference_info.is_review_open)" -ForegroundColor Gray
    Write-Host "   Days until submission: $($response.data.deadlines.days_until_submission)" -ForegroundColor Gray
    Write-Host ""
} catch {
    Write-Host "❌ Get Conference Statistics Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 7: List Tracks (Protected)
Write-Host "7️⃣ Testing List Tracks..." -ForegroundColor Yellow
try {
    $headers = @{
        "Authorization" = "Bearer $token"
    }
    $response = Invoke-RestMethod -Uri "$BASE_URL/conferences/1/tracks" -Method GET -Headers $headers
    Write-Host "✅ List Tracks Success! Found $($response.data.Count) tracks" -ForegroundColor Green
    foreach($track in $response.data) {
        Write-Host "   - ID: $($track.track_id) | $($track.track_name) | Papers: $($track.bai_baos_count)" -ForegroundColor Gray
    }
    Write-Host ""
} catch {
    Write-Host "❌ List Tracks Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 8: Create Conference (Admin)
Write-Host "8️⃣ Testing Create Conference (Admin)..." -ForegroundColor Yellow
try {
    $headers = @{
        "Authorization" = "Bearer $token"
    }
    $newConference = @{
        title = "Test Conference 2026"
        year = 2026
        start_date = "2026-12-01"
        end_date = "2026-12-03"
        deadline_submission = "2026-10-15"
        deadline_review = "2026-11-01"
        deadline_camera_ready = "2026-11-20"
        level_code = "NATIONAL"
        faculty_id = 1
        status = "OPEN"
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri "$BASE_URL/conferences" -Method POST -Body $newConference -ContentType "application/json" -Headers $headers
    Write-Host "✅ Create Conference Success!" -ForegroundColor Green
    Write-Host "   Conference ID: $($response.data.conference_id)" -ForegroundColor Gray
    Write-Host "   Title: $($response.data.title)" -ForegroundColor Gray
    Write-Host ""
} catch {
    Write-Host "❌ Create Conference Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 9: Get My Conferences
Write-Host "9️⃣ Testing Get My Conferences..." -ForegroundColor Yellow
try {
    $headers = @{
        "Authorization" = "Bearer $token"
    }
    $response = Invoke-RestMethod -Uri "$BASE_URL/my-conferences" -Method GET -Headers $headers
    Write-Host "✅ Get My Conferences Success! Found $($response.data.Count) conferences" -ForegroundColor Green
    Write-Host ""
} catch {
    Write-Host "❌ Get My Conferences Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 10: Login as Chair
Write-Host "🔟 Testing Login as Chair..." -ForegroundColor Yellow
try {
    $loginData = @{
        email = "chair1@huit.edu.vn"
        password = "password123"
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    $chairToken = $response.data.token
    Write-Host "✅ Chair Login Success!" -ForegroundColor Green
    Write-Host "   User: $($response.data.user.full_name)" -ForegroundColor Gray
    Write-Host "   Roles: $($response.data.user.roles -join ', ')" -ForegroundColor Gray
    Write-Host ""
} catch {
    Write-Host "❌ Chair Login Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Test 11: List Conference Requests
Write-Host "1️⃣1️⃣ Testing List Conference Requests..." -ForegroundColor Yellow
try {
    $headers = @{
        "Authorization" = "Bearer $token"
    }
    $response = Invoke-RestMethod -Uri "$BASE_URL/conference-requests" -Method GET -Headers $headers
    Write-Host "✅ List Conference Requests Success! Found $($response.data.total) requests" -ForegroundColor Green
    Write-Host ""
} catch {
    Write-Host "❌ List Conference Requests Failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message
    Write-Host ""
}

# Summary
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "✅ TEST SUITE COMPLETED!" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📊 Test Results:" -ForegroundColor Yellow
Write-Host "   Total Tests: 11" -ForegroundColor Gray
Write-Host "   Base URL: $BASE_URL" -ForegroundColor Gray
Write-Host ""
Write-Host "🔑 Admin Token: $($token.Substring(0, 30))..." -ForegroundColor Gray
Write-Host ""
Write-Host "💡 Use this token for protected endpoints:" -ForegroundColor Yellow
Write-Host '   $headers = @{ "Authorization" = "Bearer ' -NoNewline -ForegroundColor Gray
Write-Host $token -NoNewline -ForegroundColor White
Write-Host '" }' -ForegroundColor Gray
Write-Host ""
Write-Host "📚 Full API Documentation: API_DOCS.md" -ForegroundColor Cyan
Write-Host "🧪 Quick Tests: QUICK_API_TESTS.md" -ForegroundColor Cyan
Write-Host ""
