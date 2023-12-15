<?php 
    if (isset($_POST['transactionBtn']) && $_POST['transactionBtn'] == 'edit') {
        session_start();
        $database_path = '../database/config.php';
        $database_path_index = './database/config.php';

        if (file_exists($database_path)) {
            include($database_path);
        } else if (file_exists($database_path_index)){
            include($database_path_index);
        }

        try {
            $transactionId = $_POST['transac-id'];
            $transactionType = $_POST['transac-type'];
            $memId = $_POST['mem_id'];
    
            if ($transactionType == 'Deposits') {
                $sql = "SELECT *, 'Deposits' AS transacType, d.deposit_id AS transacId, d.deposited AS transacAmount, DATE(d.deposit_timestamp) AS transacDate FROM members m INNER JOIN deposit d ON d.mem_id = m.mem_id WHERE m.mem_id = $memId AND d.deposit_id = $transactionId";
            } else if ($transactionType == 'Loan') {
                $sql = "SELECT *, 'Loan' AS transacType, ld.loan_detail_id AS transacId, ld.loan_amount AS transacAmount, ld.date_requested AS transacDate FROM members m INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id WHERE m.mem_id = $memId AND ld.loan_detail_id = $transactionId";
            } else {
                $sql = "SELECT *, 'Loan Payment' AS transacType, lp.payment_id AS transacId, lp.payment_amount AS transacAmount, DATE(lp.payment_timestamp) AS transacDate FROM members m INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id WHERE m.mem_id = $memId AND lp.payment_id = $transactionId";
            }
    
            $transactionInfo = query($sql);
            $_SESSION['transactionInfo'] = $transactionInfo;
            

        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to fetch transaction information! Error: $e";
            $_SESSION['messageBg'] = "red";
        }

        $_SESSION['section'] = './administrator/administrator-edit-transaction.php';
        $_SESSION['activeNavId'] = 'a-editTransaction';
        header('Location: ../administrator-ui.php');
        exit();
    }  else {
        echo "<h1>Error! You cannot access this file!</h1>";
    }
?>