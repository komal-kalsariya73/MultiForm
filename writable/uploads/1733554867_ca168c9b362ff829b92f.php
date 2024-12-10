<?php
include 'connection.php';

$error = ''; // Variable to hold the error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $mobile_number = $_POST['mobile_number'];

    // Check for duplicate email
    $sql_check = "SELECT * FROM users WHERE email = '$email'";
    $result_check = $conn->query($sql_check);
    
    if ($result_check->num_rows > 0) {
        $error = "This email is already registered.";
    } else {
        // Profile image handling
        if (isset($_FILES['profile_images']) && $_FILES['profile_images']['error'] == UPLOAD_ERR_OK) {
            $profile_images = $_FILES['profile_images']['name'];
            $temp_name = $_FILES['profile_images']['tmp_name'];
            $folder = "./uploads/";

            if (move_uploaded_file($temp_name, $folder . $profile_images)) {
                echo "Profile image uploaded successfully.<br>";
            } else {
                echo "Failed to upload profile image.<br>";
            }
        } else {
            echo "No profile image uploaded or there was an error.<br>";
        }

        // Handle multiple images
        $multiple_images = [];
        if (isset($_FILES['images'])) {
            $total_files = count($_FILES['images']['name']);
            $folder = "./uploads/";
            
            for ($i = 0; $i < $total_files; $i++) {
                $image_name = $_FILES['images']['name'][$i];
                $temp_name = $_FILES['images']['tmp_name'][$i];

                if (move_uploaded_file($temp_name, $folder . $image_name)) {
                    $multiple_images[] = $image_name;
                } else {
                    echo "Failed to upload image: " . $image_name . "<br>";
                }
            }
        }
        $multiple_images_json = json_encode($multiple_images);

        // Insert user into the database
        $sql = "INSERT INTO users (name, lname, email, password, gender, profile_images, mobile_number, multiple_images) 
                VALUES ('$name', '$lname', '$email', '$password', '$gender', '$profile_images', '$mobile_number', '$multiple_images_json')";

        if ($conn->query($sql) === true) {
            echo "Registration successful.";
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>


https://phppot.com/php/wizard-form/