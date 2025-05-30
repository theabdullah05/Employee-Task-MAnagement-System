<?php 
    include('config/constants.php');
    session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Task Manager with PHP and MySQL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin: 20px 0 30px;
            color: #2c3e50;
            font-size: 2.5rem;
        }

        .menu {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
        }

        .menu a {
            text-decoration: none;
            color: #fff;
            background-color: #3498db;
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .menu a:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .all-tasks {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            display: inline-block;
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
        }

        .tbl-full {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .tbl-full th, 
        .tbl-full td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eaeaea;
        }

        .tbl-full th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
        }

        .tbl-full tr:hover {
            background-color: #f8f9fa;
        }

        .tbl-full a {
            text-decoration: none;
            color: #3498db;
            margin-right: 10px;
            transition: all 0.2s ease;
        }

        .tbl-full a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .tbl-full a:last-child {
            color: #e74c3c;
        }

        .tbl-full a:last-child:hover {
            color: #c0392b;
        }

        p {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #f8f9fa;
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .tbl-full {
                display: block;
                overflow-x: auto;
            }
            
            .menu {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<div class="wrapper">

    <h1>EMPLOYEE TASK MANAGEMENT SYSTEM</h1>

    <!-- Menu Starts Here -->
    <div class="menu">

        <a href="<?php echo SITEURL; ?>">Home</a>

        <?php 
            // Connect to Database with port 3308
            $conn2 = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

            if (!$conn2) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Query to get lists from database
            $sql2 = "SELECT * FROM tbl_lists";

            $res2 = mysqli_query($conn2, $sql2);

            if ($res2) {
                while ($row2 = mysqli_fetch_assoc($res2)) {
                    $list_id = $row2['list_id'];
                    $list_name = $row2['list_name'];
                    ?>
                    <a href="<?php echo SITEURL; ?>list-task.php?list_id=<?php echo $list_id; ?>">
                        <?php echo htmlspecialchars($list_name); ?>
                    </a>
                    <?php
                }
            } else {
                echo "<p>No lists found.</p>";
            }
        ?>

        <a href="<?php echo SITEURL; ?>manage-list.php">Manage Lists</a>
    </div>
    <!-- Menu Ends Here -->

    <!-- Tasks Starts Here -->

    <p>
        <?php 
            if(isset($_SESSION['add'])) {
                echo $_SESSION['add'];
                unset($_SESSION['add']);
            }
            if(isset($_SESSION['delete'])) {
                echo $_SESSION['delete'];
                unset($_SESSION['delete']);
            }
            if(isset($_SESSION['update'])) {
                echo $_SESSION['update'];
                unset($_SESSION['update']);
            }
            if(isset($_SESSION['delete_fail'])) {
                echo $_SESSION['delete_fail'];
                unset($_SESSION['delete_fail']);
            }
        ?>
    </p>

    <div class="all-tasks">

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
                // Connect Database with port 3308 (reuse $conn2 or create new)
                $conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // SQL query to get tasks
                $sql = "SELECT * FROM tbl_tasks";
                $res = mysqli_query($conn, $sql);

                if ($res) {
                    $count_rows = mysqli_num_rows($res);
                    $sn = 1;

                    if ($count_rows > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $task_id = $row['task_id'];
                            $task_name = $row['task_name'];
                            $priority = $row['priority'];
                            $deadline = $row['deadline'];
                            ?>
                            <tr>
                                <td><?php echo $sn++; ?>.</td>
                                <td><?php echo htmlspecialchars($task_name); ?></td>
                                <td><?php echo htmlspecialchars($priority); ?></td>
                                <td><?php echo htmlspecialchars($deadline); ?></td>
                                <td>
                                    <a href="<?php echo SITEURL; ?>update-task.php?task_id=<?php echo $task_id; ?>">Update</a>
                                    <a href="<?php echo SITEURL; ?>delete-task.php?task_id=<?php echo $task_id; ?>">Delete</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5">No Task Added Yet.</td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='5'>Error fetching tasks.</td></tr>";
                }
            ?>

        </table>

    </div>

    <!-- Tasks Ends Here -->
</div>

</body>

</html>