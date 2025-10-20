@component('mail::message')
# Yêu cầu Tạo Hội thảo Bị Từ chối

Chào {{ $user->full_name ?? $user->name }},

Xin thông báo rằng yêu cầu tạo hội thảo của bạn đã bị từ chối.

## Thông tin Hội thảo

- **Tên hội thảo:** {{ $request->title }}
- **Lĩnh vực:** {{ $request->field }}
- **Cấp độ:** {{ $request->level_code }}
- **Ngày dự kiến:** {{ $request->expected_date->format('d/m/Y') }}

## Lý do Từ chối

```
{{ $reason }}
```

## Bước Tiếp Theo

Nếu bạn muốn có thêm thông tin chi tiết về lý do từ chối hoặc muốn gửi lại yêu cầu với những điều chỉnh, vui lòng liên hệ với quản trị viên.

Chúng tôi sẵn sàng hỗ trợ bạn trong quá trình này.

Trân trọng,  
**Ban Quản lý Hệ thống**
@endcomponent
