<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phân công phản biện</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .paper-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .action-button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
        }
        .highlight {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎯 Phân công phản biện</h1>
        <p>Bạn có một nhiệm vụ phản biện mới</p>
    </div>

    <div class="content">
        <p>Xin chào <strong>{{ $reviewerName }}</strong>,</p>

        <p>Bạn vừa được phân công phản biện cho bài báo khoa học. Dưới đây là thông tin chi tiết:</p>

        <div class="paper-info">
            <h3>📄 Thông tin bài báo:</h3>
            <p><strong>Tiêu đề:</strong> {{ $paperTitle }}</p>
            <p><strong>Mã bài báo:</strong> {{ $paperId }}</p>
            <p><strong>Ngày phân công:</strong> {{ $assignedAt->format('d/m/Y H:i') }}</p>
        </div>

        <div class="highlight">
            <h3>⏰ Thời hạn phản biện:</h3>
            <p>Hạn chót hoàn thành phản biện: <strong>{{ $dueDate->format('d/m/Y') }}</strong></p>
            <p><em>Thời gian còn lại: {{ $dueDate->diffForHumans() }}</em></p>
        </div>

        <h3>📋 Các bước tiếp theo:</h3>
        <ol>
            <li><strong>Xác nhận nhận phản biện</strong> - Vui lòng xác nhận bạn có thể thực hiện nhiệm vụ này</li>
            <li><strong>Tải xuống bài báo</strong> - Đọc kỹ nội dung bài báo</li>
            <li><strong>Thực hiện phản biện</strong> - Viết nhận xét và đánh giá</li>
            <li><strong>Nộp kết quả</strong> - Gửi phản biện trước hạn chót</li>
        </ol>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/reviewer/assignments/' . $assignmentId) }}" class="action-button">
                🚀 Bắt đầu phản biện
            </a>
        </div>

        <div class="highlight">
            <h3>❗ Quan trọng:</h3>
            <ul>
                <li>Nếu bạn có xung đột lợi ích, vui lòng báo ngay cho ban tổ chức</li>
                <li>Thông tin bài báo cần được bảo mật tuyệt đối</li>
                <li>Phản biện phải công bằng, khách quan và mang tính xây dựng</li>
            </ul>
        </div>

        <p>Cảm ơn bạn đã đóng góp vào việc nâng cao chất lượng nghiên cứu khoa học!</p>

        <p>Trân trọng,<br>
        <strong>Ban tổ chức hội thảo khoa học</strong></p>
    </div>

    <div class="footer">
        <p>📧 Email này được gửi tự động từ hệ thống quản lý hội thảo</p>
        <p>Nếu có thắc mắc, vui lòng liên hệ: support@hoithao.edu.vn</p>
    </div>
</body>
</html>