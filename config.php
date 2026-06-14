<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "lending_db");
if (!$conn) { die("Connection failed"); }
mysqli_set_charset($conn, "utf8");


function safe_query($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }
    return $result;
}


function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
}


function check_role($required_role) {
    check_login();
    if ($_SESSION['role'] != $required_role) {
        header("Location: ../index.php");
        exit();
    }
}
?>
