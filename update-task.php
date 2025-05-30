<?php 
include('config/constants.php');
session_start();

// Connect once to DB on port 3308
$conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, 3308);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables
$task_name = "";
$task_description = "";
$list_id = 0;
$priority = "";
$deadline = "";

// Check Task ID in URL
if (isset($_GET['task_id'])) {
    $task_id = intval($_GET['task_id']); // sanitize

    // Prepare select statement to get task details
    $stmt = mysqli_prepare($conn, "SELECT task_name, task_description, list_id, priority, deadline FROM tbl_tasks WHERE task_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $task_name, $task_description, $list_id, $priority, $deadline);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (empty($task_name)) {
        // No task found, redirect home
        header('location:' . SITEURL);
        exit();
    }
} else {
    // No task_id in URL, redirect home
    header('location:' . SITEURL);
    exit();
}
?>

<html>
<head>
    <title>Task Manager with PHP and MySQL</title>
    <link rel="stylesheet" href="<?php echo SITEURL; ?>css/style.css" />
</head>

<body>

<div class="wrapper">

    <h1>TASK MANAGER</h1>

    <p><a class="btn-secondary" href="<?php echo SITEURL; ?>">Home</a></p>

    <h3>Update Task Page</h3>

    <p>
        <?php 
        if (isset($_SESSION['update_fail'])) {
            echo htmlspecialchars($_SESSION['update_fail'], ENT_QUOTES);
            unset($_SESSION['update_fail']);
        }
        ?>
    </p>

    <form method="POST" action="">
        <table class="tbl-half">
            <tr>
                <td>Task Name: </td>
                <td><input type="text" name="task_name" value="<?php echo htmlspecialchars($task_name, ENT_QUOTES); ?>" required="required" /></td>
            </tr>

            <tr>
                <td>Task Description: </td>
                <td>
                    <textarea name="task_description" rows="5" cols="30"><?php echo htmlspecialchars($task_description, ENT_QUOTES); ?></textarea>
                </td>
            </tr>

            <tr>
                <td>Select List: </td>
                <td>
                    <select name="list_id">
                        <?php
                        // Get lists from DB
                        $sql2 = "SELECT list_id, list_name FROM tbl_lists";
                        $res2 = mysqli_query($conn, $sql2);
                        if ($res2 && mysqli_num_rows($res2) > 0) {
                            while ($row2 = mysqli_fetch_assoc($res2)) {
                                $list_id_db = $row2['list_id'];
                                $list_name_db = $row2['list_name'];
                                $selected = ($list_id_db == $list_id) ? "selected" : "";
                                echo "<option value='" . intval($list_id_db) . "' $selected>" . htmlspecialchars($list_name_db, ENT_QUOTES) . "</option>";
                            }
                        } else {
                            $selected = ($list_id == 0) ? "selected" : "";
                            echo "<option value='0' $selected>None</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Priority: </td>
                <td>
                    <select name="priority">
                        <option value="High" <?php if ($priority == "High") echo "selected"; ?>>High</option>
                        <option value="Medium" <?php if ($priority == "Medium") echo "selected"; ?>>Medium</option>
                        <option value="Low" <?php if ($priority == "Low") echo "selected"; ?>>Low</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Deadline: </td>
                <td><input type="date" name="deadline" value="<?php echo htmlspecialchars($deadline, ENT_QUOTES); ?>" /></td>
            </tr>

            <tr>
                <td><input class="btn-primary btn-lg" type="submit" name="submit" value="UPDATE" /></td>
            </tr>
        </table>
    </form>

</div>

</body>
</html>

<?php
// Handle form submission
if (isset($_POST['submit'])) {
    // Get updated values with trimming
    $task_name = trim($_POST['task_name']);
    $task_description = trim($_POST['task_description']);
    $list_id = intval($_POST['list_id']);
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];

    // Update with prepared statement
    $stmt = mysqli_prepare($conn, "UPDATE tbl_tasks SET task_name = ?, task_description = ?, list_id = ?, priority = ?, deadline = ? WHERE task_id = ?");
    mysqli_stmt_bind_param($stmt, "ssissi", $task_name, $task_description, $list_id, $priority, $deadline, $task_id);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($res) {
        $_SESSION['update'] = "Task Updated Successfully.";
        header('location:' . SITEURL);
        exit();
    } else {
        $_SESSION['update_fail'] = "Failed to Update Task";
        header('location:' . SITEURL . 'update-task.php?task_id=' . $task_id);
        exit();
    }
}
?>
