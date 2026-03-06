<?php
header("Content-Type: application/json");
require_once __DIR__ . '/db.php';

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
        // Thêm liên kết mới
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['url'])) {
            http_response_code(400);
            echo json_encode(["error" => "Dữ liệu đầu vào không hợp lệ"]);
            exit;
        }

        $newLink = [
            "id" => uniqid(),
            "title" => $input['title'] ?? 'New Link',
            "url" => $input['url'],
            "theme" => $input['theme'] ?? "blue",
            "logoUrl" => $input['logoUrl'] ?? null,
            "initial" => $input['initial'] ?? strtoupper(substr($input['title'] ?? 'L', 0, 1)),
            "tags" => json_encode($input['tags'] ?? []) // Encode mảng tags sang JSON string
        ];

        try {
            $sql = "INSERT INTO links (id, title, url, theme, logoUrl, initial, tags) VALUES (:id, :title, :url, :theme, :logoUrl, :initial, :tags)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($newLink);

            // Trả về dữ liệu đã decode để frontend sử dụng ngay
            $newLink['tags'] = json_decode($newLink['tags'], true);
            echo json_encode(["success" => true, "link" => $newLink]);
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
