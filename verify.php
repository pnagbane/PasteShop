<?php
if (isset($_GET['code'])) {
    $pagetitle = "Verify your account";
    require_once('assets/header.php');
    $code = $_GET['code'];
    // echo $code;
    $query = "SELECT * FROM users WHERE verification_code = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $result = $stmt->get_result();
    // var_dump($result);
    if($result->num_rows == 1) {
        $query = "UPDATE users SET verified = 1, verification_code = NULL WHERE verification_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $code);
        if($stmt->execute()) {
            echo "<h1>Account Verified Successfully</h1>";
        } else {
            die('<h1>Verification Failed: Contact system adminstrator</h1>');
        }
    } else {
        die('<h1>Verification Failed: User not found</h1>');
    }
} else {
    header('Location: register.php');
    exit();
}
