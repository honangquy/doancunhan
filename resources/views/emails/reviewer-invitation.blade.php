<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lời mời làm phản biện viên</title>
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
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px 20px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #ea580c;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .conference-info {
            background: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #ea580c;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 HUIT Conferences</h1>
        <h2>Lời mời làm phản biện viên</h2>
    </div>
    
    <div class="content">
        <p>Xin chào,</p>
        
        <p>Tôi là <strong>{{ $chair_name ?? 'Chủ tịch hội thảo' }}</strong>, chủ tịch hội thảo. Tôi xin gửi lời mời bạn tham gia làm phản biện viên cho hội thảo của chúng tôi.</p>
        
        <div class="conference-info">
            <h3>📋 Thông tin hội thảo:</h3>
            <p><strong>Tên hội thảo:</strong> {{ $conference->title ?? 'Hội thảo khoa học' }}</p>
            <p><strong>Năm:</strong> {{ $conference->year ?? date('Y') }}</p>
            @if($conference && $conference->start_date)
                <p><strong>Ngày bắt đầu:</strong> {{ \Carbon\Carbon::parse($conference->start_date)->format('d/m/Y') }}</p>
            @endif
            @if($conference && $conference->end_date)
                <p><strong>Ngày kết thúc:</strong> {{ \Carbon\Carbon::parse($conference->end_date)->format('d/m/Y') }}</p>
            @endif
        </div>

        <p>Với kinh nghiệm và chuyên môn của bạn, chúng tôi tin rằng bạn sẽ đóng góp tích cực cho việc đánh giá chất lượng các bài báo khoa học.</p>

        <p><strong>Quyền lợi khi tham gia:</strong></p>
        <ul>
            <li>Tiếp cận các nghiên cứu mới nhất trong lĩnh vực</li>
            <li>Mở rộng mạng lưới học thuật</li>
            <li>Nhận chứng nhận phản biện viên</li>
            <li>Đóng góp vào sự phát triển của cộng đồng khoa học</li>
        </ul>

        <div style="text-align: center;">
            <a href="{{ $invitation_url }}" class="button">
                ✅ Chấp nhận lời mời
            </a>
        </div>

        <p><strong>Lưu ý quan trọng:</strong></p>
        <ul>
            <li>Lời mời này có hiệu lực trong 7 ngày</li>
            <li>Vui lòng sử dụng chính email này ({{ $email ?? 'email này' }}) để đăng ký/đăng nhập</li>
            <li>Sau khi chấp nhận, bạn sẽ được yêu cầu cập nhật thông tin cá nhân</li>
        </ul>

        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email này.</p>

        <p>Trân trọng,<br>
        <strong>{{ $chair_name ?? 'Chủ tịch hội thảo' }}</strong><br>
        Chủ tịch hội thảo {{ $conference->title ?? 'Hội thảo khoa học' }}</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} HUIT Conferences. Hệ thống quản lý hội thảo khoa học.</p>
        <p>Nếu bạn không mong muốn nhận email này, vui lòng bỏ qua.</p>
    </div>
</body>
</html>