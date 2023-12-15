<?php
    session_start();
    $database_path = '../database/config.php';
    $database_path_index = './database/config.php';

    if (file_exists($database_path)) {
        include_once($database_path);
    } else if (file_exists($database_path_index)){
        include_once($database_path_index);
    }
    if (isset($_POST['approve'])) {

        try {
            $request_id = $_POST['request_id'];
            $sql = "UPDATE loan_requests SET request_status = 'Approved' WHERE request_id = $request_id";
            query($sql);

            $_SESSION['message'] = "Successfully updated the request status with the request ID of $request_id!";
            $_SESSION['messageBg'] = 'green';
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';
        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to update loan request. Error: $e";
            $_SESSION['messageBg'] = 'red';
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';
        }
        header('Location: ../administrator-ui.php');
        exit();
    } 
    
    if (isset($_POST['decline'])) {
        try {
            $request_id = $_POST['request_id'];
            $sql = "UPDATE loan_requests SET request_status = 'Declined' WHERE request_id = $request_id";
            query($sql);

            $_SESSION['message'] = "Successfully updated the request status with the request ID of $request_id!";
            $_SESSION['messageBg'] = 'green';
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';
        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to update loan request. Error: $e";
            $_SESSION['messageBg'] = 'red';
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';
        }
        header('Location: ../administrator-ui.php');
        exit();
    }

    if (isset($_POST['claim'])) {
        try {
            $request_id = $_POST['request_id'];
            $sql = "UPDATE loan_requests SET is_claim = 1, claimed_timestamp = NOW() WHERE request_id = $request_id";
            query($sql);

            $_SESSION['message'] = "Recorded! Successfully updated the claim status with the request ID of $request_id!";
            $_SESSION['messageBg'] = 'green';
            $_SESSION['section'] = './administrator/member-transactions.php';
            $_SESSION['activeNavId'] = 'm-transactions';
        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to claim loan request. Error: $e";
            $_SESSION['messageBg'] = 'red';
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';
        }
        header('Location: ../administrator-ui.php');
        exit();
    }

    if (isset($_POST['delete'])) {
        try {
            $request_id = $_POST['request_id'];

            $sql = "SELECT loan_detail_id FROM loan_requests WHERE request_id = $request_id";
            $results = $conn->query($sql);
            
            while ($loan_detail_ids = mysqli_fetch_assoc($results)) {
                $sql = "DELETE FROM loan_details WHERE loan_detail_id = ". $loan_detail_ids['loan_detail_id'];
                query($sql);
            }

            $_SESSION['message'] = "Successfully deleted loan request with id of $request_id!";
            $_SESSION['messageBg'] = 'green';
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';
        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to delete loan request. Error: $e";
            $_SESSION['messageBg'] = 'red';
            $_SESSION['section'] = './administrator/loan-requests.php';
            $_SESSION['activeNavId'] = 'l-requests';
        }
        header('Location: ../administrator-ui.php');
        exit();
    } 
        
    echo "<h1>Error! You cannot access this file!</h1>";

?>