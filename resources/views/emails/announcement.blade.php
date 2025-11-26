<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .message {
            background: #f9fafb;
            padding: 20px;
            border-left: 4px solid #ea580c;
            margin: 20px 0;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
            color: #6b7280;
        }
        .conference-name {
            font-weight: bold;
            color: #ea580c;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>
    
    <div class="content">
        <p class="greeting">Xin chào {{ $recipient_name }},</p>
        
        <div class="message">
            {!! nl2br(e($body)) !!}
        </div>
        
        @if(!empty($conference_name))
        <p style="margin-top: 20px;">
            <strong>Hội thảo:</strong> <span class="conference-name">{{ $conference_name }}</span>
        </p>
        @endif
        
        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Ban tổ chức</strong>
        </p>
    </div>
    
    <div class="footer">
        <p>Email này được gửi tự động từ hệ thống HUIT Conferences</p>
        <p>Vui lòng không trả lời email này</p>
    </div>
</body>
</html>
