<?php
require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $suffix = $_POST['suffix'];
    $birth_date = $_POST['birth_date'];
    $sex = $_POST['sex'];
    $mobile = $_POST['mobile'];
    $working = $_POST['working'];
    $occupation = $_POST['occupation'];
    $house = $_POST['house'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $verified = $_POST['verified'];
    $profile_pic = $_POST['profile_pic'];

    $sql = "UPDATE registerlanding SET 
                first_name=?, last_name=?, middle_name=?, suffix=?, 
                birth_date=?, sex=?, mobile=?, working=?, occupation=?, 
                house=?, street=?, barangay=?, city=?, verified=?, profile_pic=? 
            WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssisi", 
        $first_name, $last_name, $middle_name, $suffix, 
        $birth_date, $sex, $mobile, $working, $occupation, 
        $house, $street, $barangay, $city, $verified, $profile_pic, $id);
    
    $stmt->execute();
    echo "Sync successful!";
    $stmt->close();
}
?>