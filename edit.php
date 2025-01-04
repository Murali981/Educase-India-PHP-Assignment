<?php
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Fetch all classes for dropdown
$stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch student data
$stmt = $pdo->prepare("SELECT * FROM student WHERE id = ?");
$stmt->execute([$_GET['id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    
    if (empty($_POST['name'])) {
        $errors[] = "Name is required";
    }
    if (empty($_POST['email'])) {
        $errors[] = "Email is required";
    }

    // Handle image upload
    $imagePath = $student['image']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array(strtolower($filetype), $allowed)) {
            $errors[] = "Only JPG and PNG files are allowed";
        } else {
            $newFilename = uniqid() . "." . $filetype;
            $uploadPath = "uploads/" . $newFilename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Delete old image if exists
                if ($student['image'] && file_exists("uploads/" . $student['image'])) {
                    unlink("uploads/" . $student['image']);
                }
                $imagePath = $newFilename;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE student SET name = ?, email = ?, address = ?, class_id = ?, image = ? WHERE id = ?");
        try {
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['address'],
                $_POST['class_id'],
                $imagePath,
                $student['id']
            ]);
            header("Location: index.php");
            exit();
        } catch(PDOException $e) {
            $errors[] = "Error updating student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Student</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to List</a>

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
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($student['name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($student['email']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"
                                ><?php echo htmlspecialchars($student['address']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select class="form-control" id="class_id" name="class_id" required>
                            <?php foreach($classes as $class): ?>
                                <option value="<?php echo $class['class_id']; ?>" 
                                    <?php echo $class['class_id'] == $student['class_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if($student['image']): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="uploads/<?php echo htmlspecialchars($student['image']); ?>" 
                                     alt="Current Image" style="max-width: 200px;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="image" class="form-label">New Image (optional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Only JPG and PNG files are allowed</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Student</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>