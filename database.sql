```sql
-- File khởi tạo cấu trúc database cho Link Manager Dashboard

CREATE TABLE IF NOT EXISTS links (
  id varchar(50) NOT NULL PRIMARY KEY,
  title varchar(255) NOT NULL,
  url varchar(1024) NOT NULL,
  theme varchar(50) DEFAULT 'indigo',
  logoUrl varchar(1024) DEFAULT NULL,
  initial varchar(2) DEFAULT NULL,
  tags text,
  created_at timestamp DEFAULT CURRENT_TIMESTAMP
);

-- Bảng lưu trữ Danh mục (Categories)
CREATE TABLE IF NOT EXISTS categories (
  id varchar(50) NOT NULL PRIMARY KEY,
  name varchar(100) NOT NULL,
  icon varchar(50) DEFAULT 'dashboard',
  color varchar(50) DEFAULT 'indigo',
  created_at timestamp DEFAULT CURRENT_TIMESTAMP
);

-- Chèn dữ liệu danh mục mặc định ban đầu nếu bảng trống
INSERT IGNORE INTO categories (id, name, icon, color) VALUES
('indigo', 'Work', 'work', 'indigo'),
('purple', 'Personal', 'person', 'purple'),
('pink', 'Social', 'forum', 'pink'),
('emerald', 'Research', 'science', 'emerald');

-- Chèn dữ liệu mẫu (Tùy chọn)
INSERT INTO `links` (`id`, `title`, `url`, `theme`, `initial`, `tags`) VALUES
('figma_1', 'Design System V2', 'https://figma.com/file/Ck234/Design-System', 'blue', 'D', '["Work", "UI/UX"]'),
('github_1', 'Tailwind CSS Docs', 'https://tailwindcss.com/docs/installation', 'emerald', 'T', '["Research", "Dev"]');
