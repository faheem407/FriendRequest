<?php
// Include the Model/User.php file to use the User class
require("Models/User.php");
$user = new User();

// Start a new Session
session_start();

// Login a User
if (isset($_POST["login_btn"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    if(str_word_count($username) == 1) {
        if ($user->login($username, $password)) {
            $_SESSION["login"] = $username;
        }
        else
        {
            echo '<center class="text-white bg-danger">Error in username and password</center>';
        }
    }
    else {
        echo '<center class="text-white bg-danger">Bad Username</center>';
    }
}

// Register a new User
elseif (isset($_POST["register_btn"])) {
    $email = $_POST["email"];
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $dob = $_POST["dob"];
    $addr = $_POST["address"];
    $loc = $_POST["loc"];
    $rel_status = $_POST["rel_status"];
    $pass = $_POST["password"];
    $img_path = "";
    $verified = FALSE;

    // Verify that the basic user details do not consist of bad information
    if(str_word_count($username) == 1 && str_word_count($fullname) < 5) {
        $verified = TRUE;
    }

    // Set image name if image is uploaded
    if (isset($_FILES["img"]["name"])) {
        $info = pathinfo($_FILES['img']['name']);
        if (isset($info["extension"])) {
            $ext = $info["extension"];
            $img_path = "$username.$ext";
            move_uploaded_file($_FILES['img']['tmp_name'], "images/$img_path");
        }
    }
    if($verified) {
        if ($user->register($email, $fullname, $username, $img_path, $addr, $dob, $rel_status, $loc, $pass) !== TRUE) {
            echo $user->db->conn->error;
        }
        else {
            $user->login($username, $pass);
            $_SESSION["login"] = $username;
        }
    }
    else {
        echo "Bad Entries! Please provide valid values!";
    }
}

// Logout a User
if (isset($_POST["logout_btn"]))
{
    unset($_SESSION["login"]);
    session_destroy();
}
