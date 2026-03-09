<?php
require_once 'db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all categories with their total links count
        try {
            // Left join with links to count usage
            $stmt = $pdo->query("
                SELECT c.id, c.name, c.icon, c.color, COUNT(l.id) as count
                FROM categories c
                LEFT JOIN links l ON c.id = l.theme
                GROUP BY c.id
                ORDER BY c.created_at ASC
            ");
            $categories = $stmt->fetchAll();
            echo json_encode($categories);
        }
        catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'POST':
        // Create new category
        $input = json_decode(file_get_contents('php://input'), true);

        $name = trim($input['name'] ?? '');
        $icon = trim($input['icon'] ?? 'folder'); // Default icon
        $color = trim($input['color'] ?? 'indigo'); // Default color

        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['error' => 'Category name is required']);
            exit;
        }

        // Generate a simple ID based on name
        $id = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        $id = trim($id, '-');

        if (empty($id)) {
            $id = 'cat-' . time();
        }

        try {
            // Kiểm tra danh mục trùng lặp theo tên (không phân biệt chữ hoa, chữ thường)
            $checkNameStmt = $pdo->prepare("SELECT id FROM categories WHERE LOWER(name) = LOWER(?)");
            $checkNameStmt->execute([$name]);
            if ($checkNameStmt->fetch()) {
                http_response_code(409); // Xung đột dữ liệu
                echo json_encode(['error' => 'Tên danh mục này đã tồn tại trong hệ thống!']);
                exit;
            }

            // Check if ID exists, append random string if it does
            $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
            $checkStmt->execute([$id]);
            if ($checkStmt->fetch()) {
                $id .= '-' . rand(100, 999);
            }

            $stmt = $pdo->prepare("INSERT INTO categories (id, name, icon, color) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $name, $icon, $color]);

            echo json_encode([
                'success' => true,
                'category' => [
                    'id' => $id,
                    'name' => $name,
                    'icon' => $icon,
                    'color' => $color,
                    'count' => 0
                ]
            ]);
        }
        catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create category: ' . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        // Delete category
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Category ID is required']);
            exit;
        }

        try {
            // Optional: check if category has links before deleting, or move links to a default category
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        }
        catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete category']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
