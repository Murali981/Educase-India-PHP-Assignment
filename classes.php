<?php
require_once 'config/database.php';

// Handle Class Addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['class_name']) && !empty($_POST['class_name'])) {
        $className = trim($_POST['class_name']);
        $stmt = $pdo->prepare("INSERT INTO classes (name) VALUES (?)");
        try {
            $stmt->execute([$className]);
            header("Location: classes.php");
            exit();
        } catch(PDOException $e) {
            $error = "Error adding class: " . $e->getMessage();
        }
    }
}

// Handle Class Deletion
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM classes WHERE class_id = ?");
    try {
        $stmt->execute([$_GET['delete_id']]);
        header("Location: classes.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error deleting class: " . $e->getMessage();
    }
}

// Handle Class Edit
if (isset($_POST['edit_id']) && isset($_POST['edit_name'])) {
    $stmt = $pdo->prepare("UPDATE classes SET name = ? WHERE class_id = ?");
    try {
        $stmt->execute([$_POST['edit_name'], $_POST['edit_id']]);
        header("Location: classes.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error updating class: " . $e->getMessage();
    }
}

// Fetch all classes
$stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Classes</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to Students</a>

        <!-- Add Class Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add New Class</h5>
                <form method="POST" action="classes.php">
                    <div class="mb-3">
                        <label for="class_name" class="form-label">Class Name</label>
                        <input type="text" class="form-control" id="class_name" name="class_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Class</button>
                </form>
            </div>
        </div>

       <!-- Classes List -->
<table class="table">
    <thead>
        <tr>
            <th>Class Name</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($classes as $class): ?>
            <tr>
                <td><?php echo htmlspecialchars($class['name']); ?></td>
                <td><?php echo $class['created_at']; ?></td>
                <!-- Replace your existing td here with this new code -->
                <td>
                    <button type="button" 
                            class="btn btn-warning btn-sm" 
                            onclick="editClass(<?php echo $class['class_id']; ?>, '<?php echo htmlspecialchars($class['name']); ?>')">
                        Edit
                    </button>
                    <a href="classes.php?delete_id=<?php echo $class['class_id']; ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('Are you sure? This will remove all students in this class!')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>
    <script>
function editClass(id, name) {
    let newName = prompt("Enter new class name:", name);
    if (newName && newName !== name) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="edit_id" value="${id}">
            <input type="hidden" name="edit_name" value="${newName}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</body>
</html>