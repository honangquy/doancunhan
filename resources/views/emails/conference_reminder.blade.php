<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $recipientName }} - Nhắc lịch hội thảo HUIT</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #ea580c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background: #c2410c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Nhắc lịch hội thảo HUIT</h1>
        </div>
        
        <div class="content">
            {!! $body !!}
        </div>
        
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống quản lý hội thảo HUIT.</p>
            <p>Nếu bạn không muốn nhận email nhắc lịch, vui lòng cập nhật tùy chọn trong tài khoản của bạn.</p>
            <p>&copy; {{ date('Y') }} HUIT Conferences. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
