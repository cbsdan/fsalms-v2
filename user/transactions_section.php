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

$username = $_SESSION['valid'];

if (isset($_SESSION['limit'])) {
     $limit = $_SESSION['limit'];
    $_SESSION['limit'] = null;
} else {
    $limit = 25;
}
//DEFAULT SQL COMMAND, IF ADMIN DOESN'T SEARCH AND FILTER ACTIVITY
$sql = "SELECT * FROM (
            SELECT 'Deposits' as activity, a.username, d.deposit_id AS transaction_id, m.mem_id, CONCAT(m.fname, ' ', m.lname) AS name, d.deposited AS amount, d.deposit_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN deposit d ON d.mem_id = a.mem_id

            UNION 

            SELECT 'Loan Payment' as activity, a.username, lp.payment_id AS transaction_id, m.mem_id, CONCAT(m.fname, ' ', m.lname) AS name, lp.payment_amount AS amount, lp.payment_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id

            UNION 

            SELECT 'Loan' as activity, a.username, ld.loan_detail_id AS transaction_id, m.mem_id, CONCAT(m.fname, ' ', m.lname) AS name, ld.loan_amount AS amount, lr.claimed_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
            INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
            WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
        ) AS combined_result
        WHERE username = '$username'
        ORDER BY date DESC
        LIMIT $limit;";


if (isset($_GET['select-count'])) {
    $_SESSION['limit'] = $_GET['select-count'];

    $_SESSION['section'] = './user/transactions_section.php';
    $_SESSION['activeNavId'] = 'transaction-nav';
    header('Location: ../user-ui.php');
    exit();
}
//SQL COMMAND IF ADMIN FILTER ACTIVITY, ALL, DEPOSITS, LOAN OR LOAN PAYMENT
if (isset($_SESSION['activity'])) {
    //if all is selected in filtering activity it will assigned null else it will assigned deposits, loan or loan payment
    $activity = $_SESSION['activity'];
    $activity = ($activity != 'all') ? $_SESSION['activity'] : null; 

    if ($activity != null) {
        //If admin want to filter record whether it is approved or declined, this will be the query:
        $sql = "SELECT * FROM (
            SELECT 'Deposits' as activity, a.username, d.deposit_id AS transaction_id, m.mem_id, CONCAT(m.fname, ' ', m.lname) AS name, d.deposited AS amount, d.deposit_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN deposit d ON d.mem_id = a.mem_id

            UNION 

            SELECT 'Loan Payment' as activity, a.username, lp.payment_id AS transaction_id, m.mem_id, CONCAT(m.fname, ' ', m.lname) AS name, lp.payment_amount AS amount, lp.payment_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id

            UNION 

            SELECT 'Loan' as activity, a.username, ld.loan_detail_id AS transaction_id, m.mem_id, CONCAT(m.fname, ' ', m.lname) AS name, ld.loan_amount AS amount, lr.claimed_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
            INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
            WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
        ) AS combined_result
        WHERE activity = '$activity' AND username = '$username' 
        ORDER BY date DESC
        LIMIT $limit;";
    }
}

$transactions = $conn->query($sql);
?>

<div class="background">
    <h1 class="transactions-title title">Transactions</h1>
    <hr>
    <div class="transactions-container content">
        <form action="./database/filter-transactions.php" method="POST" id='filterTransaction'>
            <input type='hidden' name='activeSection' value='./user/transactions_section.php'>
            <select class="options select-input" name="transaction-options" id="transaction-type">
                <option value="all" class="option" selected>All</option>
                <option value="Deposits" class="option" <?php echo (isset($activity) && $activity == 'Deposits') ? 'selected' : '' ?>>Deposits</option>
                <option value="Loan" class="option" <?php echo (isset($activity) && $activity == 'Loan') ? 'selected' : '' ?>>Loan</option>
                <option value="Loan Payment" class="option" <?php echo (isset($activity) && $activity == 'Loan Payment') ? 'selected' : '' ?>>Loan Payment</option>
            </select> 
        </form>
        <div class="transactions-table content result">
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (mysqli_num_rows($transactions) > 0) {
                            while($transaction = $transactions->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . $transaction['activity'] . "</td>
                                    <td>" . $transaction['transaction_id'] . "</td>
                                    <td>₱" . $transaction['amount'] . "</td>
                                    <td>" . $transaction['date'] . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='no-result-label text-center'>No transactions</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div class='load-more mt-3'>
        <form id='form-limit-transactions' action='./user/transactions_section.php' method='GET'>
            <label for='select-count'>Limit Transactions: </label>
            <select id='select-count' name='select-count'>
                <option value="25" <?php echo ($limit == '25' ? 'selected' : '')?>>25</option>
                <option value="50" <?php echo ($limit == '50' ? 'selected' : '')?>>50</option>
                <option value="100" <?php echo ($limit == '100' ? 'selected' : '')?>>100</option>
                <option value="10000" <?php echo ($limit == '10000' ? 'selected' : '')?>>All</option>
            </select>
            <button type='submit' name='load-more' class='bg-green'>Apply</button>
        </form>    
    </div>
    </div>
</div>

<script>
    //select-option tag
    document.getElementById('transaction-type').addEventListener('change', ()=>{
        document.getElementById('filterTransaction').submit();

    });
</script>