<?php
    session_start();
    $database_path = '../database/config.php';
    $database_path_index = './database/config.php';

    if (file_exists($database_path)) {
        include($database_path);
    } else if (file_exists($database_path_index)){
        include($database_path_index);
    }

    if (isset($_POST['edit-btn'])) {

        try {
            $mem_id = $_POST['mem_id'];
            $transaction_id = $_POST['transaction-id'];
            $transaction_amount = $_POST['transaction-amount'];
            $transaction_type = $_POST['transaction-type'];
    
            if ($transaction_type == 'Deposits') {
                $sql = "UPDATE deposit SET deposited = $transaction_amount WHERE mem_id = $mem_id AND deposit_id = $transaction_id";
            } else if ($transaction_type == 'Loan') {
                $interest_rate = $_POST['interest-rate'];
                $sql = "UPDATE loan_details SET loan_amount = $transaction_amount, interest_rate = $interest_rate WHERE loan_detail_id = $transaction_id";
            } else {
                $sql = "UPDATE loan_payment SET payment_amount = $transaction_amount WHERE mem_id = $mem_id AND payment_id = $transaction_id";
            }
            
            query($sql);

            //UPDATE LOAN STATUS IF THE UPDATED LOAN AMOUNT IS GREATER THAN TOTAL LOAN PAYMENT OR LESS THAN LOAN PAYMENT 
            if ($transaction_type == 'Loan') {
                //Fetch the total amount paid by member
                
                $sql = "SELECT SUM(lp.payment_amount) AS total_paid FROM loan_payment lp INNER JOIN members m ON m.mem_id = lp.mem_id WHERE m.mem_id = $mem_id AND lp.loan_detail_id = $transaction_id";
                $result = query($sql);
                $total_paid = $result['total_paid'];

                if (!isset($result['total_paid'])) { 
                    $total_paid = 0;
                }

                //Fetch the loan amount of loan by member
                $sql = "SELECT ROUND(loan_amount + (loan_amount * (interest_rate / 100)), 2) AS total_loan FROM loan_details WHERE loan_detail_id = $transaction_id";
                $result = query($sql);
                $total_loan = $result['total_loan'];

                if ($total_paid < $total_loan ) {
                    $sql = "UPDATE loan_details SET is_paid = 0 WHERE loan_detail_id = $transaction_id";
                } else {
                    $sql = "UPDATE loan_details SET is_paid = 1 WHERE loan_detail_id = $transaction_id";
                }
                query($sql);
            }

            //UPDATE LOAN STATUS IF TOTAL LOAN PAYMENT IS GREATER THAN (PAID) OR LESS THAN (UNPAID) 
            if ($transaction_type == 'Loan Payment') {
                $sql = "SELECT loan_detail_id FROM loan_payment WHERE payment_id = $transaction_id";

                $result = query($sql);
                $transaction_id = $result['loan_detail_id'];

                //Fetch the total amount paid by member
                $sql = "SELECT SUM(lp.payment_amount) AS total_paid FROM loan_payment lp INNER JOIN members m ON m.mem_id = lp.mem_id WHERE m.mem_id = $mem_id AND lp.loan_detail_id = $transaction_id";
                $result = query($sql);
                $total_paid = $result['total_paid'];

                if (!isset($result['total_paid'])) { 
                    $total_paid = 0;
                }

                //Fetch the loan amount of loan by member
                $sql = "SELECT ROUND(loan_amount + (loan_amount * (interest_rate / 100)), 2) AS total_loan FROM loan_details WHERE loan_detail_id = $transaction_id";
                $result = query($sql);
                $total_loan = $result['total_loan'];

                if ($total_paid >= $total_loan ) {
                    $sql = "UPDATE loan_details SET is_paid = 1 WHERE loan_detail_id = $transaction_id";
                } else {
                    $sql = "UPDATE loan_details SET is_paid = 0 WHERE loan_detail_id = $transaction_id";
                }
                query($sql);
            }
            
            updateMemberStatus($conn);
            
            $_SESSION['message'] = "Successfully updated transaction!";
            $_SESSION['messageBg'] = "green";
        } catch(Exception $e) {
            $_SESSION['message'] = "Failed to fetch transaction information! Error: $e";
            $_SESSION['messageBg'] = "red";
        }

        $_SESSION['section'] = './administrator/administrator-edit-transaction.php';
        $_SESSION['activeNavId'] = 'a-editTransaction';
        header('Location: ../administrator-ui.php');
        exit();
    }
    
    if (isset($_POST['delete-btn'])) {
        try {
            $mem_id = $_POST['mem_id'];
            $transaction_id = $_POST['transac-id'];
            $transaction_type = $_POST['transac-type'];

            if ($transaction_type == 'Deposits') {
                $sql = "DELETE FROM deposit WHERE mem_id = $mem_id AND deposit_id = $transaction_id";
            } else if ($transaction_type == 'Loan') {
                $sql = "DELETE FROM loan_details WHERE loan_detail_id = $transaction_id";
            } else {
                //SELECT FIRST THE LOAN DETAIL ID TO UPDATE LOAN STATUS AFTER UPDATED
                $sql = "SELECT loan_detail_id FROM loan_payment WHERE payment_id = $transaction_id";
                $result = query($sql);
                $loan_detail_id = $result['loan_detail_id'];

                $sql = "DELETE FROM loan_payment WHERE mem_id = $mem_id AND payment_id = $transaction_id";
            }

            query($sql);

            //UPDATE LOAN STATUS IF TOTAL LOAN PAYMENT IS GREATER THAN (PAID) OR LESS THAN (UNPAID) 
            if ($transaction_type == 'Loan Payment') {
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
                } else {
                    $sql = "UPDATE loan_details SET is_paid = 0 WHERE loan_detail_id = $loan_detail_id";
                }
                query($sql);
            }

            $_SESSION['message'] = "Successfully deleted transaction!";
            $_SESSION['messageBg'] = "green";
        } catch(Exception $e) {
            $_SESSION['message'] = "Failed to delete transaction! Error: $e";
            $_SESSION['messageBg'] = "red";
        }

        $_SESSION['section'] = './administrator/administrator-edit-transaction.php';
        $_SESSION['activeNavId'] = 'a-editTransaction';
        header('Location: ../administrator-ui.php');
        exit();
    }

    echo "<h1>Error! You cannot access this file!</h1>";

?>