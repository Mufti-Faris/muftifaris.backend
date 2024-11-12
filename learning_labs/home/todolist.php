<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'todolist';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add task for the logged-in user
if (isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task, status, priority, deadline) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        // Display the SQL error
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('issss', $user_id, $task, $status, $priority, $deadline);
    if ($stmt->execute()) {
        header('Location: todolist.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch tasks specific to the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tasks WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Display the SQL error
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L0124133</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo"><?php echo htmlspecialchars($_SESSION['username']); ?> To-Do List</div>
    <div class="navigation">
        <a href="logout.php" class="btnLogout-popup">
            <img src="https://img.icons8.com/ios/452/logout-rounded.png" class="logout-icon" alt="Logout Icon" />
            Logout
        </a>
    </div>
</header>


    <div class="container">
        <h1>To-Do List</h1>

        <!-- Task Form -->
        <form method="POST" action="todolist.php">
            <input type="text" name="task" placeholder="Enter a new task" required>
            <select name="status" required>
                <option value="pending" selected>Pending</option>
                <option value="completed">Completed</option>
            </select>
            <select name="priority" required>
                <option value="normal" selected>Normal</option>
                <option value="high">High</option>
                <option value="low">Low</option>
            </select>
            <input type="date" name="deadline" required>
            <button type="submit" name="add_task">Add Task</button>
        </form>

        <!-- Task Table -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tasks->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $tasks->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['task']); ?></td>
                            <td><?php echo ucfirst($row['status']); ?></td>
                            <td class="<?php echo 'priority-' . strtolower($row['priority']); ?>">
                                <?php echo strtoupper($row['priority']); ?>
                            </td>
                            <td><?php echo $row['deadline']; ?></td>
                            <td class="actions-cell">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="delete-btn">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No tasks found. Add a new task above.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
