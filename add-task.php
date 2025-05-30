<?php 
    include('config/constants.php');
    session_start();  // Start the session to use $_SESSION
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
            padding: 20px;
        }

        .wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 2.2rem;
        }

        h3 {
            margin: 25px 0 15px;
            color: #3498db;
            font-size: 1.5rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .btn-secondary {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-secondary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        p {
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            background-color: #f8f9fa;
            color: #2c3e50;
            border-left: 4px solid #e74c3c;
        }

        .tbl-half {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .tbl-half tr {
            border-bottom: 1px solid #eaeaea;
        }

        .tbl-half td {
            padding: 15px 10px;
            vertical-align: top;
        }

        .tbl-half tr:last-child {
            border-bottom: none;
        }

        .tbl-half tr:last-child td {
            padding-top: 25px;
            text-align: center;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }

        .btn-primary {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 150px;
        }

        .btn-primary:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn-lg {
            padding: 12px 30px;
            font-size: 1.1rem;
        }

        @media (max-width: 600px) {
            .wrapper {
                padding: 20px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .tbl-half td {
                display: block;
                width: 100%;
                padding: 10px 0;
            }
            
            .tbl-half tr {
                margin-bottom: 15px;
                display: block;
            }
            
            .btn-primary {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="wrapper">

    <h1>TASK MANAGER</h1>

    <a class="btn-secondary" href="<?php echo SITEURL; ?>">Home</a>

    <h3>Add Task Page</h3>

    <p>
    <?php 
        if(isset($_SESSION['add_fail'])) {
            echo $_SESSION['add_fail'];
            unset($_SESSION['add_fail']);
        }
    ?>
    </p>

    <form method="POST" action="">
        <table class="tbl-half">
            <tr>
                <td>Task Name: </td>
                <td><input type="text" name="task_name" placeholder="Type your Task Name" required /></td>
            </tr>
            
            <tr>
                <td>Task Description: </td>
                <td><textarea name="task_description" placeholder="Type Task Description"></textarea></td>
            </tr>
            
            <tr>
                <td>Select List: </td>
                <td>
                    <select name="list_id" required>
                        <?php 
                            // Connect to DB with port
                            $conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
                            if(!$conn) {
                                die("Database connection failed: " . mysqli_connect_error());
                            }

                            // SQL to get lists
                            $sql = "SELECT * FROM tbl_lists";
                            $res = mysqli_query($conn, $sql);

                            if($res) {
                                $count_rows = mysqli_num_rows($res);

                                if($count_rows > 0) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        $list_id = $row['list_id'];
                                        $list_name = htmlspecialchars($row['list_name']); // sanitize output
                                        echo "<option value=\"$list_id\">$list_name</option>";
                                    }
                                } else {
                                    echo '<option value="0">None</option>';
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>Priority: </td>
                <td>
                    <select name="priority" required>
                        <option value="High">High</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>Deadline: </td>
                <td><input type="date" name="deadline" /></td>
            </tr>
            
            <tr>
                <td colspan="2"><input class="btn-primary btn-lg" type="submit" name="submit" value="SAVE" /></td>
            </tr>
        </table>
    </form>

</div>

</body>
</html>

<?php
if(isset($_POST['submit'])) {

    // Connect to DB once with port
    $conn2 = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
    if(!$conn2) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Sanitize & prepare input variables safely
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $list_id = intval($_POST['list_id']);  // int cast for safety
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];

    // Prepare statement for safe insert
    $sql2 = "INSERT INTO tbl_tasks (task_name, task_description, list_id, priority, deadline) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn2, $sql2);

    if($stmt) {
        mysqli_stmt_bind_param($stmt, "ssiss", 
            $task_name, $task_description, $list_id, $priority, $deadline
        );

        $res2 = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $res2 = false;
    }

    if($res2) {
        $_SESSION['add'] = "Task Added Successfully.";
        header('location:'.SITEURL);
        exit();
    } else {
        $_SESSION['add_fail'] = "Failed to Add Task";
        header('location:'.SITEURL.'add-task.php');
        exit();
    }
}
?>