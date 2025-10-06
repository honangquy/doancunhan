-- ============================================================
-- HỆ THỐNG QUẢN LÝ HỘI THẢO HUIT - FULL SCHEMA (MySQL 8)
-- ============================================================

CREATE DATABASE IF NOT EXISTS quanly_hoithao
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;
USE quanly_hoithao;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ======================
-- 1. Lookup tables
-- ======================
CREATE TABLE TrangThaiBaiBao (
  status_code VARCHAR(30) PRIMARY KEY,
  status_name VARCHAR(100) NOT NULL
);

CREATE TABLE GiaTriBidding (
  bidding_code VARCHAR(20) PRIMARY KEY,
  bidding_name VARCHAR(50) NOT NULL,
  score INT NOT NULL
);

CREATE TABLE LoaiCOI (
  coi_code VARCHAR(30) PRIMARY KEY,
  coi_name VARCHAR(100) NOT NULL
);

CREATE TABLE LoaiVaiTro (
  role_code VARCHAR(20) PRIMARY KEY,
  role_name VARCHAR(100) NOT NULL
);

CREATE TABLE CapHoiThao (
  level_code VARCHAR(20) PRIMARY KEY,
  level_name VARCHAR(50) NOT NULL
);

CREATE TABLE TrangThaiPhanCong (
  status_code VARCHAR(20) PRIMARY KEY
);

CREATE TABLE LoaiKhuyenNghi (
  recommendation_code VARCHAR(20) PRIMARY KEY,
  recommendation_name VARCHAR(50) NOT NULL
);

-- ======================
-- 2. Khoa & Người dùng
-- ======================
CREATE TABLE Khoa (
  faculty_id INT AUTO_INCREMENT PRIMARY KEY,
  faculty_code VARCHAR(50) UNIQUE,
  faculty_name VARCHAR(200) NOT NULL
);

