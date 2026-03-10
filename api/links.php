<?php
header("Content-Type: application/json");
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../auth.php';
require_api_login();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Lấy toàn bộ danh sách liên kết từ Database
        try {
            $stmt = $pdo->query("SELECT * FROM links ORDER BY created_at DESC");
            $links = $stmt->fetchAll();

            // Decode tags cho mỗi liên kết (vì tags được lưu dạng JSON trong SQL)
            foreach ($links as &$link) {
                $link['tags'] = json_decode($link['tags'], true) ?? [];
            }

            echo json_encode($links);
        }
        catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Lỗi truy vấn: " . $e->getMessage()]);
        }
        break;

    case 'POST':
        // Thêm hoặc Cập nhật liên kết
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['url'])) {
            http_response_code(400);
            echo json_encode(["error" => "Dữ liệu đầu vào không hợp lệ"]);
            exit;
        }

        $id = $input['id'] ?? null;
        $title = $input['title'] ?? 'New Link';
        $url = $input['url'];
        $theme = $input['theme'] ?? "indigo";
        $logoUrl = $input['logoUrl'] ?? null;
        if ((empty($logoUrl) || strpos($logoUrl, 's2/favicons') !== false) && !empty($url)) {
            $parsedHost = parse_url($url, PHP_URL_HOST);
            if (!empty($parsedHost)) {
                $logoUrl = "https://t3.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://" . $parsedHost . "&size=128";
            }
        }
        $initial = $input['initial'] ?? strtoupper(substr($title, 0, 1));
        $tags = json_encode($input['tags'] ?? []);

        try {
            // Kiểm tra link trùng lặp (xét cả trường hợp có hoặc không có dấu / ở cuối)
            $checkSql = "SELECT id FROM links WHERE (url = :url OR url = :url_no_slash OR url = :url_with_slash)";
            $checkParams = [
                'url' => $url,
                'url_no_slash' => rtrim($url, '/'),
                'url_with_slash' => rtrim($url, '/') . '/'
            ];

            if ($id) {
                $checkSql .= " AND id != :id";
                $checkParams['id'] = $id;
            }

            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute($checkParams);

            if ($checkStmt->fetch()) {
                http_response_code(409); // Trạng thái HTTP 409: Xảy ra xung đột (Conflict)
                echo json_encode(["error" => "Liên kết này đã tồn tại trong hệ thống!"]);
                exit;
            }

            if ($id) {
                // Update
                $sql = "UPDATE links SET title = :title, url = :url, theme = :theme, logoUrl = :logoUrl, initial = :initial, tags = :tags WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    "id" => $id,
                    "title" => $title,
                    "url" => $url,
                    "theme" => $theme,
                    "logoUrl" => $logoUrl,
                    "initial" => $initial,
                    "tags" => $tags
                ]);

                $linkData = [
                    "id" => $id, "title" => $title, "url" => $url, "theme" => $theme,
                    "logoUrl" => $logoUrl, "initial" => $initial, "tags" => json_decode($tags, true)
                ];

                echo json_encode(["success" => true, "link" => $linkData]);
            }
            else {
                // Insert
                $newId = uniqid();
                $sql = "INSERT INTO links (id, title, url, theme, logoUrl, initial, tags) VALUES (:id, :title, :url, :theme, :logoUrl, :initial, :tags)";
                $stmt = $pdo->prepare($sql);

                $newLink = [
                    "id" => $newId, "title" => $title, "url" => $url, "theme" => $theme,
                    "logoUrl" => $logoUrl, "initial" => $initial, "tags" => $tags
                ];

                $stmt->execute($newLink);

                $newLink['tags'] = json_decode($newLink['tags'], true);
                echo json_encode(["success" => true, "link" => $newLink]);
            }
        }
        catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Không thể lưu dữ liệu: " . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        // Xóa liên kết
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Thiếu ID"]);
            exit;
        }

        $id = $_GET['id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM links WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["success" => true]);
            }
            else {
                http_response_code(404);
                echo json_encode(["error" => "Không tìm thấy liên kết"]);
            }
        }
        catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Lỗi khi xóa: " . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không được hỗ trợ"]);
        break;
}
?>
