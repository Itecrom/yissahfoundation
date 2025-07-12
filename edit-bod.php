<?php
include 'config.php';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_GET['id'])) {
    die("No ID specified.");
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $position = $conn->real_escape_string($_POST['position']);
    $bio = $conn->real_escape_string($_POST['bio']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $filename = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $targetFile;
            $updateImageSql = ", image = '$image'";
        } else {
            $updateImageSql = "";
        }
    } else {
        $updateImageSql = "";
    }

    $sql = "UPDATE bods SET name='$name', position='$position', bio='$bio' $updateImageSql WHERE id=$id";

    if ($conn->query($sql)) {
        $message = "Record updated successfully!";
        $messageClass = "success";
    } else {
        $message = "Failed to update record: " . $conn->error;
        $messageClass = "error";
    }
}

$result = $conn->query("SELECT * FROM bods WHERE id=$id");
if (!$result || $result->num_rows === 0) {
    die("No board member found with ID $id");
}
$bod = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Board Member</title>
    <style>
        /* Background */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Form container */
        .form-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            max-width: 500px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1.5px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus,
        input[type="file"]:focus {
            border-color: #6c63ff;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        img.current-image {
            display: block;
            margin: 0 auto 20px auto;
            max-width: 150px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        button {
            background: #6c63ff;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #574fd6;
        }

        .message {
            text-align: center;
            padding: 12px 0;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .cancel-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            text-decoration: none;
            color: #6c63ff;
            font-weight: 600;
        }

        .cancel-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Board Member</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($bod['name']); ?>" required>

            <label for="position">Position:</label>
            <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($bod['position']); ?>" required>

            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" required><?php echo htmlspecialchars($bod['bio']); ?></textarea>

            <label>Current Image:</label>
            <?php if (!empty($bod['image'])): ?>
                <img src="<?php echo htmlspecialchars($bod['image']); ?>" alt="Current Image" class="current-image">
            <?php else: ?>
                <p style="text-align:center;color:#666;">No image uploaded.</p>
            <?php endif; ?>

            <label for="image">Upload New Image (optional):</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit">Save</button>
        </form>
        <a href="admin.php#bods" class="cancel-link">Cancel</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
