<?php
$conn = mysqli_connect('localhost', 'root', '', 'pasteshop_db');
if(!$conn) {
    die("Database Error: " . mysqli_connect_error());
}
?>
