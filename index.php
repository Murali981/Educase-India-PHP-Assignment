<?php
require_once 'config/database.php';

// Fetch students with their class names
$stmt = $pdo->query("SELECT student.*, classes.name as class_name 
                     FROM student 
                     LEFT JOIN classes ON student.class_id = classes.class_id");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Students List</h2>
        <a href="create.php" class="btn btn-primary mb-3">Add New Student</a>
        <a href="classes.php" class="btn btn-secondary mb-3">Manage Classes</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>Image</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                        <td>
                            <?php if($student['image']): ?>
                            <img src="uploads/<?php echo $student['image']; ?>" 
                            alt="Student Image" 
                            style="width: 50px; height: 50px; object-fit: cover;"
                            class="img-thumbnail">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td><?php echo $student['created_at']; ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $student['id']; ?>" 
                               class="btn btn-info btn-sm">View</a>
                            <a href="edit.php?id=<?php echo $student['id']; ?>" 
                               class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?php echo $student['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>