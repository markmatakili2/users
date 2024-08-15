<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $profilePic = $_FILES['profile_pic'];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($profilePic["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a real image
    $check = getimagesize($profilePic["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
    } else {
        if (move_uploaded_file($profilePic["tmp_name"], $targetFile)) {
            $sql = "UPDATE users SET profile_pic = :profile_pic WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['profile_pic' => $targetFile, 'id' => $_SESSION['user_id']]);
            echo "Profile picture uploaded successfully!";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>

<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

    <?php if ($user['profile_pic']): ?>
        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" width="150">
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="profile.php">
        <input type="file" name="profile_pic" required>
        <button type="submit">Upload Profile Picture</button>
    </form>

    <a href="logout.php">Logout</a>
</body>

</html>