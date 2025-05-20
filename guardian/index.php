<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/database.php';

// ✅ Check if email & session_token are provided
if (!isset($_GET['email']) || !isset($_GET['session_token'])) {
    header("Location: https://smartbarangayconnect.com");
    exit();
}

$email = $_GET['email'];
$session_token = $_GET['session_token'];

// $email = 'wendhil09@gmail.com';
// $session_token = 'e37525a1d42b60583f94885e8b80c6f081eb26bcc7e399d546423ffae8c3baa9';

// $email = 'test@gmail.com';
// $session_token = 'c995529a1eee118f1b640b1ba5c060f431238660fb0af992ae8454bbbbfda710';

// $email = 'tffnyshnbls@gmail.com';
// $session_token = '679dde090551f15284046211ae7229c9c22996714beb4f6976561efee89574c0';



// ✅ Fetch registerlanding data from Main Domain API
$api_url = "https://smartbarangayconnect.com/api_get_registerlanding.php";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

if (!$data || !is_array($data)) {
    // die("❌ Failed to fetch data from Main Domain.");
    header("Location: https://smartbarangayconnect.com");
    exit();
}

// ✅ Clear old data in subdomain database
$pdo->exec("TRUNCATE TABLE registerlanding");

// ✅ Insert new data into subdomain database
$sql = "INSERT INTO registerlanding 
    (id, email, first_name, last_name, session_token, picture_pic, birth_date, sex, mobile, working, occupation, house, street, barangay, city, password) 
    VALUES (:id, :email, :first_name, :last_name, :session_token, :picture_pic, :birth_date, :sex, :mobile, :working, :occupation, :house, :street, :barangay, :city, :password)";

$stmt = $pdo->prepare($sql);

foreach ($data as $row) {
    $stmt->execute([
        ':id' => $row['id'],
        ':email' => $row['email'],
        ':first_name' => $row['first_name'],
        ':last_name' => $row['last_name'],
        ':session_token' => $row['session_token'],
        ':picture_pic' => $row['picture_pic'] ?? null,
        ':birth_date' => $row['birth_date'],
        ':sex' => $row['sex'],
        ':mobile' => $row['mobile'],
        ':working' => $row['working'],
        ':occupation' => $row['occupation'],
        ':house' => $row['house'],
        ':street' => $row['street'],
        ':barangay' => $row['barangay'],
        ':city' => $row['city'],
        ':password' => $row['password']
    ]);
}


// ✅ Verify session token in subdomain database
$sql = "SELECT id, email, first_name, last_name, picture_pic, birth_date, sex, mobile, working, occupation, house, street, barangay, city 
        FROM registerlanding WHERE email = :email AND session_token = :session_token";
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $email, ':session_token' => $session_token]);

$row = $stmt->fetch();

if (!$row) {
    // die("❌ Invalid session token or email!");
    header("Location: https://smartbarangayconnect.com");
    exit();
} else {
    // ✅ Check if the email exists in the guardian_account table
    try {
        $guardianStmt = $pdo->prepare('SELECT guardian_id, student_id, role, email, isConfirm FROM guardian_account WHERE email = :email'  );
        $guardianStmt->execute([':email' => $email]);
        $guardianRow = $guardianStmt->fetch(PDO::FETCH_ASSOC);
        
        // Store guardian status in session
        if ($guardianRow && (int) $guardianRow['isConfirm'] === 1) {

            // Save session from guardian_account
            $_SESSION['guardian_id'] = $guardianRow['guardian_id'];
            $_SESSION['student_id'] = $guardianRow['student_id'];
            $_SESSION['role'] = strtolower($guardianRow['role']);
            $_SESSION['email'] = $email;
            $_SESSION['activeGuardian'] = true;

        } else {
            // Set active guardian to false
            $_SESSION['activeGuardian'] = false;
        }
    } catch (PDOException $e) {
        // Log the error but don't expose it to the user
        error_log("Error checking guardian account: " . $e->getMessage());
        // Continue with the session - we'll just assume they're not a guardian
        header("Location: https://smartbarangayconnect.com");
        exit();
    }

    // ✅ Store all data in session
    $_SESSION['id'] = $row['id'];
    $_SESSION['email'] = $email;
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name'] = $row['last_name'];
    $_SESSION['session_token'] = $session_token;
    $_SESSION['picture_pic'] = !empty($row['picture_pic']) ? $row['picture_pic'] : 'https://smartbarangayconnect.com/uploads/default-profile.png';

    //  Additional session data
    $_SESSION['birth_date'] = $row['birth_date'];
    $_SESSION['sex'] = $row['sex'];
    $_SESSION['mobile'] = $row['mobile'];
    $_SESSION['working'] = $row['working'];
    $_SESSION['occupation'] = $row['occupation'];
    $_SESSION['house'] = $row['house'];
    $_SESSION['street'] = $row['street'];
    $_SESSION['barangay'] = $row['barangay'];
    $_SESSION['city'] = $row['city'];

    // ✅ Debugging: Uncomment to check if session data is correct before redirecting
    // var_dump($_SESSION);
    // exit();

    // ✅ Redirect to dashboard
    header("Location: ../index.php");
    exit();
}
?>