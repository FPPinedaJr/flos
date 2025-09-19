<?php
session_start();
require 'connect_db.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Both fields are required."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT iduser, password, is_admin FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(["status" => "error", "message" => "User not found."]);
            exit;
        }

        if (hash("sha256", $password) !== $user["password"]) {
            echo json_encode(["status" => "error", "message" => "Incorrect password."]);
            exit;
        }

        if ($user["is_admin"] == 1){
            $_SESSION["is_admin"] = 1;
        } else {
            $_SESSION["is_admin"] = 0;
        }

        $_SESSION["is_logged_in"] = true;
        $_SESSION["user_id"] = $user["iduser"];

        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>