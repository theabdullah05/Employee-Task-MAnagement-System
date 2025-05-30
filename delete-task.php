<?php 
    include('config/constants.php');
    session_start();

    if(isset($_GET['task_id'])) {
        $task_id = intval($_GET['task_id']); // sanitize input

        // Connect to database with port and database name
        $conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
        if(!$conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // Prepare statement for safer query
        $sql = "DELETE FROM tbl_tasks WHERE task_id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $task_id);
            $res = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if($res) {
                $_SESSION['delete'] = "Task Deleted Successfully.";
            } else {
                $_SESSION['delete_fail'] = "Failed to Delete Task.";
            }
        } else {
            $_SESSION['delete_fail'] = "Failed to Prepare Delete Statement.";
        }

        header('location:'.SITEURL);
        exit();

    } else {
        // No task_id provided, redirect
        header('location:'.SITEURL);
        exit();
    }
?>