CREATE TABLE NguoiDung (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(200) NOT NULL,
  is_student TINYINT(1) NOT NULL DEFAULT 0,
  faculty_id INT NULL,
  organization VARCHAR(255),
  locked TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (faculty_id) REFERENCES Khoa(faculty_id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE VaiTroNguoiDung (
  user_role_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  role_code VARCHAR(20) NOT NULL,
  conference_id INT NULL,
  UNIQUE KEY uq_user_role (user_id, role_code, conference_id),
  FOREIGN KEY (user_id) REFERENCES NguoiDung(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (role_code) REFERENCES LoaiVaiTro(role_code)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ======================
-- 3. Yêu cầu tổ chức hội thảo
-- ======================
CREATE TABLE YeuCauHoiThao (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  field VARCHAR(255),
  level_code ENUM('KHOA','TRUONG') NOT NULL,
  expected_date DATE,
  objective VARCHAR(500),
  proposal_file VARCHAR(255) NOT NULL,
  status ENUM('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  approver_id INT NULL,
  approval_note VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  approved_at DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES NguoiDung(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (approver_id) REFERENCES NguoiDung(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

-- ======================
-- 4. Hội thảo, Tiểu ban, Thông báo
-- ======================
CREATE TABLE HoiThao (
  conference_id INT AUTO_INCREMENT PRIMARY KEY,
  parent_id INT NULL,
  level_code VARCHAR(20) NOT NULL,
  faculty_id INT NULL,
  title VARCHAR(255) NOT NULL,
  year SMALLINT NOT NULL,
  start_date DATE,
  end_date DATE,
  deadline_submission DATE,
  deadline_review DATE,
  deadline_camera_ready DATE,
  status VARCHAR(50),
  FOREIGN KEY (parent_id) REFERENCES HoiThao(conference_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (level_code) REFERENCES CapHoiThao(level_code)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (faculty_id) REFERENCES Khoa(faculty_id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE TieuBan (
  track_id INT AUTO_INCREMENT PRIMARY KEY,
  conference_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  chair_id INT NULL,
  FOREIGN KEY (conference_id) REFERENCES HoiThao(conference_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (chair_id) REFERENCES NguoiDung(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE ThongBao (
  announcement_id INT AUTO_INCREMENT PRIMARY KEY,
  conference_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content LONGTEXT NOT NULL,
  audience ENUM('ALL','AUTHORS','REVIEWERS') NOT NULL DEFAULT 'ALL',
  created_by INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conference_id) REFERENCES HoiThao(conference_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (created_by) REFERENCES NguoiDung(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ======================
-- 5. Bài báo, phiên bản, tác giả
-- ======================
CREATE TABLE BaiBao (
  paper_id INT AUTO_INCREMENT PRIMARY KEY,
  conference_id INT NOT NULL,
  track_id INT NULL,
  submitter_id INT NOT NULL,
  title VARCHAR(500) NOT NULL,
  abstract LONGTEXT,
  current_version_id INT NULL,
  status_code VARCHAR(30) NOT NULL DEFAULT 'SUBMITTED',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conference_id) REFERENCES HoiThao(conference_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (track_id) REFERENCES TieuBan(track_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (submitter_id) REFERENCES NguoiDung(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (status_code) REFERENCES TrangThaiBaiBao(status_code)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE PhienBanBaiBao (
  version_id INT AUTO_INCREMENT PRIMARY KEY,
  paper_id INT NOT NULL,
  version_no INT NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  note VARCHAR(255),
  UNIQUE KEY uk_version (paper_id, version_no),
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE TacGiaBaiBao (
  paper_id INT NOT NULL,
  user_id INT NOT NULL,
  author_order INT NOT NULL,
  is_contact TINYINT(1) NOT NULL DEFAULT 0,
  organization VARCHAR(255),
  PRIMARY KEY (paper_id, user_id),
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (user_id) REFERENCES NguoiDung(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ======================
-- 6. Reviewer: chuyên môn & bidding
-- ======================
CREATE TABLE ChuyenMonReviewer (
  user_id INT NOT NULL,
  track_id INT NOT NULL,
  expertise_level TINYINT NOT NULL,
  PRIMARY KEY (user_id, track_id),
  FOREIGN KEY (user_id) REFERENCES NguoiDung(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (track_id) REFERENCES TieuBan(track_id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Bidding (
  user_id INT NOT NULL,
  paper_id INT NOT NULL,
  bidding_code VARCHAR(20) NOT NULL,
  note VARCHAR(255),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, paper_id),
  FOREIGN KEY (user_id) REFERENCES NguoiDung(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (bidding_code) REFERENCES GiaTriBidding(bidding_code)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ======================
-- 7. COI (rút gọn)
-- ======================
CREATE TABLE COI (
  coi_id INT AUTO_INCREMENT PRIMARY KEY,
  paper_id INT NOT NULL,
  reviewer_id INT NOT NULL,
  coi_code VARCHAR(30) NOT NULL,
  source_type ENUM('DECLARED','DETECTED') NOT NULL,
  evidence VARCHAR(500),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (reviewer_id) REFERENCES NguoiDung(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (coi_code) REFERENCES LoaiCOI(coi_code)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE XuLyCOI (
  decision_id INT AUTO_INCREMENT PRIMARY KEY,
  coi_id INT NOT NULL,
  chair_id INT NOT NULL,
  decision ENUM('CONFIRMED','REJECTED') NOT NULL,
  note VARCHAR(255),
  decided_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (coi_id) REFERENCES COI(coi_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (chair_id) REFERENCES NguoiDung(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ======================
-- 8. Phân công & Phản biện
-- ======================
CREATE TABLE PhanCongPhanBien (
  assignment_id INT AUTO_INCREMENT PRIMARY KEY,
  paper_id INT NOT NULL,
  reviewer_id INT NOT NULL,
  chair_id INT NULL,
  status_code VARCHAR(20) NOT NULL DEFAULT 'INVITED',
  token CHAR(36) NOT NULL,
  assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deadline DATE,
  UNIQUE KEY uq_token (token),
  UNIQUE KEY uq_assignment (paper_id, reviewer_id),
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (reviewer_id) REFERENCES NguoiDung(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (status_code) REFERENCES TrangThaiPhanCong(status_code)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE PhanBien (
  review_id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL,
  recommendation_code VARCHAR(20) NOT NULL,
  score TINYINT,
  comment_author LONGTEXT,
  comment_chair LONGTEXT,
  submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (assignment_id) REFERENCES PhanCongPhanBien(assignment_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (recommendation_code) REFERENCES LoaiKhuyenNghi(recommendation_code)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ======================
-- 9. Lịch sử, chỉnh sửa, rút bài
-- ======================
CREATE TABLE LichSuTrangThai (
  history_id INT AUTO_INCREMENT PRIMARY KEY,
  paper_id INT NOT NULL,
  from_status VARCHAR(30),
  to_status VARCHAR(30) NOT NULL,
  changed_by INT NULL,
  changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  note VARCHAR(255),
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE YeuCauChinhSua (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  paper_id INT NOT NULL,
  requester_id INT NOT NULL,
  requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deadline DATE,
  note VARCHAR(255),
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (requester_id) REFERENCES NguoiDung(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE RutBaiBao (
  withdrawal_id INT AUTO_INCREMENT PRIMARY KEY,
  paper_id INT NOT NULL,
  author_id INT NOT NULL,
  withdrawn_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reason VARCHAR(255),
  approver_id INT NULL,
  approved_at DATETIME NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (author_id) REFERENCES NguoiDung(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (approver_id) REFERENCES NguoiDung(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

SET FOREIGN_KEY_CHECKS = 1;
