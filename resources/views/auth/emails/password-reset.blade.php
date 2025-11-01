<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px 20px;
            border-radius: 0 0 10px 10px;
        }
        .btn {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .btn:hover {
            background: #218838;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Đặt lại mật khẩu</h1>
        <p>HUIT Conference System</p>
    </div>
    
    <div class="content">
        <p>Xin chào <strong>{{ $user->full_name }}</strong>,</p>
        
        <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại HUIT Conference System.</p>
        
        <p>Để đặt lại mật khẩu, vui lòng nhấn vào nút bên dưới:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="btn">Đặt lại mật khẩu</a>
        </div>
        
        <p>Hoặc copy và dán link sau vào trình duyệt của bạn:</p>
        <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 5px;">
            {{ $resetUrl }}
        </p>
        
        <div class="footer">
            <p><strong>Lưu ý quan trọng:</strong></p>
            <ul>
                <li>Link này sẽ hết hạn sau 24 giờ</li>
                <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                <li>Để bảo mật, không chia sẻ link này với bất kỳ ai</li>
            </ul>
            
            <p>Nếu bạn gặp khó khăn, vui lòng liên hệ với chúng tôi qua email này.</p>
            
            <hr>
            <p><em>Email này được gửi tự động từ HUIT Conference System. Vui lòng không trả lời email này.</em></p>
        </div>
    </div>
</body>
</html>