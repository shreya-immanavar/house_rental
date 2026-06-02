<?php
include 'config/config.php';
$conn->query("ALTER TABLE properties ADD COLUMN tags VARCHAR(255) DEFAULT ''");
if ($conn->error)
    echo $conn->error;
else
    echo "Success";
