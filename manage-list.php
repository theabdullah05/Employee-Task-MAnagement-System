<?php 
include('config/constants.php');
session_start();

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

        <a class="btn-secondary" href="<?php echo SITEURL; ?>">Home</a>

        <h3>Manage Lists Page</h3>

        <p>
            <?php 
                // Display session messages and then unset them
                $session_keys = ['add', 'delete', 'update', 'delete_fail'];
                foreach ($session_keys as $key) {
                    if (isset($_SESSION[$key])) {
                        echo htmlspecialchars($_SESSION[$key], ENT_QUOTES) . "<br />";
                        unset($_SESSION[$key]);
                    }
                }
            ?>
        </p>

        <!-- Table to display lists starts here -->
        <div class="all-lists">

            <a class="btn-primary" href="<?php echo SITEURL; ?>add-list.php">Add List</a>

            <table class="tbl-half">
                <tr>
                    <th>S.N.</th>
                    <th>List Name</th>
                    <th>Actions</th>
                </tr>

                <?php 
                    // Query to get all lists
                    $sql = "SELECT * FROM tbl_lists";
                    $res = mysqli_query($conn, $sql);

                    if($res) {
                        $count_rows = mysqli_num_rows($res);
                        $sn = 1;

                        if($count_rows > 0) {
                            while($row = mysqli_fetch_assoc($res)) {
                                $list_id = $row['list_id'];
                                $list_name = htmlspecialchars($row['list_name'], ENT_QUOTES);
                                ?>
                                <tr>
                                    <td><?php echo $sn++; ?>.</td>
                                    <td><?php echo $list_name; ?></td>
                                    <td>
                                        <a href="<?php echo SITEURL; ?>update-list.php?list_id=<?php echo $list_id; ?>">Update</a> 
                                        <a href="<?php echo SITEURL; ?>delete-list.php?list_id=<?php echo $list_id; ?>">Delete</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="3">No List Added Yet.</td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="3">Failed to retrieve lists from database.</td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>
        <!-- Table to display lists ends here -->
    </div>
</body>
</html>
