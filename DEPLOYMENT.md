# Hướng Dẫn Deploy HUIT Conferences Lên VPS

Tài liệu hướng dẫn chi tiết từng bước để deploy dự án **HUIT Conferences** (Laravel 9) lên VPS sử dụng Docker và tự động hóa bằng GitHub Actions CI/CD. Dự án chạy song song với `lq_extract` trên cùng VPS.

---

## Mục lục

- [Kiến trúc tổng quan](#kiến-trúc-tổng-quan)
- [Bước 1: Chuẩn bị VPS](#bước-1-chuẩn-bị-vps)
- [Bước 2: Clone repo lần đầu](#bước-2-clone-repo-lần-đầu)
- [Bước 3: Tạo file .env production](#bước-3-tạo-file-env-production)
- [Bước 4: Build và chạy Docker lần đầu](#bước-4-build-và-chạy-docker-lần-đầu)
- [Bước 5: Cấu hình Nginx Reverse Proxy](#bước-5-cấu-hình-nginx-reverse-proxy)
- [Bước 6: Cài SSL với Certbot](#bước-6-cài-ssl-với-certbot)
- [Bước 7: Setup GitHub Actions CI/CD](#bước-7-setup-github-actions-cicd)
- [Bước 8: Test toàn bộ pipeline](#bước-8-test-toàn-bộ-pipeline)
- [Quản lý & Vận hành](#quản-lý--vận-hành)
- [Troubleshooting](#troubleshooting)

---

## Kiến trúc tổng quan

```
Internet
   │
   ▼
┌─────────────────────────────────────────────────┐
│  VPS (Ubuntu)                                   │
│                                                 │
│  Nginx (host) ─── Reverse Proxy                 │
│    ├── hoithao.yourdomain.com → localhost:8000   │
│    └── lqextract.yourdomain.com → localhost:3000 │
│                                                 │
│  ┌─── Docker Network: huit ───────────────────┐ │
│  │  huit_web   (Nginx:80 → host:8000)         │ │
│  │     │                                      │ │
│  │     ▼                                      │ │
│  │  huit_app   (PHP-FPM:9000)                 │ │
│  │     │                                      │ │
│  │     ├──▶ huit_db    (MySQL:3306)            │ │
│  │     └──▶ huit_redis (Redis:6379)            │ │
│  └────────────────────────────────────────────┘ │
│                                                 │
│  ┌─── Docker Network: lq_extract ─────────────┐ │
│  │  (Dự án lq_extract đang chạy, port 3000)   │ │
│  └────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

**4 container** của HUIT Conferences:

| Container | Image | Vai trò | Port nội bộ |
| :--- | :--- | :--- | :---: |
| `huit_web` | nginx:alpine | Web server, proxy đến PHP-FPM | 80 → host:8000 |
| `huit_app` | Custom (PHP 8.2-fpm) | Chạy Laravel (PHP-FPM) | 9000 |
| `huit_db` | mysql:8.0 | Cơ sở dữ liệu MySQL | 3306 |
| `huit_redis` | redis:alpine | Cache & Session | 6379 |

---

## Bước 1: Chuẩn bị VPS

> Nếu VPS đã cài Docker, Nginx, Git từ dự án `lq_extract`, bạn có thể bỏ qua các bước con đã hoàn thành.

### 1.1. Cài Docker & Docker Compose

```bash
# Cập nhật hệ thống
sudo apt update && sudo apt upgrade -y

# Cài Docker
curl -fsSL https://get.docker.com | sudo sh

# Thêm user hiện tại vào group docker (không cần sudo khi chạy docker)
sudo usermod -aG docker $USER

# Kiểm tra
docker --version
docker compose version
```

### 1.2. Cài Nginx (nếu chưa có)

```bash
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 1.3. Cài Certbot (cho SSL)

```bash
sudo apt install certbot python3-certbot-nginx -y
```

### 1.4. Tạo Swap Memory (bắt buộc nếu VPS <= 2GB RAM)

Chạy 2 dự án Docker đồng thời (PHP + MySQL + Redis + Next.js + Postgres) rất tốn RAM. Swap giúp tránh OOM crash.

```bash
# Tạo swap 2GB
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile

# Cấu hình để swap tồn tại sau khi reboot
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab

# Kiểm tra
free -h
```

---

## Bước 2: Clone repo lần đầu

```bash
# Tạo thư mục chứa project
sudo mkdir -p /var/www/qlyhoithao
sudo chown -R $USER:$USER /var/www/qlyhoithao

# Clone repo
cd /var/www
git clone https://github.com/honangquy/doancunhan.git qlyhoithao
cd qlyhoithao
```

> Thay `YOUR_USERNAME` bằng username GitHub thực tế của bạn.

---

## Bước 3: Tạo file .env production

File `.env` **không được push lên Git** (đã có trong `.gitignore`), nên phải tạo trực tiếp trên VPS.

```bash
cd /var/www/qlyhoithao
nano .env
```

Paste nội dung sau và **sửa lại các giá trị** cho phù hợp:

```env
APP_NAME="HUIT Conferences"
APP_ENV=production
APP_KEY=base64:GENERATE_A_NEW_KEY_HERE
APP_DEBUG=false
APP_URL=https://hoithao.yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# ---- Database (phải khớp với docker-compose) ----
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=quanly_hoithao
DB_USERNAME=huit_user
DB_PASSWORD=YOUR_STRONG_DB_PASSWORD

# Biến cho MySQL container
MYSQL_USER=huit_user
MYSQL_PASSWORD=YOUR_STRONG_DB_PASSWORD
MYSQL_ROOT_PASSWORD=YOUR_STRONG_ROOT_PASSWORD

# ---- Cache & Session ----
BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# ---- Mail ----
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

# ---- JWT ----
JWT_SECRET=GENERATE_A_NEW_JWT_SECRET
```

### Tạo APP_KEY và JWT_SECRET

Sau khi build Docker lần đầu (Bước 4), chạy:

```bash
docker exec huit_app php artisan key:generate
docker exec huit_app php artisan jwt:secret
```

Hai lệnh trên sẽ tự động ghi giá trị vào file `.env`.

---

## Bước 4: Build và chạy Docker lần đầu

```bash
cd /var/www/qlyhoithao

# Build image và khởi chạy tất cả containers ở chế độ production
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

Quá trình build lần đầu sẽ mất khoảng 3-5 phút (tải image, cài composer, build Vite assets).

### Kiểm tra containers đã chạy

```bash
docker ps
```

Kết quả mong đợi (tất cả đều `Up`):

```
CONTAINER ID   IMAGE              STATUS                    NAMES
xxxxxxxxxxxx   qlyhoithao-app     Up 2 minutes              huit_app
xxxxxxxxxxxx   nginx:alpine       Up 2 minutes              huit_web
xxxxxxxxxxxx   mysql:8.0          Up 2 minutes (healthy)    huit_db
xxxxxxxxxxxx   redis:alpine       Up 2 minutes              huit_redis
```

### Chạy Seeder (nếu cần dữ liệu mẫu)

```bash
docker exec huit_app php artisan db:seed
```

### Test truy cập nội bộ

```bash
curl -I http://localhost:8000
```

Nếu nhận được `HTTP/1.1 200 OK` hoặc `302 Found` → Laravel đang chạy thành công.

---

## Bước 5: Cấu hình Nginx Reverse Proxy

Tạo file cấu hình Nginx trên VPS (host, không phải trong Docker):

```bash
sudo nano /etc/nginx/sites-available/qlyhoithao
```

Paste nội dung sau:

```nginx
server {
    listen 80;
    server_name hoithao.yourdomain.com;    # <-- Thay bằng tên miền thật

    client_max_body_size 64M;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_read_timeout 60s;
        proxy_send_timeout 60s;
    }
}
```

Kích hoạt và kiểm tra:

```bash
# Tạo symlink để kích hoạt
sudo ln -s /etc/nginx/sites-available/qlyhoithao /etc/nginx/sites-enabled/

# Kiểm tra cú pháp
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

Bây giờ truy cập `http://hoithao.yourdomain.com` trên trình duyệt để kiểm tra.

---

## Bước 6: Cài SSL với Certbot

```bash
sudo certbot --nginx -d hoithao.yourdomain.com
```

Certbot sẽ tự động:
- Xin certificate từ Let's Encrypt
- Sửa file Nginx config để redirect HTTP → HTTPS
- Tự động gia hạn certificate (kiểm tra bằng `sudo certbot renew --dry-run`)

---

## Bước 7: Setup GitHub Actions CI/CD

### 7.1. Tạo SSH Key cho deploy (nếu chưa có)

Trên VPS:

```bash
# Tạo SSH key riêng cho deploy
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_deploy -N ""

# Thêm public key vào authorized_keys
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys

# Copy private key (sẽ dùng ở bước sau)
cat ~/.ssh/github_deploy
```

### 7.2. Thêm GitHub Secrets

Vào GitHub repository → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**:

| Secret name | Giá trị |
| :--- | :--- |
| `SERVER_HOST` | IP hoặc domain của VPS (VD: `123.45.67.89`) |
| `SERVER_USERNAME` | Username SSH vào VPS (VD: `root` hoặc `deploy`) |
| `SERVER_SSH_KEY` | Nội dung **private key** từ bước 7.1 (toàn bộ, bao gồm `-----BEGIN...` và `-----END...`) |

> Nếu đã cấu hình Secrets từ dự án `lq_extract` và dùng cùng VPS, có thể tạo Organization Secrets để dùng chung.

### 7.3. File workflow

File `.github/workflows/deploy.yml` đã được tạo sẵn trong project. Kiểm tra lại biến `PROJECT_DIR` trong file đó trỏ đúng đường dẫn trên VPS:

```yaml
PROJECT_DIR="/var/www/qlyhoithao"   # <-- Phải khớp với Bước 2
```

---

## Bước 8: Test toàn bộ pipeline

1. Trên máy local, commit và push code:
   ```bash
   git add .
   git commit -m "Setup CI/CD deployment"
   git push origin main
   ```

2. Vào GitHub → tab **Actions** → kiểm tra workflow **Deploy HUIT Conferences to VPS** có chạy thành công không.

3. Sau khi workflow hoàn thành, truy cập `https://hoithao.yourdomain.com` để xác nhận ứng dụng hoạt động.

---

## Quản lý & Vận hành

### Xem logs

```bash
# Log của Laravel (PHP)
docker logs huit_app --tail 100 -f

# Log của Nginx
docker logs huit_web --tail 100 -f

# Log của MySQL
docker logs huit_db --tail 50

# Log Laravel application (bên trong container)
docker exec huit_app tail -f /var/www/storage/logs/laravel.log
```

### Chạy Artisan commands

```bash
docker exec huit_app php artisan <command>

# Ví dụ:
docker exec huit_app php artisan migrate:status
docker exec huit_app php artisan tinker
docker exec huit_app php artisan optimize:clear
```

### Backup Database

```bash
# Export
docker exec huit_db mysqldump -u root -p'YOUR_ROOT_PASSWORD' quanly_hoithao > backup_$(date +%Y%m%d).sql

# Import
docker exec -i huit_db mysql -u root -p'YOUR_ROOT_PASSWORD' quanly_hoithao < backup_file.sql
```

### Setup Laravel Scheduler (Cron)

Nếu dự án có các tác vụ định kỳ (schedule), thêm cronjob trên VPS:

```bash
crontab -e
```

Thêm dòng:

```
* * * * * docker exec huit_app php artisan schedule:run >> /dev/null 2>&1
```

### Khởi động lại containers

```bash
cd /var/www/qlyhoithao

# Restart nhẹ (không build lại)
docker compose -f docker-compose.yml -f docker-compose.prod.yml restart

# Rebuild hoàn toàn
docker compose -f docker-compose.yml -f docker-compose.prod.yml down
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

---

## Troubleshooting

### Container không start được

```bash
# Kiểm tra log chi tiết
docker logs huit_app 2>&1

# Kiểm tra trạng thái
docker ps -a
```

**Nguyên nhân thường gặp:**
- `.env` chưa được tạo trên VPS
- `DB_HOST` trong `.env` ghi `127.0.0.1` thay vì `db` (tên service Docker)
- MySQL chưa sẵn sàng khi app cố kết nối (entrypoint đã handle, nhưng nếu timeout thì tăng retry)

### Lỗi 502 Bad Gateway trên Nginx

Nginx host không thể kết nối đến container:

```bash
# Kiểm tra port 8000 có đang lắng nghe
sudo ss -tlnp | grep 8000

# Kiểm tra container huit_web có đang chạy
docker ps | grep huit_web
```

### Lỗi Permission denied (storage/logs)

```bash
docker exec huit_app chown -R laravel:laravel /var/www/storage
docker exec huit_app chmod -R 775 /var/www/storage
```

### Vite assets không load (CSS/JS bị 404)

Kiểm tra assets đã được build trong image:

```bash
docker exec huit_app ls -la /var/www/public/build
```

Nếu thư mục rỗng, rebuild lại image:

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

### MySQL bị OOM killed

Kiểm tra swap và RAM:

```bash
free -h
dmesg | grep -i "out of memory"
```

Nếu chưa có swap, quay lại Bước 1.4 để tạo.
