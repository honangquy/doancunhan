<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Join Requests Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <!-- Include notification component -->
    @include('components.notification')
    
    <div class="container mx-auto px-4 py-8" x-data="joinRequestsTestManager()">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold mb-8 text-center">🧪 Admin Join Requests Test</h1>
            
            <!-- Test Buttons -->
            <div class="bg-white rounded-lg p-6 shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-4">Test Process Request Functions</h2>
                <div class="space-y-4">
                    <!-- Simulate real buttons -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">Test Join Request #1</h3>
                            <p class="text-sm text-gray-500">Simulated join request for testing</p>
                        </div>
                        <div class="flex space-x-2">
                            <button @click="processRequest(1, 'approve')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Duyệt
                            </button>
                            <button @click="processRequest(1, 'reject')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Từ chối
                            </button>
                        </div>
                    </div>
                    
                    <!-- Direct notification tests -->
                    <div class="border-t pt-4">
                        <h3 class="font-medium mb-3">Direct Notification Tests</h3>
                        <div class="flex space-x-2 flex-wrap gap-2">
                            <button onclick="showSuccess('Success!', 'Test success notification')" 
                                    class="bg-green-500 text-white px-3 py-1.5 rounded text-sm hover:bg-green-600">
                                Test Success
                            </button>
                            <button onclick="showError('Error!', 'Test error notification')" 
                                    class="bg-red-500 text-white px-3 py-1.5 rounded text-sm hover:bg-red-600">
                                Test Error
                            </button>
                            <button onclick="showWarning('Warning!', 'Test warning notification')" 
                                    class="bg-yellow-500 text-white px-3 py-1.5 rounded text-sm hover:bg-yellow-600">
                                Test Warning
                            </button>
                            <button onclick="showInfo('Info!', 'Test info notification')" 
                                    class="bg-blue-500 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-600">
                                Test Info
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Display -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h2 class="text-xl font-semibold mb-4">Debug Information</h2>
                <div x-data="{ clicked: '' }" class="space-y-2 text-sm">
                    <div>Last clicked: <span x-text="clicked" class="font-mono bg-gray-100 px-2 py-1 rounded"></span></div>
                    <div>Alpine.js Status: <span class="text-green-600 font-semibold">✅ Loaded</span></div>
                    <div>CSRF Token: <span class="font-mono text-xs">{{ substr(csrf_token(), 0, 10) }}...</span></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function joinRequestsTestManager() {
        return {
            processRequest(requestId, action) {
                console.log('processRequest called:', { requestId, action });
                
                const actionText = action === 'approve' ? 'duyệt' : 'từ chối';
                const actionIcon = action === 'approve' ? '✓' : '✗';
                
                // Show confirmation
                if (!confirm(`${actionIcon} Bạn có chắc chắn muốn ${actionText} yêu cầu này?`)) {
                    return;
                }
                
                // Show loading notification
                const loadingId = showInfo(
                    'Đang xử lý...', 
                    `Đang ${actionText} yêu cầu #${requestId}, vui lòng đợi.`,
                    0
                );
                
                // Simulate API call
                setTimeout(() => {
                    // Remove loading notification
                    const container = document.querySelector('[x-data*="notificationManager"]').__x.$data;
                    container.remove(loadingId);
                    
                    // Show success (simulate successful response)
                    showSuccess(
                        '🎉 Test thành công!', 
                        `Đã ${actionText} yêu cầu #${requestId} thành công! (This is just a test)`
                    );
                    
                    console.log('Test completed successfully');
                }, 2000);
            }
        };
    }
    </script>
</body>
</html>