<?php
    if (isset($_POST['select'])) {
        require_once('./config.php');
        session_start();
        $mem_id = $_POST['mem_id'];
        $_SESSION['section'] = $_POST['page'];
        $_SESSION['activeNavId'] = $_POST['activeNavId'];
        $_SESSION['mem_id'] = $mem_id;

        $fetchQuery = "SELECT m.mem_id, CONCAT(m.fname, ' ', m.lname) AS name,m.fname, m.lname, m.sex, m.address, m.contact, TIMESTAMPDIFF(YEAR, m.birthdate, CURDATE()) AS age, m.birthdate, m.date_added, a.username, a.profile, a.password FROM members m 
                        INNER JOIN accounts a ON m.mem_id = a.mem_id
                        WHERE m.mem_id = $mem_id";

        if (isset($_POST['loan_detail_id'])) {
            $loan_detail_id = $_POST['loan_detail_id'];
            $fetchQuery = "SELECT m.mem_id, lr.loan_detail_id, CONCAT(m.fname, ' ', m.lname) AS name,m.fname, m.lname, m.sex, m.address, m.contact, TIMESTAMPDIFF(YEAR, m.birthdate, CURDATE()) AS age, m.birthdate, m.date_added, a.username, a.profile, a.password FROM members m 
                            INNER JOIN accounts a ON m.mem_id = a.mem_id
                            INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
                            WHERE m.mem_id = $mem_id AND lr.loan_detail_id = $loan_detail_id ";
        }

        $result = $conn->query($fetchQuery);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['mem_info'] = $row;
        }
        header('Location: ../administrator-ui.php');
        exit();
    }  else {
        echo "<h1>Error! You cannot access this file!</h1>";
    }
?>