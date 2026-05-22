# Hướng Dẫn Deploy Dự Án Laravel Lên VPS Bằng Docker & CI/CD (Kế thừa từ `lq_extract`)

Kế thừa toàn bộ hệ thống "chuẩn chỉ" từ `lq_extract`, chúng ta sẽ sử dụng **Docker** để đóng gói Laravel, và dùng **GitHub Actions** để tự động hóa việc deploy mỗi khi code được push lên branch `main`.

---

## 1. Đóng gói dự án Laravel (Dockerfile)

Tạo một `Dockerfile` ở thư mục gốc của dự án Laravel (Sử dụng PHP-FPM / Apache):

```dockerfile
# Sử dụng image PHP 8.2 có sẵn Apache
FROM php:8.2-apache

# Cài đặt các thư viện hệ thống cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy source code vào
COPY . .

# Cài đặt thư viện Laravel
RUN composer install --optimize-autoloader --no-dev

# Cấp quyền cho thư mục storage và cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Đổi Document Root của Apache trỏ vào thư mục public của Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Bật mod_rewrite của Apache (để chạy được routing của Laravel)
RUN a2enmod rewrite

# Mở port 80 (bên trong container)
EXPOSE 80
```

## 2. Thiết lập `docker-compose.yml`

Bắt buộc **tránh xung đột port, container_name và volume** với `lq_extract`.

```yaml
version: '3.8'

services:
  # Container cho Web/Laravel
  laravel_app:
    build: .
    container_name: laravel_web_app  # Đặt tên khác với lq_extractor_app
    restart: always
    ports:
      - "8000:80"  # Map port 8000 của VPS (vì 3000 đã dùng cho lq_extract)
    env_file:
      - .env
    depends_on:
      - laravel_db

  # Container cho Database riêng của dự án Laravel
  laravel_db:
    image: mysql:8.0
    container_name: laravel_mysql_db # Khác với lq_extractor_db
    restart: always
    environment:
      MYSQL_DATABASE: laravel_database
      MYSQL_USER: admin
      MYSQL_PASSWORD: mysecretpassword
      MYSQL_ROOT_PASSWORD: rootsecretpassword
    volumes:
      - laravel_db_data:/var/lib/mysql

volumes:
  laravel_db_data: # Khác volume postgres_data
```

## 3. Cấu hình file `.env` của Laravel

Kết nối DB trỏ thẳng đến tên service trong `docker-compose`:
```env
DB_CONNECTION=mysql
DB_HOST=laravel_db       # Sử dụng tên service trong docker-compose làm host
DB_PORT=3306
DB_DATABASE=laravel_database
DB_USERNAME=admin
DB_PASSWORD=mysecretpassword
```

## 4. Tự động hóa Deploy với GitHub Actions (CI/CD)

Giống hệt như `lq_extract`, chúng ta sẽ setup workflow để tự động vào VPS pull code và build Docker mỗi khi có thay đổi.

- Tạo file `.github/workflows/deploy.yml` trong project Laravel:

```yaml
name: Deploy Laravel to VPS

on:
  push:
    branches: [ "main" ]

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - name: SSH into VPS and Deploy
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USERNAME }}
          key: ${{ secrets.SERVER_SSH_KEY }}
          script: |
            cd /var/www/my_laravel_project # Sửa đường dẫn vào thư mục Laravel trên VPS
            
            # Reset và kéo code mới
            git reset --hard
            git pull origin main
            
            # Gỡ bỏ container cũ tránh lỗi "Conflict name"
            docker compose down
            
            # Build và chạy lại từ đầu
            docker compose up -d --build
            
            # Chạy migrate cho Laravel sau khi container bật lên
            docker exec laravel_web_app php artisan migrate --force
            
            # Tùy chọn: Xóa cache cũ của Laravel
            docker exec laravel_web_app php artisan optimize:clear
            
            # Dọn dẹp các rác image dư thừa
            docker image prune -f
```

*(Nhớ cấu hình các GitHub Secrets `SERVER_HOST`, `SERVER_USERNAME`, `SERVER_SSH_KEY` trong Settings của repository Laravel nhé).*

---

## Các Lưu Ý Vàng Khi Chạy 2 Dự Án Song Song

> [!WARNING]
> Những điểm "sống còn" để 2 dự án Laravel và Next.js không "đá" nhau trên cùng 1 VPS.

### 1. Dùng Nginx Reverse Proxy làm "Cửa ngõ"
Vì người dùng sẽ truy cập qua tên miền (vd: `app1.com` và `app2.com`), bạn cần cài Nginx trực tiếp trên VPS (không qua Docker) để làm Reverse Proxy đứng ra hứng traffic và điều phối.
- Cấu hình Nginx cho `lq_extract` (`app1.com`) -> `proxy_pass http://localhost:3000;`
- Cấu hình Nginx cho `Laravel Project` (`app2.com`) -> `proxy_pass http://localhost:8000;`

### 2. Tối ưu RAM (Đừng quên Swap)
VPS đang cõng cả Next.js + Postgres và PHP + MySQL. Tổng cộng 4 container khá tốn RAM. **Bắt buộc** phải tạo Swap memory (RAM ảo) khoảng 2GB - 4GB trên VPS để MySQL hoặc Postgres không bị OOM (Out of Memory) tự động sập giữa chừng.

### 3. Xử lý Cronjob (Schedule) cho Laravel
Nếu dự án Laravel có các tác vụ định kỳ chạy ngầm (Schedule), thay vì cài trên OS VPS, bạn thêm cronjob vào VPS (`crontab -e`) để nó chọc lệnh vào container:
```bash
* * * * * docker exec laravel_web_app php artisan schedule:run >> /dev/null 2>&1
```
