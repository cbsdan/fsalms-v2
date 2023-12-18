<?php
$authentication_path = '../functions/user-authenticate.php';
$authentication_path_index = './functions/user-authenticate.php';
if (file_exists($authentication_path)) {
    include_once("$authentication_path");
} elseif (file_exists($authentication_path_index)) {
    include_once("$authentication_path_index");
}

$database_path = '../database/config.php';
$database_path_index = './database/config.php';

if (file_exists($database_path)) {
    include_once($database_path);
} else {
    include_once($database_path_index);
}

//FOR SEARCH 
if (isset($_GET['search'])) {
    $searchValue = $_GET['search-value'];
    $searchType = $_GET['search-type'];
        
} 

if (isset($_GET['searchValue']) && isset($_GET['searchType'])) {
    $searchValue = $_SESSION['searchValue'] = $_GET['searchValue'];
    $searchType = $_SESSION['searchType'] = $_GET['searchType'];

    $_SESSION['section'] = './administrator/member-transactions.php';
    $_SESSION['activeNavId'] = 'm-transactions';
    header('Location: ../administrator-ui.php');
    exit();
}

//DEFAULT SQL COMMAND, IF ADMIN DOESN'T SEARCH AND FILTER ACTIVITY
$sql = "SELECT * FROM (
            SELECT 'Deposits' as activity, d.deposit_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN deposit d ON d.mem_id = a.mem_id

            UNION 

            SELECT 'Loan Payment' as activity, lp.payment_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, lp.payment_amount AS amount, lp.payment_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id

            UNION 

            SELECT 'Loan' as activity, ld.loan_detail_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
            INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
            WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
        ) AS combined_result

        ORDER BY date DESC;";

//SQL COMMAND IF ADMIN FILTER ACTIVITY, ALL, DEPOSITS, LOAN OR LOAN PAYMENT
if (isset($_SESSION['activity'])) {
    //if all is selected in filtering activity it will assigned null else it will assigned deposits, loan or loan payment
    $activity = $_SESSION['activity'];
    $activity = ($activity != 'all') ? $_SESSION['activity'] : null; 

    if ($activity != null) {
        //If admin want to filter record whether it is approved or declined, this will be the query:
        $sql = "SELECT * FROM (
            SELECT 'Deposits' as activity, d.deposit_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN deposit d ON d.mem_id = a.mem_id

            UNION 

            SELECT 'Loan Payment' as activity, lp.payment_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, lp.payment_amount AS amount, lp.payment_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id

            UNION 

            SELECT 'Loan' as activity, ld.loan_detail_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
            INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
            WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
        ) AS combined_result
        WHERE activity = '$activity'
        ORDER BY date DESC;";
    }
}

//SQL COMMAND IF ADMIN SEARCH A MEMBER TRANSACTIONS 
if (isset($_SESSION['searchValue']) && isset($_SESSION['searchType'])) {
    $searchValue = $_SESSION['searchValue'];
    $searchType = $_SESSION['searchType'];

    if ($searchValue != '') {
        //searchtype is name
        $sql = "SELECT * FROM (
            SELECT 'Deposits' as activity, d.deposit_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN deposit d ON d.mem_id = a.mem_id

            UNION 

            SELECT 'Loan Payment' as activity, lp.payment_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, lp.payment_amount AS amount, lp.payment_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id

            UNION 

            SELECT 'Loan' as activity, ld.loan_detail_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
            INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
            WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
        ) AS combined_result
        WHERE $searchType LIKE '%$searchValue%'
        ORDER BY date DESC;";
    }

} 

//SQL COMMAND IF ADMIN SEARCH A MEMBER TRANSACTIONS AND ALSO FILTER ACTIVITY
if (isset($activity) && isset($_SESSION['searchValue']) && isset($_SESSION['searchType'])) {
    $searchType = $_SESSION['searchType'];
    $searchValue = $_SESSION['searchValue'];

    if ($searchValue != '' && $activity != null) {
        $sql = "SELECT * FROM (
            SELECT 'Deposits' as activity, d.deposit_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname), AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN deposit d ON d.mem_id = a.mem_id

            UNION 

            SELECT 'Loan Payment' as activity, lp.payment_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, lp.payment_amount AS amount, lp.payment_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id

            UNION 

            SELECT 'Loan' as activity, ld.loan_detail_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
            INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
            WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
        ) AS combined_result
        WHERE activity = '$activity' AND $searchType LIKE '%$searchValue%'
        ORDER BY date DESC;";
    }
}

// echo $sql;
// exit();
$transactions = $conn->query($sql);
?>

<h1>Member Transactions</h1>
<hr>
<div class="member-transactions">
    <form action="./administrator/member-transactions.php" method="GET">
        <div class="search-section">
            <input class="search-input" type="text" name='searchValue' placeholder="Search here" value='<?php echo (isset($searchValue)) ? $searchValue : ''?>'>
            <select class="options select-input" name='searchType'>
                <option value="mem_id" name="id" class="option" <?php echo (isset($searchType)) ? (($searchType == 'mem_id') ? 'selected' : '') : ''?>>ID</option>
                <option value="name" name="name" class="option" <?php echo (isset($searchType)) ? (($searchType == 'name') ? 'selected' : '') : 'selected'?>>Name</option>
            </select> 
        </div>
    </form>

    <div class="transaction-table">
        <form action="./database/filter-transactions.php" method="POST" id='filterTransaction'>
            <select class="options select-input" name="transaction-options" id="transaction-type">
                <option value="all" class="option" selected>All</option>
                <option value="Deposits" class="option" <?php echo (isset($activity) && $activity == 'Deposits') ? 'selected' : '' ?>>Deposits</option>
                <option value="Loan" class="option" <?php echo (isset($activity) && $activity == 'Loan') ? 'selected' : '' ?>>Loan</option>
                <option value="Loan Payment" class="option" <?php echo (isset($activity) && $activity == 'Loan Payment') ? 'selected' : '' ?>>Loan Payment</option>
            </select> 
        </form>
        <div class="result" id="member-transactions-table">
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Type</th>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Member Type</th>
                        <th>Amount</th>
                        <th>Date</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (mysqli_num_rows($transactions) > 0) {
                            while($transaction = $transactions->fetch_assoc()) {
                                if ($transaction['profile'] != "") {
                                    $profileSrc = getImageSrc($transaction['profile']);
                                } else {
                                    $profileSrc = "./img/default-profile.png";
                                }
                                
                                if ($transaction['is_temp_mem'] == 0 || $transaction['is_temp_mem'] == false ) {
                                    $member_type = 'Member';
                                } else {
                                    $member_type = 'Temporary';
                                }

                                echo "<tr>
                                        <td><img class='m-auto' src='" . $profileSrc . "'></td>
                                        <td>" . $transaction['activity'] . "</td>
                                        <td>" . $transaction['mem_id'] . "</td>
                                        <td>" . $transaction['name'] . "</td>
                                        <td class='text-center'>" . $member_type . "</td>
                                        <td>₱" . $transaction['amount'] . "</td>
                                        <td>" . $transaction['date'] . "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<td colspan='6' class='no-result-label text-center'>No Transaction Found!</td>";
                        }
                    ?>
            </table>
        </div>
    </div>
</div>


<script>
    //select-option tag
    document.getElementById('transaction-type').addEventListener('change', ()=>{
        document.getElementById('filterTransaction').submit();

    });
</script>