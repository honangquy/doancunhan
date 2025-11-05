# 📱 Flutter Conference App - Starter Project

## 🚀 Quick Setup Commands

```bash
# 1. Create Flutter project
flutter create conference_app
cd conference_app

# 2. Add dependencies (replace pubspec.yaml dependencies section)
# See dependencies in pubspec.yaml below

# 3. Install packages
flutter pub get

# 4. Generate model files
flutter packages pub run build_runner build

# 5. Run app
flutter run
```

---

## 📦 pubspec.yaml

```yaml
name: conference_app
description: HUIT Conference Management Mobile App
version: 1.0.0+1

environment:
  sdk: '>=3.0.0 <4.0.0'

dependencies:
  flutter:
    sdk: flutter
  
  # HTTP & API
  http: ^1.1.0
  dio: ^5.3.2  # Alternative to http (more features)
  
  # State Management
  provider: ^6.1.1
  riverpod: ^2.4.9  # Alternative to provider
  
  # Local Storage
  shared_preferences: ^2.2.2
  hive: ^2.2.3
  hive_flutter: ^1.1.0
  
  # JSON Serialization
  json_annotation: ^4.8.1
  freezed_annotation: ^2.4.1
  
  # UI Components
  cupertino_icons: ^1.0.2
  flutter_svg: ^2.0.7
  cached_network_image: ^3.3.0
  
  # File Handling
  file_picker: ^6.1.1
  path_provider: ^2.1.1
  
  # Notifications
  flutter_local_notifications: ^16.3.0
  
  # Navigation
  go_router: ^12.1.3
  
  # Utils
  intl: ^0.19.0
  url_launcher: ^6.2.1

dev_dependencies:
  flutter_test:
    sdk: flutter
  
  # Code Generation
  build_runner: ^2.4.7
  json_serializable: ^6.7.1
  freezed: ^2.4.6
  
  # Linting
  flutter_lints: ^3.0.0

flutter:
  uses-material-design: true
  
  assets:
    - assets/images/
    - assets/icons/
```

---

## 🏗️ Project Structure

```
conference_app/
├── lib/
│   ├── core/
│   │   ├── constants/
│   │   │   ├── api_constants.dart
│   │   │   ├── app_constants.dart
│   │   │   └── colors.dart
│   │   ├── errors/
│   │   │   ├── exceptions.dart
│   │   │   └── failures.dart
│   │   └── utils/
│   │       ├── date_utils.dart
│   │       └── validators.dart
│   ├── data/
│   │   ├── models/
│   │   │   ├── conference.dart
│   │   │   ├── paper.dart
│   │   │   ├── user.dart
│   │   │   └── assignment.dart
│   │   ├── repositories/
│   │   │   ├── auth_repository.dart
│   │   │   ├── conference_repository.dart
│   │   │   └── paper_repository.dart
│   │   └── services/
│   │       ├── api_service.dart
│   │       ├── storage_service.dart
│   │       └── notification_service.dart
│   ├── presentation/
│   │   ├── pages/
│   │   │   ├── auth/
│   │   │   │   ├── login_page.dart
│   │   │   │   └── register_page.dart
│   │   │   ├── home/
│   │   │   │   └── home_page.dart
│   │   │   ├── conferences/
│   │   │   │   ├── conference_list_page.dart
│   │   │   │   └── conference_detail_page.dart
│   │   │   └── papers/
│   │   │       ├── paper_list_page.dart
│   │   │       └── paper_submission_page.dart
│   │   ├── widgets/
│   │   │   ├── common/
│   │   │   │   ├── app_bar.dart
│   │   │   │   ├── loading_widget.dart
│   │   │   │   └── error_widget.dart
│   │   │   └── conference/
│   │   │       └── conference_card.dart
│   │   └── providers/
│   │       ├── auth_provider.dart
│   │       ├── conference_provider.dart
│   │       └── paper_provider.dart
│   └── main.dart
└── assets/
    ├── images/
    └── icons/
```

---

## 🔧 Core Configuration Files

### 1. API Constants
```dart
// lib/core/constants/api_constants.dart
class ApiConstants {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  static const String productionUrl = 'https://yourdomain.com/api';
  
  // Auth endpoints
  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String profile = '/auth/profile';
  static const String logout = '/auth/logout';
  
  // Conference endpoints
  static const String conferences = '/conferences';
  static const String myConferences = '/my-conferences';
  static const String facilities = '/facilities';
  
  // Paper endpoints
  static const String papers = '/papers';
  static const String myPapers = '/my-papers';
  
  // Review endpoints
  static const String myAssignments = '/my-assignments';
  static const String reviews = '/reviews';
  
  // Notification endpoints
  static const String notifications = '/notifications';
  
  // Timeout durations
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
}
```

