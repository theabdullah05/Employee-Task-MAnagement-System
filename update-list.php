<?php 
include('config/constants.php');
session_start();

// Connect to database once
$conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables
$list_name = "";
$list_description = "";

// Get the Current Values of Selected List
if (isset($_GET['list_id'])) {
    $list_id = intval($_GET['list_id']); // sanitize as integer

    // Prepare statement to fetch list data safely
    $stmt = mysqli_prepare($conn, "SELECT list_name, list_description FROM tbl_lists WHERE list_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $list_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $list_name, $list_description);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (empty($list_name)) {
        // If no list found, redirect
        header('location:' . SITEURL . 'manage-list.php');
        exit();
    }
} else {
    // No list_id in URL, redirect
    header('location:' . SITEURL . 'manage-list.php');
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    // Get updated values and trim whitespace
    $list_name = trim($_POST['list_name']);
    $list_description = trim($_POST['list_description']);

    // Update query using prepared statement to avoid SQL injection
    $stmt = mysqli_prepare($conn, "UPDATE tbl_lists SET list_name = ?, list_description = ? WHERE list_id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $list_name, $list_description, $list_id);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($res) {
        $_SESSION['update'] = "List Updated Successfully";
        header('location:' . SITEURL . 'manage-list.php');
        exit();
    } else {
        $_SESSION['update_fail'] = "Failed to Update List";
        header('location:' . SITEURL . 'update-list.php?list_id=' . $list_id);
        exit();
    }
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

    <a class="btn-secondary" href="<?php echo SITEURL; ?>">Home</a>
    <a class="btn-secondary" href="<?php echo SITEURL; ?>manage-list.php">Manage Lists</a>

    <h3>Update List Page</h3>

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
                <td>List Name: </td>
                <td><input type="text" name="list_name" value="<?php echo htmlspecialchars($list_name, ENT_QUOTES); ?>" required="required" /></td>
            </tr>

            <tr>
                <td>List Description: </td>
                <td>
                    <textarea name="list_description" rows="5" cols="30"><?php echo htmlspecialchars($list_description, ENT_QUOTES); ?></textarea>
                </td>
            </tr>

            <tr>
                <td><input class="btn-lg btn-primary" type="submit" name="submit" value="UPDATE" /></td>
            </tr>
        </table>
    </form>

</div>

</body>
</html>
