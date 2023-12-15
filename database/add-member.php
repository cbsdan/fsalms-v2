<?php
    if (isset($_POST['add-btn'])) {
        session_start();
        $database_path = '../database/config.php';
        $database_path_index = './database/config.php';

        if (file_exists($database_path)) {
            include($database_path);
        } else if (file_exists($database_path_index)){
            include($database_path_index);
        }

        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $sex = $_POST['sex'];
        $birthdate = $_POST['birthdate'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $date_added = $_POST['date_added'];

        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($_FILES['user-profile']['tmp_name'] != '') {
            $profileData = $_FILES['user-profile']['tmp_name'];
            $profile = file_get_contents($profileData); // Convert image to BLOB
        } else {
            $profile = '';
        }

        $verify_query = $conn->query("SELECT username FROM accounts WHERE username='$username'");
        if(mysqli_num_rows($verify_query) != 0){
            $_SESSION['message'] = "There is an existing username with $username";
            $_SESSION['messageBg'] = 'red';
            $_SESSION['section'] = './administrator/administrator-add.php';
            $_SESSION['activeNavId'] = 'a-addMember';
            header('Location: ../administrator-ui.php');
            exit();
        }

        try {
            $sql = "INSERT INTO members (fname, lname, sex, birthdate, address, contact, date_added) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssss', $fname, $lname, $sex, $birthdate, $address, $contact, $date_added);
            $stmt->execute();
            
            $mem_id = $stmt->insert_id;

            $sql = "INSERT INTO accounts (username, password, profile, mem_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $username, $password, $profile, $mem_id);
            $stmt->execute();
            
            //get membership fee
            $sql = "SELECT membership_fee FROM system_info LIMIT 1;";
            $result = query($sql);
            $membership_fee = $result['membership_fee'];

            $sql = "INSERT INTO deposit (deposited, mem_id) VALUES ($membership_fee, $mem_id)";
            query($sql);
            
            $_SESSION['message'] = "Successfully added a new member";
            $_SESSION['messageBg'] = "green";
            $_SESSION['section'] = './administrator/administrator-add.php';
            $_SESSION['activeNavId'] = 'a-addMember';
            header('Location: ../administrator-ui.php');
            exit();

        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to add member. Error: $e";
            $_SESSION['messageBg'] = 'red';
            $_SESSION['section'] = './administrator/administrator-add.php';
            $_SESSION['activeNavId'] = 'a-addMember';
            header('Location: ../administrator-ui.php');
            exit();
        }
    } else {
        echo "<h1>Error! You cannot access this file!</h1>";
    }
?>