### 2. App Colors
```dart
// lib/core/constants/colors.dart
import 'package:flutter/material.dart';

class AppColors {
  // Primary colors
  static const Color primary = Color(0xFF1976D2);
  static const Color primaryDark = Color(0xFF1565C0);
  static const Color primaryLight = Color(0xFF42A5F5);
  
  // Secondary colors  
  static const Color secondary = Color(0xFFFF9800);
  static const Color secondaryDark = Color(0xFFEF6C00);
  static const Color secondaryLight = Color(0xFFFFB74D);
  
  // Status colors
  static const Color success = Color(0xFF4CAF50);
  static const Color warning = Color(0xFFFF9800);
  static const Color error = Color(0xFFE53935);
  static const Color info = Color(0xFF2196F3);
  
  // Neutral colors
  static const Color white = Color(0xFFFFFFFF);
  static const Color black = Color(0xFF000000);
  static const Color grey100 = Color(0xFFF5F5F5);
  static const Color grey300 = Color(0xFFE0E0E0);
  static const Color grey600 = Color(0xFF757575);
  static const Color grey800 = Color(0xFF424242);
  
  // Conference status colors
  static const Color activeConference = Color(0xFF4CAF50);
  static const Color pendingConference = Color(0xFFFF9800);
  static const Color closedConference = Color(0xFF9E9E9E);
}
```

### 3. Custom Exceptions
```dart
// lib/core/errors/exceptions.dart
class ApiException implements Exception {
  final String message;
  final int? statusCode;
  
  ApiException(this.message, {this.statusCode});
  
  @override
  String toString() => 'ApiException: $message';
}

class NetworkException extends ApiException {
  NetworkException(String message) : super(message);
}

class UnauthorizedException extends ApiException {
  UnauthorizedException() : super('Unauthorized access', statusCode: 401);
}

class ValidationException extends ApiException {
  final Map<String, List<String>> errors;
  
  ValidationException(this.errors) 
    : super('Validation failed', statusCode: 422);
}

class ServerException extends ApiException {
  ServerException() : super('Server error occurred', statusCode: 500);
}
```

---

## 📱 Core Service Classes

### 1. API Service (Using Dio)
```dart
// lib/data/services/api_service.dart
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../../core/constants/api_constants.dart';
import '../../core/errors/exceptions.dart';
import 'storage_service.dart';

class ApiService {
  late Dio _dio;
  final StorageService _storageService = StorageService();
  
  ApiService() {
    _dio = Dio(BaseOptions(
      baseUrl: ApiConstants.baseUrl,
      connectTimeout: ApiConstants.connectTimeout,
      receiveTimeout: ApiConstants.receiveTimeout,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));
    
    _setupInterceptors();
  }
  
  void _setupInterceptors() {
    // Request interceptor - Add auth token
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storageService.getAuthToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        
        if (kDebugMode) {
          print('🚀 Request: ${options.method} ${options.path}');
          print('📤 Data: ${options.data}');
        }
        
        handler.next(options);
      },
      
      onResponse: (response, handler) {
        if (kDebugMode) {
          print('✅ Response: ${response.statusCode} ${response.requestOptions.path}');
        }
        handler.next(response);
      },
      
      onError: (error, handler) {
        if (kDebugMode) {
          print('❌ Error: ${error.message}');
        }
        
        final exception = _handleError(error);
        handler.reject(DioException(
          requestOptions: error.requestOptions,
          error: exception,
        ));
      },
    ));
  }
  
  ApiException _handleError(DioException error) {
    switch (error.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return NetworkException('Connection timeout');
      
      case DioExceptionType.badResponse:
        final statusCode = error.response?.statusCode;
        final data = error.response?.data;
        
        switch (statusCode) {
          case 401:
            return UnauthorizedException();
          case 422:
            return ValidationException(
              Map<String, List<String>>.from(data['errors'] ?? {})
            );
          case 500:
            return ServerException();
          default:
            return ApiException(
              data['message'] ?? 'Unknown error occurred',
              statusCode: statusCode,
            );
        }
      
      default:
        return NetworkException('Network error occurred');
    }
  }
  
  // Generic methods
  Future<Response> get(String path, {Map<String, dynamic>? queryParameters}) {
    return _dio.get(path, queryParameters: queryParameters);
  }
  
  Future<Response> post(String path, {dynamic data}) {
    return _dio.post(path, data: data);
  }
  
  Future<Response> put(String path, {dynamic data}) {
    return _dio.put(path, data: data);
  }
  
  Future<Response> delete(String path) {
    return _dio.delete(path);
  }
  
  Future<Response> uploadFile(
    String path,
    String filePath,
    String fieldName,
    {Map<String, dynamic>? data}
  ) {
    final formData = FormData.fromMap({
      ...?data,
      fieldName: MultipartFile.fromFileSync(filePath),
    });
    
    return _dio.post(path, data: formData);
  }
}
```

### 2. Storage Service
```dart
// lib/data/services/storage_service.dart
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';

class StorageService {
  static const String _authTokenKey = 'auth_token';
  static const String _userDataKey = 'user_data';
  
  Future<void> saveAuthToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_authTokenKey, token);
  }
  
  Future<String?> getAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_authTokenKey);
  }
  
  Future<void> clearAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_authTokenKey);
  }
  
  Future<void> saveUserData(Map<String, dynamic> userData) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_userDataKey, jsonEncode(userData));
  }
  
  Future<Map<String, dynamic>?> getUserData() async {
    final prefs = await SharedPreferences.getInstance();
    final userDataString = prefs.getString(_userDataKey);
    
    if (userDataString != null) {
      return Map<String, dynamic>.from(jsonDecode(userDataString));
    }
    
    return null;
  }
  
  Future<void> clearUserData() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_userDataKey);
  }
  
  Future<void> clearAll() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
  }
}
```

