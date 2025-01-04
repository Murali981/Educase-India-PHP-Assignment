<?php
require_once 'config/database.php';

// Fetch all classes for dropdown
$stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    
    // Validate inputs
    if (empty($_POST['name'])) {
        $errors[] = "Name is required";
    }
    if (empty($_POST['email'])) {
        $errors[] = "Email is required";
    }
    if (empty($_POST['class_id'])) {
        $errors[] = "Class is required";
    }

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array(strtolower($filetype), $allowed)) {
            $errors[] = "Only JPG and PNG files are allowed";
        } else {
            // Create unique filename
            $newFilename = uniqid() . "." . $filetype;
            $uploadPath = "uploads/" . $newFilename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $imagePath = $newFilename;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO student (name, email, address, class_id, image) VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['address'],
                $_POST['class_id'],
                $imagePath
            ]);
            header("Location: index.php");
            exit();
        } catch(PDOException $e) {
            $errors[] = "Error adding student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Add New Student</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to Students</a>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="create.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select class="form-control" id="class_id" name="class_id" required>
                            <option value="">Select a class</option>
                            <?php foreach($classes as $class): ?>
                                <option value="<?php echo $class['class_id']; ?>">
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Only JPG and PNG files are allowed</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Student</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>