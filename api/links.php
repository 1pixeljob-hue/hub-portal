<?php
header("Content-Type: application/json");

$dataFile = __DIR__ . '/../data/links.json';

// Ensure data file exists
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Read Links
        echo file_get_contents($dataFile);
        break;

    case 'POST':
        // Add new Link
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['url'])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid input data"]);
            exit;
        }

        $links = json_decode(file_get_contents($dataFile), true) ?? [];
        
        $newLink = [
            "id" => uniqid(),
            "title" => $input['title'] ?? 'New Link',
            "url" => $input['url'],
            "theme" => "blue", // default theme
            "logoUrl" => $input['logoUrl'] ?? null,
            "initial" => $input['initial'] ?? strtoupper(substr($input['title'] ?? 'L', 0, 1)),
            "tags" => $input['tags'] ?? []
        ];

        // Prepend to list
        array_unshift($links, $newLink);
        
        if (file_put_contents($dataFile, json_encode($links, JSON_PRETTY_PRINT))) {
            echo json_encode(["success" => true, "link" => $newLink]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to save data. Permissions issue?"]);
        }
        break;

    case 'DELETE':
        // Delete a link
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID is required"]);
            exit;
        }

        $id = $_GET['id'];
        $links = json_decode(file_get_contents($dataFile), true) ?? [];
        $initialCount = count($links);

        $links = array_filter($links, function($link) use ($id) {
            return $link['id'] !== $id;
        });

        // Re-index array after filtering
        $links = array_values($links);

        if (count($links) < $initialCount) {
             file_put_contents($dataFile, json_encode($links, JSON_PRETTY_PRINT));
             echo json_encode(["success" => true]);
        } else {
             http_response_code(404);
             echo json_encode(["error" => "Link not found"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>
