<?php
    if (isset($_POST['select'])) {
        try {
            session_start();
            require_once('./config.php');
            $mem_id = $_POST['mem_id'];
            
            $sql = "SELECT loan_detail_id FROM loan_requests WHERE mem_id = $mem_id";
            $results = $conn->query($sql);
            
            while ($loan_detail_ids = mysqli_fetch_assoc($results)) {
                $sql = "DELETE FROM loan_details WHERE loan_detail_id = ". $loan_detail_ids['loan_detail_id'];
                query($sql);
  
            }
    
            $sql = "DELETE FROM accounts WHERE mem_id = $mem_id";
            query($sql);
    
            $sql = "DELETE FROM deposit WHERE mem_id = $mem_id";
            query($sql);
    
            $sql = "DELETE FROM loan_requests WHERE mem_id = $mem_id";
            query($sql);
    
            $sql = "DELETE FROM loan_payment WHERE mem_id = $mem_id";
            query($sql);
    
            $sql = "DELETE FROM members WHERE mem_id = $mem_id";
            query($sql);

            $_SESSION['message'] = "Successfully delete a user!";
            $_SESSION['messageBg'] = 'green';
        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to delete a user. Error: $e";
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