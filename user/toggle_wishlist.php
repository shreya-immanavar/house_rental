<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

if(isset($_GET['id'])){
    $user_id = $_SESSION['user']['id'];
    $property_id = (int)$_GET['id'];
    
    // Check if already in wishlist
    $check = $conn->query("SELECT id FROM wishlist WHERE user_id=$user_id AND property_id=$property_id");
    
    if($check->num_rows > 0){
        // Remove it
        $conn->query("DELETE FROM wishlist WHERE user_id=$user_id AND property_id=$property_id");
    } else {
        // Add it
        $conn->query("INSERT INTO wishlist (user_id, property_id) VALUES ($user_id, $property_id)");
    }
}

// Redirect back to previous page
if(isset($_SERVER['HTTP_REFERER'])){
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: view_properties.php");
}
exit;
?>
