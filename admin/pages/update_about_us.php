<?php
include '../dbConnection/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['about_us_content'];
    $slug = 'about-us';

    // Check if the about-us page exists
    $checkSql = "SELECT id FROM pages WHERE slug = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $slug);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $checkStmt->close();

    if ($result->num_rows > 0) {
        // Update existing page
        $sql = "UPDATE pages SET content = ? WHERE slug = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $content, $slug);
    } else {
        // Insert new page
        $title = "About Us";
        $currentDate = date('Y-m-d H:i:s');
        $sql = "INSERT INTO pages (title, content, slug, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $title, $content, $slug, $currentDate);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();
?>