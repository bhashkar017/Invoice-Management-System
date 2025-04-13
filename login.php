<?php
/*******************************************************************************
* Invoice Management System                                               *
*                                                                              *
* Version: 1.0	                                                               *
* Developer:  Abhishek Raj                                    				   *
*******************************************************************************/

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

// output any connection error
if ($mysqli->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection error']);
    exit;
}

session_start();
if(isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($mysqli, $_POST['username']);
    $password = md5($_POST['password']); // Hash the password with MD5 to match the stored hash

    $fetch = $mysqli->query("SELECT * FROM `users` WHERE username='$username' AND `password` = '$password'");
    
    if($fetch && $fetch->num_rows > 0) {
        $row = $fetch->fetch_assoc();
        $_SESSION['login_username'] = $row['username'];
        header('Content-Type: application/json');
        echo json_encode(1);
    } else {
        header('Content-Type: application/json');
        echo json_encode(0);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Missing username or password']);
}
?>