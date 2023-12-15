<?php
    if (isset($_POST['changeSettings'])) {
        session_start();
        $database_path = '../database/config.php';
        $database_path_index = './database/config.php';

        if (file_exists($database_path)) {
            include($database_path);
        } else if (file_exists($database_path_index)){
            include($database_path_index);
        }

        $admin_username = $_SESSION['valid'];

        //FOR MODIFYING ADMIN USERNAME AND SYSTEM DATA
        if ($_POST['changeSettings'] == 'editAdmin') {
            $username = $_POST['admin-username'];
            $weekly_payment = $_POST['weekly-payment'];
            $membership_fee = $_POST['membership-fee'];
            $start_date = $_POST['starting-date'];
            $end_date = $_POST['ending-date'];
            $manager_percentage = $_POST['manager-percentage'];
            $members_percentage = $_POST['members-percentage'];

            if ($_FILES['admin-profile']['tmp_name'] != '') {
                $profileData = $_FILES['admin-profile']['tmp_name'];
                $profile = file_get_contents($profileData); // Convert image to BLOB
            } else {
                $profile = '';
            }
            try {
                if ($profile == '') {
                    $sql = "UPDATE accounts SET username = (?) WHERE username = '$admin_username';";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('s', $username);
                    $stmt->execute();

                } else {
                    $sql = "UPDATE accounts SET username = (?), profile = (?) WHERE username = '$admin_username';";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ss', $username, $profile);
                    $stmt->execute();

                }
                
                $sql = "UPDATE system_info SET weekly_payment = (?), membership_fee = (?), start_date = (?), end_date = (?), manager_percentage = (?), member_percentage = (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssii', $weekly_payment, $membership_fee, $start_date, $end_date, $manager_percentage, $members_percentage);
                $stmt->execute();
                
                $_SESSION['message'] = "Successfully modified admin and system data";
                $_SESSION['messageBg'] = "green";
                $_SESSION['section'] = './administrator/settings.php';
                $_SESSION['activeNavId'] = 'settings-nav';
                $_SESSION['valid'] = $username;
    
            } catch (Exception $e) {
                $_SESSION['message'] = "Failed to modify admin and system data. Error: $e";
                $_SESSION['messageBg'] = 'red';
                $_SESSION['section'] = './administrator/settings.php';
                $_SESSION['activeNavId'] = 'settings-nav';
            }
            header('Location: ../administrator-ui.php');
            exit();
        }
        if ($_POST['changeSettings'] == 'resetDb') {
            try {
                // Disable foreign key checks to allow truncating tables with foreign key constraints
                $conn->query("SET foreign_key_checks = 0");

                // Get a list of tables in the database
                $tables = $conn->query("SHOW TABLES");
                while ($row = $tables->fetch_assoc()) {
                    $table = $row["Tables_in_$database"];

                    // Truncate each table
                    $conn->query("TRUNCATE TABLE $table");
                }

                // Enable foreign key checks
                $conn->query("SET foreign_key_checks = 1");

                $_SESSION['message'] = "Successfully reset the database! Make a new admin account";
                $_SESSION['messageBg'] = 'green';
                header("Location: ./logout.php");
                exit();

            } catch (Exception $e) {
                $_SESSION['message'] = "Failed to reset. Error: $e";
                $_SESSION['messageBg'] = 'red';    
            }

            $_SESSION['section'] = './administrator/settings.php';
            $_SESSION['activeNavId'] = 'settings-nav';
            header('Location: ../administrator-ui.php');
            exit();
            
        }
        //FOR CHANGING ADMIN PASSWORD
        if ($_POST['changeSettings'] == 'changePw') {
            try {
                $old_password = query("SELECT password FROM accounts WHERE username = '" . $_SESSION['valid'] . "'");
                $old_password = $old_password['password'];
                $old_password_input = $_POST['old_password'];
    
                if ($old_password == $old_password_input) {
                    $new_password = $_POST['new_password'];
                    $confirm_password = $_POST['confirm_password'];
                    if ($new_password == $confirm_password) {
                        query("UPDATE accounts SET password = '$new_password' WHERE username = '" . $_SESSION['valid'] . "'");
                        $_SESSION['message'] = "Successfully changed password!";
                        $_SESSION['messageBg'] = 'green';
                        $_SESSION['section'] = './administrator/settings.php';
                        $_SESSION['activeNavId'] = 'settings-nav';
    
                    } else {
                        $_SESSION['message'] = "New password and confirm password mismatched!";
                        $_SESSION['messageBg'] = 'red';
                        $_SESSION['section'] = './administrator/settings.php';
                        $_SESSION['activeNavId'] = 'settings-nav';
                    }
                } else {
                    $_SESSION['message'] = "Old password does not matched!";
                    $_SESSION['messageBg'] = 'red';
                    $_SESSION['section'] = './administrator/settings.php';
                    $_SESSION['activeNavId'] = 'settings-nav';
                }
                
            } catch (Exception $e) {
                $_SESSION['message'] = "Error: $e";
                $_SESSION['messageBg'] = 'red';
                $_SESSION['section'] = './administrator/settings.php';
                $_SESSION['activeNavId'] = 'settings-nav';

            }

            header('Location: ../administrator-ui.php');
            exit();
        }
    } else {
        echo "<h1>Error! You cannot access this file!</h1>";
    }
?>