<?php
    if (isset($_POST['submit'])) {
        session_start();
        $database_path = '../database/config.php';
        $database_path_index = './database/config.php';

        if (file_exists($database_path)) {
            include($database_path);
        } else if (file_exists($database_path_index)){
            include($database_path_index);
        }

        $old_username = $_SESSION['valid'];

        if ($_POST['submit'] == 'edit') {
            try {
                $mem_id = $_POST['mem_id'];
                $username = $_POST['username'];
                $fname = $_POST['fname'];
                $lname = $_POST['lname'];
                $sex = $_POST['sex'];
                $birthdate = $_POST['birthdate'];
                $address =$_POST['address'];
                $contactnumber = $_POST['cnumber'];
                $profile = '';
                if ($_FILES['user-profile']['tmp_name'] != '') {
                    $profileData = $_FILES['user-profile']['tmp_name'];
                    $profile = file_get_contents($profileData); // Convert image to BLOB
                } else {
                    $profile = '';
                }

                if ($profile == '') {
                    $sql = "UPDATE accounts SET username = (?) WHERE username = '$old_username';";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('s', $username);
                    $stmt->execute();

                } else {
                    $sql = "UPDATE accounts SET username = (?), profile = (?) WHERE username = '$old_username';";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ss', $username, $profile);
                    $stmt->execute();

                }

                $sql = "UPDATE members SET fname = (?), lname = (?), sex = (?), birthdate = (?), address = (?), contact = (?) WHERE mem_id = $mem_id";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssss', $fname, $lname, $sex, $birthdate, $address, $contactnumber);
                $stmt->execute();
                
                $_SESSION['message'] = "Successfully updated account";
                $_SESSION['messageBg'] = "green";
                $_SESSION['section'] = './user/edit_section.php';
                $_SESSION['activeNavId'] = 'edit-nav';
                $_SESSION['valid'] = $username;
            } catch (Exception $e) {
                $_SESSION['message'] = "Failed to update account";
                $_SESSION['messageBg'] = "red";
                $_SESSION['section'] = './user/edit_section.php';
                $_SESSION['activeNavId'] = 'edit-nav';
                
            }
        }
        if ($_POST['submit'] == 'change-pw') {
            try {
                $old_password = $_POST['old_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                $mem_id = $_POST['mem_id'];
    
                $sql = "SELECT password FROM accounts WHERE mem_id = $mem_id";
                $result = query($sql);
                $password = $result['password'];
    
                if ($old_password == $password) {
                    if ($new_password == $confirm_password) {
                        $sql = "UPDATE accounts SET password = '$new_password' WHERE mem_id = $mem_id";
                        query($sql);
                        $_SESSION['message'] = "Successfully changed password";
                        $_SESSION['messageBg'] = "green";
                        $_SESSION['section'] = './user/edit_section.php';
                        $_SESSION['activeNavId'] = 'edit-nav';
                    }
                    else {
                        $_SESSION['message'] = "Failed to change password! Mismatched new password and confirm password";
                        $_SESSION['messageBg'] = "red";
                        $_SESSION['section'] = './user/edit_section.php';
                        $_SESSION['activeNavId'] = 'edit-nav';
                    }
                } else {
                    $_SESSION['message'] = "Failed to change password! Mismatched old password!";
                    $_SESSION['messageBg'] = "red";
                    $_SESSION['section'] = './user/edit_section.php';
                    $_SESSION['activeNavId'] = 'edit-nav';
                }
                
            } catch (Exception $e) {
                $_SESSION['message'] = "Failed to change password! Error: $e";
                $_SESSION['messageBg'] = "red";
                $_SESSION['section'] = './user/edit_section.php';
                $_SESSION['activeNavId'] = 'edit-nav';
            }
        }          

        header('Location: ../user-ui.php');
        exit();
} else {
    echo "<h1>Error! You cannot access this file!</h1>";
}