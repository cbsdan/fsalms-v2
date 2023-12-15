<?php 
    //use to filter record, if all, claim or unclaim
    if (isset($_POST['filter-record'])) {
        try {
            session_start();
            $database_path = '../database/config.php';
            $database_path_index = './database/config.php';
    
            if (file_exists($database_path)) {
                include($database_path);
            } else if (file_exists($database_path_index)){
                include($database_path_index);
            }
    
            $filterRecord = $_POST['filter-record'];  
            if ($filterRecord == 'Approved') {
                $_SESSION['status'] = 1;
            } else if ($filterRecord == 'Declined'){
                $_SESSION['status'] = 0;
            }
    
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';

        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to change status filter. Error: $e";
            $_SESSION['messageBg'] = 'red';
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';
        }

        header('Location: ../administrator-ui.php');
        exit();
    }  else {
        echo "<h1>Error! You cannot access this file!</h1>";
    }

?>