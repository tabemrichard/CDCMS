<?php

if (!isset($_SESSION['user_pending_confirmation'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['resend']) && $_GET['resend'] == 'true') {
    $userData = $_SESSION['user_pending_confirmation']['user'];
    $first_name = $userData['first_name'];
    $last_name = $userData['last_name'];
    $email = $userData['email'];
    $password = $userData['password'];
    
    if (sendConfirmationToken($first_name, $last_name, $email, $password)) {
        $_SESSION['confirmation_success'] = 'A new confirmation code has been sent to your email.';
        $_SESSION['code_resent'] = true;
    } else {
        $_SESSION['confirmation_error'] = 'Failed to send confirmation email. Please try again.';
    }
    
    header('Location: confirmation.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmation_code'])) {
    $token = trim($_POST['confirmation_code']);
    $result = verifyToken($token);
    
    if (isset($result['success'])) {
        $user = $result['user'];
        $password_hash = password_hash($user['password'], PASSWORD_DEFAULT);
        
        // Insert user into database
        $insertQuery = 'INSERT INTO user (first_name, last_name, email, password, role)
                         VALUES (:first_name, :last_name, :email, :password, "teacher")';
        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindParam(':first_name', $user['first_name'], PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $user['last_name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $user['email'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            $_SESSION['registration_success'] = 'Registration successful! Your account has been created. You can now log in.';
            header('Location: login.php');
            exit;
        } else {
            $_SESSION['registration_errors']['account_error'] = 'Failed to create account. Please try again.';
            header('Location: registration.php');
        }
    } elseif (isset($result['error']) && $result['error'] === 'expired') {
        $_SESSION['registration_errors']['token_expired'] = 'Confirmation code has expired. Please try again.';
        header('Location: registration.php');
    } else {
        $_SESSION['confirmation_error'] = 'Invalid confirmation code. Please try again.';
    }
}
