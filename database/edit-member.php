<?php
//FOR CHANGING ADMIN PASSWORD
if (isset($_POST['edit-btn'])) {
    try {
        require_once('./config.php');
        session_start();
        $memId = $_POST['mem_id'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $sex = $_POST['sex'];
        $birthdate = $_POST['birthdate'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];

        $old_username = $_POST['old_username'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($_FILES['user-profile']['tmp_name'] != '') {
            $profileData = $_FILES['user-profile']['tmp_name'];
            $profile = file_get_contents($profileData); // Convert image to BLOB
        } else {
            $profile = '';
        }

        if ($profile == '') {
            $stmt = $conn->prepare("UPDATE accounts SET username = (?), password = (?) WHERE username = (?);");
            $stmt->bind_param("sss", $username, $password, $old_username);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("UPDATE accounts SET username = (?), password = (?), profile = (?) WHERE username = (?);");  
            $stmt->bind_param("ssss", $username, $password, $profile, $old_username);
            $stmt->execute();
        }
        
        $stmt = $conn->prepare("UPDATE members SET fname = (?), lname = (?), sex = (?), birthdate = (?), address = (?), contact = (?) WHERE mem_id = (?);");
        $stmt->bind_param("sssssss", $fname, $lname, $sex, $birthdate, $address, $contact, $memId);
        $stmt->execute();

        $_SESSION['message'] = "Successfully update a user!";
        $_SESSION['messageBg'] = 'green';
    } catch (Exception $e) {
        $_SESSION['message'] = "Failed to update user. Error: $e";
        $_SESSION['messageBg'] = 'red';       
    }
    
    $_SESSION['section'] = './administrator/administrator-edit-member.php';
    $_SESSION['activeNavId'] = 'a-editMember';
    header('Location: ../administrator-ui.php');
    exit();
}  else {
    echo "<h1>Error! You cannot access this file!</h1>";
}

?>