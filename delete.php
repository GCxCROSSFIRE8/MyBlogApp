<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    // $id = $_GET['id'];
    $id = intval($_GET['id']);

    $sql = "DELETE FROM posts WHERE id = '$id' AND user_id = " . $_SESSION['user_id'];
    if (mysqli_query($conn, $sql)) {
         header("Location: dashboard.php?deleted=1");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    echo "Invalid post ID!";
    exit();
}
?>
