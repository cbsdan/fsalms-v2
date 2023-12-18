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

        $loan_detail_id = $_POST['loan_detail_id'];

        //check if pending request to continue
        $sql = "SELECT request_status FROM loan_requests WHERE loan_detail_id = $loan_detail_id";
        $result = $conn->query($sql);
        $result = $result->fetch_assoc();
        $request_status = $result['request_status'];

        if ($request_status == 'Pending') {
            $sql = "DELETE FROM loan_details WHERE loan_detail_id = $loan_detail_id";
            query($sql);
    
            $_SESSION['message'] = "Successfully canceled your request!";
            $_SESSION['messageBg'] = "green";
            
        } else {
            $_SESSION['message'] = "Failed to canceled your request! The loan request was approved!";
            $_SESSION['messageBg'] = "red";
        }

        $_SESSION['section'] = './user/request_section.php';
        $_SESSION['activeNavId'] = 'request-nav';
        header('Location: ../user-ui.php');
        exit();
    } else {
        echo "<h1>Error! You cannot access this file!</h1>";
    }

?>