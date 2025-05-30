<?php 
    include('config/constants.php');
    session_start();

    // Sanitize list_id from URL
    $list_id_url = isset($_GET['list_id']) ? intval($_GET['list_id']) : 0;

    // Connect to database
    $conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
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

        <!-- Menu Starts Here -->
        <div class="menu">

            <a href="<?php echo SITEURL; ?>">Home</a>

            <?php 
                // Query to get lists
                $sql2 = "SELECT * FROM tbl_lists";
                $res2 = mysqli_query($conn, $sql2);

                if($res2) {
                    while($row2 = mysqli_fetch_assoc($res2)) {
                        $list_id = $row2['list_id'];
                        $list_name = htmlspecialchars($row2['list_name'], ENT_QUOTES);
                        ?>
                        <a href="<?php echo SITEURL; ?>list-task.php?list_id=<?php echo $list_id; ?>"><?php echo $list_name; ?></a>
                        <?php
                    }
                }
            ?>

            <a href="<?php echo SITEURL; ?>manage-list.php">Manage Lists</a>
        </div>
        <!-- Menu Ends Here -->

        <div class="all-task">
            <a class="btn-primary" href="<?php echo SITEURL; ?>add-task.php">Add Task</a>

            <table class="tbl-full">
                <tr>
                    <th>S.N.</th>
                    <th>Task Name</th>
                    <th>Priority</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>

                <?php 
                    if($list_id_url > 0) {
                        // Use prepared statement to fetch tasks by list_id
                        $stmt = mysqli_prepare($conn, "SELECT task_id, task_name, priority, deadline FROM tbl_tasks WHERE list_id = ?");
                        mysqli_stmt_bind_param($stmt, "i", $list_id_url);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $task_id, $task_name, $priority, $deadline);

                        $sn = 1;
                        $has_tasks = false;

                        while(mysqli_stmt_fetch($stmt)) {
                            $has_tasks = true;
                            ?>
                            <tr>
                                <td><?php echo $sn++; ?>.</td>
                                <td><?php echo htmlspecialchars($task_name, ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($priority, ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($deadline, ENT_QUOTES); ?></td>
                                <td>
                                    <a href="<?php echo SITEURL; ?>update-task.php?task_id=<?php echo $task_id; ?>">Update</a> 
                                    <a href="<?php echo SITEURL; ?>delete-task.php?task_id=<?php echo $task_id; ?>">Delete</a>
                                </td>
                            </tr>
                            <?php
                        }
                        mysqli_stmt_close($stmt);

                        if(!$has_tasks) {
                            ?>
                            <tr>
                                <td colspan="5">No Tasks added on this list.</td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5">Invalid List Selected.</td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>

    </div>
</body>
</html>
