<?php 
    include('config/constants.php');
    session_start();

    if(isset($_GET['list_id'])) {
        $list_id = intval($_GET['list_id']); // sanitize input as integer

        // Connect to database with port
        $conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
        if(!$conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // Prepare statement to delete list safely
        $sql = "DELETE FROM tbl_lists WHERE list_id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $list_id);
            $res = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if($res) {
                $_SESSION['delete'] = "List Deleted Successfully";
            } else {
                $_SESSION['delete_fail'] = "Failed to Delete List.";
            }
        } else {
            $_SESSION['delete_fail'] = "Failed to Prepare Delete Statement.";
        }
        
        header('location:'.SITEURL.'manage-list.php');
        exit();

    } else {
        // No list_id provided, redirect
        header('location:'.SITEURL.'manage-list.php');
        exit();
    }
?>
