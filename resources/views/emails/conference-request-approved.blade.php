@component('mail::message')
# Yêu cầu Tạo Hội thảo Được Duyệt

Chào {{ $user->full_name ?? $user->name }},

Chúng tôi xin thông báo rằng **yêu cầu tạo hội thảo** của bạn đã được **duyệt thành công**! 🎉

## Thông tin Hội thảo

- **Tên hội thảo:** {{ $request->title }}
- **Lĩnh vực:** {{ $request->field }}
- **Cấp độ:** {{ $request->level_code }}
- **Ngày dự kiến:** {{ $request->expected_date->format('d/m/Y') }}

## Bước Tiếp Theo

Vui lòng hoàn thành cấu hình chi tiết hội thảo của bạn để công khai trên website. Bạn cần điền đầy đủ các thông tin:

- Mô tả chi tiết về hội thảo
- Hướng dẫn nộp bài viết
- Địa điểm tổ chức
- Thông tin liên hệ
- Các từ khóa chính

@component('mail::button', ['url' => $configUrl])
Cấu hình Hội thảo
@endcomponent

Hội thảo của bạn sẽ hiển thị trên website sau khi hoàn thành cấu hình.

Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.

Trân trọng,  
**Ban Quản lý Hệ thống**
@endcomponent
