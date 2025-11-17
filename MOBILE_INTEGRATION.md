# 📱 Hướng dẫn tích hợp API cho Mobile Team

## 🌐 Base URL

**Development:**
```
http://192.168.1.100:8000/api
```
*Thay `192.168.1.100` bằng IP máy backend (chạy `ipconfig getifaddr en0`)*

**Production:**
```
https://api.yourdomain.com/api
```

---

## 🔐 Authentication Flow

### 1. Login để lấy Token

```dart
// Flutter/Dart example
import 'package:dio/dio.dart';

class ApiService {
  final Dio dio = Dio(BaseOptions(
    baseUrl: 'http://192.168.1.100:8000/api',
    connectTimeout: Duration(seconds: 5),
    receiveTimeout: Duration(seconds: 3),
  ));
  
  String? accessToken;
  
  Future<bool> login(String email, String password) async {
    try {
      final response = await dio.post('/auth/login', data: {
        'email': email,
        'password': password,
      });
      
      if (response.data['success']) {
        accessToken = response.data['access_token'];
        // Lưu token vào SharedPreferences/SecureStorage
        await saveToken(accessToken!);
        return true;
      }
      return false;
    } catch (e) {
      print('Login error: $e');
      return false;
    }
  }
  
  // Add token to all requests
  void _addAuthHeader() {
    if (accessToken != null) {
      dio.options.headers['Authorization'] = 'Bearer $accessToken';
    }
  }
}
```

### 2. Lấy danh sách thông báo

```dart
Future<List<Announcement>> getAnnouncements({
  int? conferenceId,
  String? status,
}) async {
  _addAuthHeader();
  
  final queryParams = <String, dynamic>{};
  if (conferenceId != null) queryParams['conference_id'] = conferenceId;
  if (status != null) queryParams['status'] = status;
  
  final response = await dio.get('/announcements', queryParameters: queryParams);
  
  if (response.data['success']) {
    final data = response.data['data'];
    final announcements = (data['announcements'] as List)
        .map((json) => Announcement.fromJson(json))
        .toList();
    return announcements;
  }
  throw Exception('Failed to load announcements');
}
```

### 3. Tạo thông báo (Chair only)

```dart
Future<int?> createAnnouncement({
  required int conferenceId,
  required String title,
  required String content,
  required String audience,
  required List<String> channels,
  required DateTime scheduledAt,
}) async {
  _addAuthHeader();
  
  try {
    final response = await dio.post('/announcements', data: {
      'conference_id': conferenceId,
      'title': title,
      'content': content,
      'audience': audience,
      'channels': channels,
      'scheduled_at': scheduledAt.toIso8601String(),
    });
    
    if (response.data['success']) {
      return response.data['data']['announcement_id'];
    }
  } catch (e) {
    if (e is DioException && e.response?.statusCode == 422) {
      // Validation error
      final errors = e.response?.data['errors'];
      throw ValidationException(errors);
    }
    rethrow;
  }
  return null;
}
```

### 4. Đánh dấu đã đọc

```dart
Future<void> markAsRead(int announcementId) async {
  _addAuthHeader();
  await dio.post('/announcements/$announcementId/mark-read');
}
```

---

## 📦 Data Models

### Announcement Model

```dart
class Announcement {
  final int announcementId;
  final String title;
  final String content;
  final String audience;
  final List<String> channels;
  final String status;
  final DateTime scheduledAt;
  final DateTime? sentAt;
  final DateTime createdAt;
  final int conferenceId;
  final String conferenceName;
  final int? recipientCount;  // Chỉ có với Chair
  final bool? isRead;         // Chỉ có với User
  final DateTime? readAt;     // Chỉ có với User
  final DateTime? receivedAt; // Chỉ có với User

  Announcement({
    required this.announcementId,
    required this.title,
    required this.content,
    required this.audience,
    required this.channels,
    required this.status,
    required this.scheduledAt,
    this.sentAt,
    required this.createdAt,
    required this.conferenceId,
    required this.conferenceName,
    this.recipientCount,
    this.isRead,
    this.readAt,
    this.receivedAt,
  });

  factory Announcement.fromJson(Map<String, dynamic> json) {
    return Announcement(
      announcementId: json['announcement_id'],
      title: json['title'],
      content: json['content'],
      audience: json['audience'],
      channels: List<String>.from(json['channels']),
      status: json['status'],
      scheduledAt: DateTime.parse(json['scheduled_at']),
      sentAt: json['sent_at'] != null ? DateTime.parse(json['sent_at']) : null,
      createdAt: DateTime.parse(json['created_at']),
      conferenceId: json['conference_id'],
      conferenceName: json['conference_name'],
      recipientCount: json['recipient_count'],
      isRead: json['is_read'],
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      receivedAt: json['received_at'] != null ? DateTime.parse(json['received_at']) : null,
    );
  }
}
```

### Statistics Model

