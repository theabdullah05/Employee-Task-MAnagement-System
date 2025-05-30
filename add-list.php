<?php 
    include('config/constants.php');
    session_start();  // Make sure session is started to use $_SESSION
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
        }

        .tbl-half tr:last-child {
            border-bottom: none;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
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
        }

        .btn-primary:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
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
        }
    </style>
</head>

<body>

<div class="wrapper">

    <h1>TASK MANAGER</h1>

    <a class="btn-secondary" href="<?php echo SITEURL; ?>">Home</a>
    <a class="btn-secondary" href="<?php echo SITEURL; ?>manage-list.php">Manage Lists</a>

    <h3>Add List Page</h3>

    <p>
    <?php 
        // Display session message if set
        if(isset($_SESSION['add_fail'])) {
            echo $_SESSION['add_fail'];
            unset($_SESSION['add_fail']);
        }
    ?>
    </p>

    <!-- Form to Add List Starts Here -->
    <form method="POST" action="">
        <table class="tbl-half">
            <tr>
                <td>List Name: </td>
                <td><input type="text" name="list_name" placeholder="Type list name here" required /></td>
            </tr>
            <tr>
                <td>List Description: </td>
                <td><textarea name="list_description" placeholder="Type List Description Here"></textarea></td>
            </tr>
            <tr>
                <td colspan="2"><input class="btn-primary btn-lg" type="submit" name="submit" value="SAVE" /></td>
            </tr>
        </table>
    </form>
    <!-- Form to Add List Ends Here -->

</div>

</body>
</html>

<?php 
// Process the form submission
if(isset($_POST['submit'])) {
    
    // Sanitize input to prevent SQL injection & XSS
    $list_name = htmlspecialchars(mysqli_real_escape_string(mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT), $_POST['list_name']));
    $list_description = htmlspecialchars(mysqli_real_escape_string(mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT), $_POST['list_description']));
    
    // Connect Database on port 3308
    $conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
    
    if(!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    
    // Prepare SQL Query
    $sql = "INSERT INTO tbl_lists (list_name, list_description) VALUES (?, ?)";
    
    // Use prepared statement to prevent SQL Injection
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $list_name, $list_description);
        $res = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $res = false;
    }
    
    if($res) {
        $_SESSION['add'] = "List Added Successfully";
        header('location:'.SITEURL.'manage-list.php');
        exit();
    } else {
        $_SESSION['add_fail'] = "Failed to Add List";
        header('location:'.SITEURL.'add-list.php');
        exit();
    }
}
?>