<?php
session_start();
require 'connect_db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $score = isset($_POST["score"]) ? intval($_POST["score"]) : 0;
    $idflood = isset($_POST["projectId"]) ? intval($_POST["projectId"]) : 0;
    $comment = isset($_POST["feedback"]) ? trim($_POST["feedback"]) : '';

    if ($score <= 0 || $idflood <= 0) {
        echo "ERROR: Invalid rating or project ID";
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO rating (score, idflood, comment) VALUES (?, ?, ?)");
        $stmt->execute([$score, $idflood, $comment]);
        echo "OK"; // success
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage();
    }

} else {
    echo "ERROR: Invalid request method";
}
