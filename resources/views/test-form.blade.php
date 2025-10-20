<!DOCTYPE html>
<html>
<head>
    <title>Test Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Test Form</h1>
    
    <form id="testForm">
        @csrf
        <input type="text" name="test_field" value="test value" />
        <button type="submit">Submit Test</button>
    </form>

    <div id="result"></div>

    <script>
        document.getElementById('testForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                console.log('Submitting to:', '/test-no-auth-conference');
                
                const response = await fetch('/test-no-auth-conference', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                const result = await response.json();
                console.log('Result:', result);
                
                document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('result').innerHTML = '<pre style="color: red;">Error: ' + error.message + '</pre>';
            }
        });
    </script>
</body>
</html>