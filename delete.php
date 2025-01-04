<?php
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Fetch student to get image filename
$stmt = $pdo->prepare("SELECT * FROM student WHERE id = ?");
$stmt->execute([$_GET['id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student) {
    // Delete image file if exists
    if ($student['image'] && file_exists("uploads/" . $student['image'])) {
        unlink("uploads/" . $student['image']);
    }

    // Delete student record
    $stmt = $pdo->prepare("DELETE FROM student WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: index.php");
exit();
?>