```dart
class AnnouncementStatistics {
  final int total;
  final int sent;
  final int scheduled;
  final int failed;

  AnnouncementStatistics({
    required this.total,
    required this.sent,
    required this.scheduled,
    required this.failed,
  });

  factory AnnouncementStatistics.fromJson(Map<String, dynamic> json) {
    return AnnouncementStatistics(
      total: json['total'],
      sent: json['sent'],
      scheduled: json['scheduled'],
      failed: json['failed'],
    );
  }
}
```

---

## 🎨 UI Examples

### Màn hình danh sách (User)

```dart
class AnnouncementListScreen extends StatefulWidget {
  @override
  _AnnouncementListScreenState createState() => _AnnouncementListScreenState();
}

class _AnnouncementListScreenState extends State<AnnouncementListScreen> {
  final ApiService _api = ApiService();
  List<Announcement> announcements = [];
  int unreadCount = 0;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadAnnouncements();
  }

  Future<void> loadAnnouncements() async {
    setState(() => isLoading = true);
    try {
      announcements = await _api.getAnnouncements();
      unreadCount = announcements.where((a) => a.isRead == false).length;
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Lỗi: $e')),
      );
    } finally {
      setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Thông báo'),
        actions: [
          if (unreadCount > 0)
            Padding(
              padding: EdgeInsets.all(16),
              child: Badge(
                label: Text('$unreadCount'),
                child: Icon(Icons.notifications),
              ),
            ),
        ],
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: loadAnnouncements,
              child: ListView.builder(
                itemCount: announcements.length,
                itemBuilder: (context, index) {
                  final announcement = announcements[index];
                  return ListTile(
                    leading: Icon(
                      announcement.isRead == true 
                          ? Icons.mark_email_read 
                          : Icons.mark_email_unread,
                      color: announcement.isRead == true 
                          ? Colors.grey 
                          : Colors.blue,
                    ),
                    title: Text(
                      announcement.title,
                      style: TextStyle(
                        fontWeight: announcement.isRead == true 
                            ? FontWeight.normal 
                            : FontWeight.bold,
                      ),
                    ),
                    subtitle: Text(announcement.conferenceName),
                    trailing: Text(
                      formatDate(announcement.receivedAt ?? announcement.sentAt),
                      style: TextStyle(fontSize: 12),
                    ),
                    onTap: () => openAnnouncementDetail(announcement),
                  );
                },
              ),
            ),
    );
  }

  Future<void> openAnnouncementDetail(Announcement announcement) async {
    await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => AnnouncementDetailScreen(announcement),
      ),
    );
    
    // Mark as read
    if (announcement.isRead == false) {
      await _api.markAsRead(announcement.announcementId);
      await loadAnnouncements(); // Refresh list
    }
  }
}
```

### Màn hình tạo thông báo (Chair)

