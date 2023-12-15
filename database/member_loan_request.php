<?php
if (isset($_POST['request-btn'])) {
    session_start();
    $database_path = '../database/config.php';
    $database_path_index = './database/config.php';

    if (file_exists($database_path)) {
        include($database_path);
    } else if (file_exists($database_path_index)){
        include($database_path_index);
    }

    try {
        $member_username = $_SESSION['valid'];
        
        $sql = "SELECT mem_id FROM accounts WHERE username = '$member_username'";
        $result = query($sql);

        $memId = $result['mem_id'];
        $amount = $_POST["amount"];
        $duration = $_POST["duration"];
        $claimDate = $_POST["claim-date"];

        $currentDate = $_POST['date_requested'];
        $loanStatus = 'Pending';
        
        switch ($duration) {
            case 1:
                $interestRate = 10;
                break;
            case 2:
                $interestRate = 15;
                break;
            case 3:
                $interestRate = 20; 
                break;
            default: 
                $interestRate = 25;
        } 


        // Insert into loan_details without assuming a foreign key relationship
        $sql = "INSERT INTO loan_details (loan_amount, month_duration, date_requested, claim_date, interest_rate) VALUES ('{$amount}', '{$duration}', '{$currentDate}', '{$claimDate}', '{$interestRate}')";
        query($sql);

        $sql = "SELECT max(loan_detail_id) AS loan_detail_id FROM loan_details;";
        $loanDetailId = query($sql);
        $loanDetailId = $loanDetailId['loan_detail_id']; 
        
        // Insert into loan_requests
        $sql = "INSERT INTO loan_requests (mem_id, request_status, loan_detail_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $memId, $loanStatus, $loanDetailId);
        $stmt->execute();

        $_SESSION['message'] = "Successfully applied loan request!";
        $_SESSION['messageBg'] = 'green';
        $_SESSION['section'] = './user/request_section.php';
        $_SESSION['activeNavId'] = 'request-nav';
        

    } catch (Exception $e) {
        $_SESSION['message'] = "Failed to request loan. Error: $e";
        $_SESSION['messageBg'] = 'red';
        $_SESSION['section'] = './user/request_section.php';
        $_SESSION['activeNavId'] = 'request-nav';
    }

    header('Location: ../user-ui.php');
    exit();
} else {
    echo "<h1>Error! You cannot access this file!</h1>";
}
?>