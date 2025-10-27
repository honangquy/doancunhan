<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.favicon')
    <title>Notification Test</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">🧪 Notification System Test</h1>
        
        <!-- Include the notification component -->
        @include('components.notification')
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Test Buttons</h2>
            <div class="space-x-4">
                <button onclick="showSuccess('Success!', 'This is a success message')" 
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Test Success
                </button>
                <button onclick="showError('Error!', 'This is an error message')" 
                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Test Error
                </button>
                <button onclick="showWarning('Warning!', 'This is a warning message')" 
                        class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                    Test Warning
                </button>
                <button onclick="showInfo('Info!', 'This is an info message')" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Test Info
                </button>
            </div>
        </div>
    </div>
</body>
</html>