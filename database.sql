-- File khởi tạo cấu trúc database cho Link Manager Dashboard

CREATE TABLE IF NOT EXISTS `links` (
  `id` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `theme` varchar(50) DEFAULT 'blue',
  `logoUrl` text DEFAULT NULL,
  `initial` varchar(10) DEFAULT NULL,
  `tags` text DEFAULT NULL, -- Lưu dưới dạng JSON string hoặc text phân cách
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chèn dữ liệu mẫu (Tùy chọn)
INSERT INTO `links` (`id`, `title`, `url`, `theme`, `initial`, `tags`) VALUES
('figma_1', 'Design System V2', 'https://figma.com/file/Ck234/Design-System', 'blue', 'D', '["Work", "UI/UX"]'),
('github_1', 'Tailwind CSS Docs', 'https://tailwindcss.com/docs/installation', 'emerald', 'T', '["Research", "Dev"]');
