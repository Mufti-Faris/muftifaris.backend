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


if (isset($_POST['confirm_delete'])) {
    $task_id = $_POST['id'];
    $sql = "DELETE FROM tasks WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $task_id);
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
    <title>Delete Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('flat.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }




        .container {
            max-width: 500px;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .confirmation-text {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
        }

        .delete-form {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }


        .delete-btn-confirm,
        .cancel-btn {
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            color: #fff;
            transition: background-color 0.3s;
            width: 48%;
        }

        .delete-btn-confirm {
            background-color: #e74c3c;
        }

        .delete-btn-confirm:hover {
            background-color: #c0392b;
        }

        .cancel-btn {
            background-color: #7f8c8d;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .cancel-btn:hover {
            background-color: #95a5a6;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Delete Task</h1>
        <p class="confirmation-text">Apa kamu yakin menghapus To-Do List : "<strong><?php echo htmlspecialchars($task['task']); ?></strong>"?</p>
        <form method="POST" action="delete.php" class="delete-form">
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
            <button type="submit" name="confirm_delete" class="delete-btn-confirm">Yes, Delete</button>
            <a href="todolist.php" class="cancel-btn">Cancel</a>
        </form>
    </div>
</body>

</html>