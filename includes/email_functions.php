<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once __DIR__ . '/../config/mailer_info.php';
require __DIR__ . '/../vendor/autoload.php';

function generateToken($length = 6) {
    return bin2hex(random_bytes($length / 2));
}

function sendConfirmationToken($first_name, $last_name, $email, $password) {
    $token = generateToken();
    $mail = new PHPMailer(true);
    $name = $first_name . ' ' . $last_name;
    
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Change to DEBUG_SERVER for troubleshooting
        $mail->isSMTP();
        $mail->Host       = MAILER_HOST;
        $mail->SMTPAuth   = true; 
        $mail->Username   = MAILER_EMAIL; 
        $mail->Password   = MAILER_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        //Recipients
        $mail->setFrom(MAILER_EMAIL, 'CDC');
        $mail->addAddress($email, $name);
        
        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Confirm Your Email';
        $mail->Body    = message($name, $token);
        $mail->AltBody = "Hello $name, your confirmation code is: $token";
        
        $mail->send();
        $_SESSION['user_pending_confirmation'] = [
            'created_at' => time(), 
            'token' => $token,
            'user' => [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => $password
            ],
            'email' => $email // Adding email directly for easier access
        ];
        return true; 
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function verifyToken($token) {
    if (isset($_SESSION['user_pending_confirmation'])) {
        $confirmationToken = $_SESSION['user_pending_confirmation']['token'];
        $pastTime = $_SESSION['user_pending_confirmation']['created_at'];
        $currentTime = time();

        if ($currentTime - $pastTime >= 300) { // Greater than 5 minutes
            return ['error' => 'expired'];
        } 

        if ($token === $confirmationToken) {
            $userData = $_SESSION['user_pending_confirmation']['user'];
            unset($_SESSION['user_pending_confirmation']);
            return ['success' => true, 'user' => $userData];
        }
    }
    return ['error' => 'invalid'];
}

function message($name, $token) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Confirm Your Email</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; color: #333333; background-color: #f7f7f7;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
            <div style='background-color: #005eaa; padding: 30px 20px; text-align: center;'>
                <h1 style='margin: 0; color: #ffffff; font-size: 28px;'>Email Confirmation</h1>
            </div>
            
            <div style='padding: 30px 40px;'>
                <p style='margin-top: 0;'>Hello <span style='font-weight: bold;'>{$name}</span>,</p>
                <p>Thank you for registering with us. To complete your registration, please verify your email address using the confirmation code below:</p>
                
                <div style='font-size: 28px; font-weight: bold; text-align: center; padding: 15px; margin: 25px 0; background-color: #f0f7fd; border: 1px solid #d0e3f7; border-radius: 6px; letter-spacing: 2px; color: #005eaa;'>{$token}</div>
                
                <p style='color: #666666; font-size: 14px;'>This code will expire in <span style='font-weight: bold;'>5 minutes</span>.</p>
                <p style='margin-top: 25px;'>If you did not request this email, please disregard it or contact our support team.</p>
                
                <div style='margin-top: 40px; padding-top: 20px; border-top: 1px solid #eeeeee;'>
                    <p style='margin-bottom: 0; font-size: 14px;'>Thank you,<br><span style='font-weight: bold; color: #005eaa;'>CDC Support Team</span></p>
                </div>
            </div>
            
            <div style='background-color: #f7f7f7; padding: 20px; text-align: center; font-size: 12px; color: #777777;'>
                <p style='margin: 0;'>&copy; " . date('Y') . " CDC. All rights reserved.</p>
                <p style='margin: 5px 0 0;'>This is an automated message, please do not reply.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}
