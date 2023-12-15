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

        try {
            $mem_id = $_POST['mem_id'];
            $loan_detail_id = $_POST['loan_detail_id'];
            $payment_amount = $_POST['payment-amount'];
    
            $sql = "INSERT INTO loan_payment(payment_amount, loan_detail_id, mem_id) VALUES ((?), (?), (?))";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $payment_amount, $loan_detail_id, $mem_id);
            $stmt->execute();
            
            //Fetch the total amount paid by member
            $sql = "SELECT SUM(lp.payment_amount) AS total_paid FROM loan_payment lp INNER JOIN members m ON m.mem_id = lp.mem_id WHERE m.mem_id = $mem_id AND lp.loan_detail_id = $loan_detail_id";
            $result = query($sql);
            $total_paid = $result['total_paid'];

            if (!isset($result['total_paid'])) { 
                $total_paid = 0;
            }

            //Fetch the loan amount of loan by member
            $sql = "SELECT ROUND(loan_amount + (loan_amount * (interest_rate / 100)), 2) AS total_loan FROM loan_details WHERE loan_detail_id = $loan_detail_id";
            $result = query($sql);
            $total_loan = $result['total_loan'];

            if ($total_paid >= $total_loan ) {
                $sql = "UPDATE loan_details SET is_paid = 1 WHERE loan_detail_id = $loan_detail_id";
                query($sql);
            }

            $_SESSION['message'] = "Successfully recorded loan payment";
            $_SESSION['messageBg'] = "green";
            //THIS WILL REDIRECT THE ADMIN TO MEMBER TRANSACTIONS TO SEE IF IT SUCCESSFULLY INSERTED
            //erase the previous transaction type filter, search value and search type in member transaction
            $_SESSION['searchValue'] = null;
            $_SESSION['searchType'] = null;
            $_SESSION['activity'] = null;

            $_SESSION['section'] = './administrator/member-transactions.php';
            $_SESSION['activeNavId'] = 'm-transactions';

        } catch(Exception $e) {
            $_SESSION['message'] = "Failed to record loan payment. Error: $e";
            $_SESSION['messageBg'] = "red";
            $_SESSION['section'] = './administrator/loan-pay.php';
            $_SESSION['activeNavId'] = 'l-payment';
        }
        
        header("Location: ../administrator-ui.php");
        exit();
    }  else {
        echo "<h1>Error! You cannot access this file!</h1>";
    }
?>