<?php

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'todolist';
$conn = new mysqli($host, $user, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    if (!$task) {
        die("Task not found");
    }
}

if (isset($_POST['update_task'])) {
    $task_id = $_POST['id'];
    $updated_task = $_POST['task'];
    $updated_status = $_POST['status'];
    $updated_priority = $_POST['priority'];
    $updated_deadline = $_POST['deadline'];

    $sql = "UPDATE tasks SET task=?, status=?, priority=?, deadline=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $updated_task, $updated_status, $updated_priority, $updated_deadline, $task_id);
    if ($stmt->execute()) {
        header('Location: todolist.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Edit Task</h1>
        <form method="POST" action="edit.php">
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">


            <input type="text" name="task" value="<?php echo htmlspecialchars($task['task']); ?>" required>


            <select name="status" required>
                <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>


            <select name="priority" required>
                <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                <option value="normal" <?php echo $task['priority'] === 'normal' ? 'selected' : ''; ?>>Normal</option>
                <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
            </select>


            <input type="date" name="deadline" value="<?php echo $task['deadline']; ?>" required>

            <button type="submit" name="update_task">Update Task</button>
        </form>
    </div>
</body>

</html>