```dart
class CreateAnnouncementScreen extends StatefulWidget {
  @override
  _CreateAnnouncementScreenState createState() => _CreateAnnouncementScreenState();
}

class _CreateAnnouncementScreenState extends State<CreateAnnouncementScreen> {
  final _formKey = GlobalKey<FormState>();
  final ApiService _api = ApiService();
  
  int? selectedConferenceId;
  String selectedAudience = 'ALL';
  List<String> selectedChannels = ['SYSTEM'];
  DateTime scheduledAt = DateTime.now().add(Duration(hours: 1));
  int recipientCount = 0;
  
  final titleController = TextEditingController();
  final contentController = TextEditingController();
  
  List<Map<String, dynamic>> conferences = [];

  @override
  void initState() {
    super.initState();
    loadConferences();
  }

  Future<void> loadConferences() async {
    final response = await _api.dio.get('/announcements/conferences/list');
    if (response.data['success']) {
      setState(() {
        conferences = List<Map<String, dynamic>>.from(response.data['data']);
      });
    }
  }

  Future<void> previewRecipients() async {
    if (selectedConferenceId == null) return;
    
    final response = await _api.dio.post('/announcements/preview-recipients', data: {
      'conference_id': selectedConferenceId,
      'audience': selectedAudience,
    });
    
    if (response.data['success']) {
      setState(() {
        recipientCount = response.data['data']['count'];
      });
    }
  }

  Future<void> createAnnouncement() async {
    if (!_formKey.currentState!.validate()) return;
    
    try {
      final announcementId = await _api.createAnnouncement(
        conferenceId: selectedConferenceId!,
        title: titleController.text,
        content: contentController.text,
        audience: selectedAudience,
        channels: selectedChannels,
        scheduledAt: scheduledAt,
      );
      
      if (announcementId != null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Đã tạo thông báo #$announcementId')),
        );
        Navigator.pop(context);
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Lỗi: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Tạo thông báo')),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: EdgeInsets.all(16),
          children: [
            DropdownButtonFormField<int>(
              decoration: InputDecoration(labelText: 'Hội thảo'),
              value: selectedConferenceId,
              items: conferences.map((conf) {
                return DropdownMenuItem(
                  value: conf['conference_id'],
                  child: Text(conf['conference_name']),
                );
              }).toList(),
              onChanged: (value) {
                setState(() => selectedConferenceId = value);
                previewRecipients();
              },
              validator: (value) => value == null ? 'Chọn hội thảo' : null,
            ),
            
            TextFormField(
              controller: titleController,
              decoration: InputDecoration(labelText: 'Tiêu đề'),
              validator: (value) => value?.isEmpty ?? true ? 'Nhập tiêu đề' : null,
            ),
            
            TextFormField(
              controller: contentController,
              decoration: InputDecoration(labelText: 'Nội dung'),
              maxLines: 5,
              validator: (value) => value?.isEmpty ?? true ? 'Nhập nội dung' : null,
            ),
            
            DropdownButtonFormField<String>(
              decoration: InputDecoration(labelText: 'Đối tượng'),
              value: selectedAudience,
              items: ['ALL', 'AUTHORS', 'REVIEWERS', 'CHAIRS'].map((audience) {
                return DropdownMenuItem(value: audience, child: Text(audience));
              }).toList(),
              onChanged: (value) {
                setState(() => selectedAudience = value!);
                previewRecipients();
              },
            ),
            
            if (recipientCount > 0)
              Padding(
                padding: EdgeInsets.symmetric(vertical: 8),
                child: Text('Số người nhận: $recipientCount', 
                  style: TextStyle(color: Colors.blue)),
              ),
            
            ListTile(
              title: Text('Thời gian gửi'),
              subtitle: Text(scheduledAt.toString()),
              trailing: Icon(Icons.calendar_today),
              onTap: () async {
                final date = await showDatePicker(
                  context: context,
                  initialDate: scheduledAt,
                  firstDate: DateTime.now(),
                  lastDate: DateTime.now().add(Duration(days: 365)),
                );
                if (date != null) {
                  final time = await showTimePicker(
                    context: context,
                    initialTime: TimeOfDay.fromDateTime(scheduledAt),
                  );
                  if (time != null) {
                    setState(() {
                      scheduledAt = DateTime(
                        date.year, date.month, date.day,
                        time.hour, time.minute,
                      );
                    });
                  }
                }
              },
            ),
            
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: createAnnouncement,
              child: Text('Tạo thông báo'),
            ),
          ],
        ),
      ),
    );
  }
}
```

---

## ⚙️ Dependencies cần thêm (pubspec.yaml)

```yaml
dependencies:
  flutter:
    sdk: flutter
  
  # HTTP client
  dio: ^5.4.0
  
  # State management (optional)
  provider: ^6.1.1
  # hoặc
  bloc: ^8.1.3
  
  # Local storage
  shared_preferences: ^2.2.2
  flutter_secure_storage: ^9.0.0
  
  # Date formatting
  intl: ^0.18.1
```

---

## 🔧 Setup Steps

### 1. Backend (Mac của bạn)

```bash
# Lấy IP máy
ipconfig getifaddr en0
# Output: 192.168.1.100

# Chạy server cho mạng local
php artisan serve --host=0.0.0.0 --port=8000

# Kiểm tra từ mobile browser
# http://192.168.1.100:8000/api
```

### 2. Mobile App

```dart
// lib/config/api_config.dart
class ApiConfig {
  static const String baseUrl = 'http://192.168.1.100:8000/api';
  
  // Hoặc dùng production
  // static const String baseUrl = 'https://api.yourdomain.com/api';
}
```

### 3. Test Connection

```dart
// Test từ mobile app
void testConnection() async {
  try {
    final dio = Dio(BaseOptions(baseUrl: ApiConfig.baseUrl));
    final response = await dio.get('/');
    print('API connected: ${response.data}');
  } catch (e) {
    print('Connection failed: $e');
  }
}
```

---

## 🐛 Troubleshooting

### Lỗi: "Connection refused"
- ✅ Kiểm tra server đang chạy: `php artisan serve --host=0.0.0.0`
- ✅ Kiểm tra IP đúng: `ipconfig getifaddr en0`
- ✅ Cùng mạng WiFi

### Lỗi: "401 Unauthorized"
- ✅ Token hết hạn → Login lại
- ✅ Header thiếu: `Authorization: Bearer {token}`

### Lỗi: "CORS"
- ✅ Backend đã config CORS trong `config/cors.php`
- ✅ Nếu vẫn lỗi, thêm vào `.env`:
  ```
  SANCTUM_STATEFUL_DOMAINS=192.168.1.100:8000
  ```

---

## 📞 Support

**Backend API:** Xem `API_ANNOUNCEMENT_GUIDE.md`  
**Postman Collection:** (TODO: Export từ API routes)  
**Swagger UI:** `http://192.168.1.100:8000/api/documentation`