---

## 🎨 Sample UI Components

### 1. Main App Structure
```dart
// lib/main.dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/constants/colors.dart';
import 'presentation/providers/auth_provider.dart';
import 'presentation/providers/conference_provider.dart';
import 'presentation/pages/auth/login_page.dart';
import 'presentation/pages/home/home_page.dart';

void main() {
  runApp(const ConferenceApp());
}

class ConferenceApp extends StatelessWidget {
  const ConferenceApp({super.key});
  
  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => ConferenceProvider()),
      ],
      child: MaterialApp(
        title: 'HUIT Conference',
        theme: ThemeData(
          primarySwatch: Colors.blue,
          primaryColor: AppColors.primary,
          colorScheme: ColorScheme.fromSeed(
            seedColor: AppColors.primary,
          ),
          appBarTheme: const AppBarTheme(
            backgroundColor: AppColors.primary,
            foregroundColor: Colors.white,
            elevation: 2,
          ),
        ),
        home: const AuthWrapper(),
        debugShowCheckedModeBanner: false,
      ),
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});
  
  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, authProvider, _) {
        if (authProvider.isAuthenticated) {
          return const HomePage();
        }
        return const LoginPage();
      },
    );
  }
}
```

### 2. Login Page
```dart
// lib/presentation/pages/auth/login_page.dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/common/loading_widget.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});
  
  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Form(
            key: _formKey,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Logo/Title
                const Text(
                  'HUIT Conference',
                  style: TextStyle(
                    fontSize: 32,
                    fontWeight: FontWeight.bold,
                    color: AppColors.primary,
                  ),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 48),
                
                // Email field
                TextFormField(
                  controller: _emailController,
                  keyboardType: TextInputType.emailAddress,
                  decoration: const InputDecoration(
                    labelText: 'Email',
                    prefixIcon: Icon(Icons.email),
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter your email';
                    }
                    if (!value.contains('@')) {
                      return 'Please enter a valid email';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),
                
                // Password field
                TextFormField(
                  controller: _passwordController,
                  obscureText: _obscurePassword,
                  decoration: InputDecoration(
                    labelText: 'Password',
                    prefixIcon: const Icon(Icons.lock),
                    suffixIcon: IconButton(
                      onPressed: () {
                        setState(() {
                          _obscurePassword = !_obscurePassword;
                        });
                      },
                      icon: Icon(
                        _obscurePassword ? Icons.visibility : Icons.visibility_off,
                      ),
                    ),
                    border: const OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter your password';
                    }
                    if (value.length < 6) {
                      return 'Password must be at least 6 characters';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 24),
                
                // Login button
                Consumer<AuthProvider>(
                  builder: (context, authProvider, _) {
                    if (authProvider.isLoading) {
                      return const LoadingWidget();
                    }
                    
                    return ElevatedButton(
                      onPressed: () => _handleLogin(context),
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: const Text(
                        'Login',
                        style: TextStyle(fontSize: 16),
                      ),
                    );
                  },
                ),
                const SizedBox(height: 16),
                
                // Register link
                TextButton(
                  onPressed: () {
                    // Navigate to register page
                  },
                  child: const Text('Don\'t have an account? Register'),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
  
  Future<void> _handleLogin(BuildContext context) async {
    if (!_formKey.currentState!.validate()) return;
    
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    
    final success = await authProvider.login(
      _emailController.text.trim(),
      _passwordController.text,
    );
    
    if (!success && context.mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Login failed. Please check your credentials.'),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }
  
  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }
}
```

---

## 🎯 Next Steps for Development

### Phase 1: Basic Setup (Week 1)
1. **Create Flutter project** with above structure
2. **Implement authentication** (login/register/profile)
3. **Test API connectivity** with health check and conferences list
4. **Build basic navigation** between screens

### Phase 2: Core Features (Week 2)
1. **Conference listing and details**
2. **Paper submission flow**
3. **File upload/download functionality**
4. **Basic notifications**

### Phase 3: Advanced Features (Week 3)
1. **Review assignment system**
2. **Real-time notifications**
3. **Offline data caching**
4. **Advanced UI animations**

### Phase 4: Polish & Deploy (Week 4)
1. **Error handling improvements**
2. **Performance optimization**
3. **Testing (unit & widget tests)**
4. **App store deployment preparation**

---

## 🔧 Development Commands

```bash
# Generate models after changes
flutter packages pub run build_runner build --delete-conflicting-outputs

# Run with hot reload
flutter run --hot

# Build APK
flutter build apk --release

# Build iOS
flutter build ios --release

# Run tests
flutter test

# Analyze code
flutter analyze
```

**🎉 Your Flutter development environment is ready! Start with Phase 1 and gradually build your conference management mobile app.**