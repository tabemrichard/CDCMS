<?php
include_once 'email_functions.php';

function validateRegistrationForm($form) {
    $errors = [];
    $validatedForm = [];
    $requiredFields = ['first_name', 'last_name', 'password', 'email'];

    // Sanitize and trim inputs
    $validatedForm['first_name'] = sanitizeString($form['first_name']);
    $validatedForm['last_name'] = sanitizeString($form['last_name']);
    $validatedForm['password'] = trim($form['password']);
    $validatedForm['email'] = trim($form['email']);

    foreach ($requiredFields as $field) {
        if (empty($validatedForm[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    if (!empty($validatedForm['email']) && !validateEmail($validatedForm['email'])) {
        $errors['email'] = 'Invalid email format';
    }

    if (!empty($errors)) {
        return ['errors' => $errors];
    }

    return ['valid_form' => $validatedForm];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = validateRegistrationForm($_POST);

    if (empty($result['errors'])) {
        $user = $result['valid_form'];
        
        $checkEmailQuery = "SELECT id FROM user WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($checkEmailQuery);
        $stmt->bindParam(':email', $user['email'], PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['registration_errors'] = ['email' => 'You are already registered.'];
        } else {
            unset($_SESSION['code_resent']);
            if (sendConfirmationToken($user['first_name'], $user['last_name'], $user['email'], $user['password'])) {
                header('Location: confirmation.php');
                exit;
            } else {
                $_SESSION['registration_errors'] = ['email' => 'Failed to send confirmation email. Please try again.'];
            }
        }
    } else {
        $_SESSION['registration_errors'] = $result['errors'];
    }

    header('Location: registration.php');
    exit;
}
