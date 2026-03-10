<?php
session_start();

function require_login()
{
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}

function require_api_login()
{
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized access. Please login.']);
        exit;
    }
}
?>
