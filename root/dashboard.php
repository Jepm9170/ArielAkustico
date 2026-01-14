<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$carousel_dir = 'assets/img/Eventos/';
$image_count = 4;
$messages = [];

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['img_number'])) {
    $img_number = intval($_POST['img_number']);
    if ($img_number >= 1 && $img_number <= $image_count && isset($_FILES['carousel_img'])) {
        $target_file = $carousel_dir . 'carousel' . $img_number . '.PNG'; // Use .PNG
        $file_type = strtolower(pathinfo($_FILES['carousel_img']['name'], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (in_array($file_type, $allowed_types)) {
            // Always save as PNG to match your carousel
            if (move_uploaded_file($_FILES['carousel_img']['tmp_name'], $target_file)) {
                $messages[] = "Image #$img_number replaced successfully.";
            } else {
                $messages[] = "Failed to upload image #$img_number. Error code: " . $_FILES['carousel_img']['error'];
            }
        } else {
            $messages[] = "Invalid file type for image #$img_number. Only JPG/JPEG/PNG allowed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: #181818;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .dashboard-container {
            max-width: 700px;
            margin: 40px auto;
            background: #232323;
            border-radius: 10px;
            padding: 2rem 2.5rem;
            box-shadow: 0 2px 16px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 2rem;
        }
        .carousel-list {
            margin-bottom: 2rem;
        }
        .carousel-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            background: #292929;
            border-radius: 8px;
            padding: 1rem;
        }
        .carousel-item img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 1.5rem;
            border: 2px solid #333;
            background: #111;
        }
        .messages {
            margin-bottom: 1.5rem;
            color: #7fff7f;
        }
        .logout-btn {
            display: block;
            margin: 0 auto 1rem auto;
            background: #ff4d4d;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
            cursor: pointer;
        }
        .logout-btn:hover {
            background: #b81e1e;
        }
        .upload-form {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            background: #292929;
            border-radius: 8px;
            padding: 1rem;
        }
        .upload-form select, .upload-form input[type="file"] {
            background: #333;
            color: #fff;
            border: 1px solid #444;
            border-radius: 5px;
            padding: 0.4rem 0.7rem;
        }
        .upload-form button {
            background: #2673ff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 0.4rem 1.2rem;
            cursor: pointer;
            font-size: 1rem;
        }
        .upload-form button:hover {
            background: #1451b8;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Carousel Image Manager</h2>
        <form method="get" action="dashboard.php" style="text-align:right; margin-bottom:1rem;">
            <button class="logout-btn" type="submit" name="logout" value="1">Logout</button>
        </form>
        <?php if (!empty($messages)): ?>
            <div class="messages">
                <?php foreach ($messages as $msg): ?>
                    <div><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form class="upload-form" method="post" enctype="multipart/form-data">
            <label for="img_number">Choose Carousel Image:</label>
            <select name="img_number" id="img_number" required>
                <?php
                $selected = isset($_POST['img_number']) ? intval($_POST['img_number']) : 1;
                for ($i = 1; $i <= $image_count; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if ($selected === $i) echo 'selected'; ?>>
                        Carousel <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
            <input type="file" name="carousel_img" accept="image/jpeg,image/png" required>
            <button type="submit">Replace Image</button>
        </form>
        <div class="carousel-list">
            <?php for ($i = 1; $i <= $image_count; $i++):
                $img_path = $carousel_dir . 'carousel' . $i . '.PNG';
                $img_exists = file_exists($img_path);
            ?>
            <div class="carousel-item">
                <div>
                    <div>Image #<?php echo $i; ?></div>
                    <img src="<?php echo $img_exists ? $img_path : 'https://via.placeholder.com/120x80?text=No+Image'; ?>?t=<?php echo time(); ?>" alt="Carousel <?php echo $i; ?>">
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>