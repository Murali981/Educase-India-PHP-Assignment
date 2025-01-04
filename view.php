<?php
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Fetch student with class information
$stmt = $pdo->prepare("SELECT student.*, classes.name as class_name 
                       FROM student 
                       LEFT JOIN classes ON student.class_id = classes.class_id 
                       WHERE student.id = ?");
$stmt->execute([$_GET['id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Student Details</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to List</a>

        <div class="card">
            <div class="card-body">
                <?php if($student['image']): ?>
                    <div class="text-center mb-3">
                        <img src="uploads/<?php echo htmlspecialchars($student['image']); ?>" 
                             alt="Student Image" 
                             class="img-fluid" 
                             style="max-width: 300px;">
                    </div>
                <?php endif; ?>

                <table class="table">
                    <tr>
                        <th width="200">Name</th>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?php echo nl2br(htmlspecialchars($student['address'])); ?></td>
                    </tr>
                    <tr>
                        <th>Class</th>
                        <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td><?php echo $student['created_at']; ?></td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-warning">Edit</a>
                    <a href="delete.php?id=<?php echo $student['id']; ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>