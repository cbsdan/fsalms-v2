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
    
    // if (isset($_GET['search'])) {
    //     $searchValue = $_GET['search-value'];
    //     $searchType = $_GET['search-type'];
    // } 

    //FOR SEARCH 
        
    $limit = 25;

    if (isset($_GET['searchValue']) || isset($_GET['searchType']) || isset($_GET['load-more'])) {
        
        if (isset($_GET['searchValue']) && isset($_GET['searchType'])) {
            $searchValue = $_SESSION['searchValueTrans'] = $_GET['searchValue'];
            $searchType = $_SESSION['searchTypeTrans'] = $_GET['searchType'];
        
        } else if (isset ($_GET['load-more'])) {
            $limit = $_SESSION['limit'] = $_GET['select-count'];    
            
        }else if (isset($_GET['searchValue']) && isset($_GET['searchType']) && isset($_GET['load-more'])) { 
            $searchValue = $_SESSION['searchValueTrans'] = $_GET['searchValue'];
            $searchType = $_SESSION['searchTypeTrans'] = $_GET['searchType'];
            $limit = $_SESSION['limit'] = $_GET['select-count'];    
        }
    
        $_SESSION['section'] = './administrator/administrator-edit-transaction.php';
        $_SESSION['activeNavId'] = 'a-editTransaction';
        header('Location: ../administrator-ui.php');
        exit();
    
    }

    //DEFAULT SQL COMMAND, IF ADMIN DOESN'T SEARCH AND FILTER ACTIVITY
    $sql = "SELECT * FROM (
                SELECT 'Deposits' as activity, d.deposit_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN deposit d ON d.mem_id = a.mem_id
    
                UNION 
    
                SELECT 'Loan Payment' as activity, lp.payment_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem,lp.payment_amount AS amount, lp.payment_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id
    
                UNION 
    
                SELECT 'Loan' as activity, ld.loan_detail_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name,m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
                INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
                WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
            ) AS combined_result
    
            ORDER BY date DESC
            LIMIT $limit";
    
    if (isset($_SESSION['limit'])) {
        $limit = $_SESSION['limit'];
        $_SESSION['limit'] = null;
        
        $sql = "SELECT * FROM (
            SELECT 'Deposits' as activity, d.deposit_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN deposit d ON d.mem_id = a.mem_id

            UNION 

            SELECT 'Loan Payment' as activity, lp.payment_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem,lp.payment_amount AS amount, lp.payment_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id

            UNION 

            SELECT 'Loan' as activity, ld.loan_detail_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name,m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
            FROM members m 
            INNER JOIN accounts a ON a.mem_id = m.mem_id 
            INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
            INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
            WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
        ) AS combined_result

        ORDER BY date DESC;
        LIMIT $limit";
    }

    //SQL COMMAND IF ADMIN FILTER ACTIVITY, ALL, DEPOSITS, LOAN OR LOAN PAYMENT
    if (isset($_SESSION['activity'])) {
        //if all is selected in filtering activity it will assigned null else it will assigned deposits, loan or loan payment
        $activity = $_SESSION['activity'];
        $activity = ($activity != 'all') ? $_SESSION['activity'] : null; 
    
        if ($activity != null) {
            //If admin want to filter record whether it is approved or declined, this will be the query:
            $sql = "SELECT * FROM (
                SELECT 'Deposits' as activity, d.deposit_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN deposit d ON d.mem_id = a.mem_id
    
                UNION 
    
                SELECT 'Loan Payment' as activity, lp.payment_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, lp.payment_amount AS amount, lp.payment_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id
    
                UNION 
    
                SELECT 'Loan' as activity, ld.loan_detail_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
                INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
                WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
            ) AS combined_result
            WHERE activity = '$activity'
            ORDER BY date DESC
            LIMIT $limit;";
        }
    }
    
    //SQL COMMAND IF ADMIN SEARCH A MEMBER TRANSACTIONS 
    if (isset($_SESSION['searchValueTrans']) && isset($_SESSION['searchTypeTrans'])) {
        $searchValue = $_SESSION['searchValueTrans'];
        $searchType = $_SESSION['searchTypeTrans'];
    
        $_SESSION['searchValueTrans'] = null;
        $_SESSION['searchTypeTrans'] = null;

        if (isset($_SESSION['limit'])) {
            $limit = $_SESSION['limit'];
            $_SESSION['limit'] = null;
        }

        if ($searchValue != '') {
            //searchtype is name
            $sql = "SELECT * FROM (
                SELECT 'Deposits' as activity, d.deposit_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN deposit d ON d.mem_id = a.mem_id
    
                UNION 
    
                SELECT 'Loan Payment' as activity, lp.payment_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, lp.payment_amount AS amount, lp.payment_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id
    
                UNION 
    
                SELECT 'Loan' as activity, ld.loan_detail_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
                INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
                WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
            ) AS combined_result
            WHERE $searchType LIKE '%$searchValue%'
            ORDER BY date DESC
            LIMIT $limit;";
        }
    
    } 
    
    //SQL COMMAND IF ADMIN SEARCH A MEMBER TRANSACTIONS AND ALSO FILTER ACTIVITY
    if (isset($activity) && isset($_SESSION['searchValueTrans']) && isset($_SESSION['searchTypeTrans'])) {
        $searchType = $_SESSION['searchTypeTrans'];
        $searchValue = $_SESSION['searchValueTrans'];
    
        $_SESSION['searchTypeTrans'] = null;
        $_SESSION['searchValueTrans'] = null;

        if (isset($_SESSION['limit'])) {
            $limit = $_SESSION['limit'];
            $_SESSION['limit'] = null;
        }

        if ($searchValue != '' && $activity != null) {
            $sql = "SELECT * FROM (
                SELECT 'Deposits' as activity, d.deposit_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, d.deposited AS amount, d.deposit_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN deposit d ON d.mem_id = a.mem_id
    
                UNION 
    
                SELECT 'Loan Payment' as activity, lp.payment_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, lp.payment_amount AS amount, lp.payment_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN loan_payment lp ON lp.mem_id = m.mem_id
    
                UNION 
    
                SELECT 'Loan' as activity, ld.loan_detail_id AS transaction_id, m.mem_id, a.profile, CONCAT(m.fname, ' ', m.lname) AS name, m.is_temp_mem, ld.loan_amount AS amount, lr.claimed_timestamp AS date
                FROM members m 
                INNER JOIN accounts a ON a.mem_id = m.mem_id 
                INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id
                INNER JOIN loan_details ld ON ld.loan_detail_id = lr.loan_detail_id
                WHERE lr.request_status = 'Approved' AND lr.is_claim = 1
            ) AS combined_result
            WHERE activity = '$activity' AND $searchType LIKE '%$searchValue%'
            ORDER BY date DESC
            LIMIT $limit;";
        }
    }
    
    $transactions = $conn->query($sql);

    if (isset($_SESSION['transactionInfo'])) {
        $transactionInfo = $_SESSION['transactionInfo'];
        $_SESSION['transactionInfo'] = null;
    }
?>

<h1>Edit Transaction</h1>
<hr>
<div class="select-transaction">
    <form action="./administrator/administrator-edit-transaction.php" method="GET">
        <div class="search-section">
            <input class="search-input" type="text" name='searchValue' placeholder="Search here" value='<?php echo (isset($searchValue)) ? $searchValue : ''?>'>
            <select class="options select-input" name='searchType'>
                <option value="mem_id" name="id" class="option" <?php echo (isset($searchType)) ? (($searchType == 'mem_id') ? 'selected' : '') : ''?>>ID</option>
                <option value="name" name="name" class="option" <?php echo (isset($searchType)) ? (($searchType == 'name') ? 'selected' : '') : 'selected'?>>Name</option>
            </select> 
        </div>
    </form>
    <form action="./database/filter-transactions.php" method="POST" id='filterTransaction'>
        <input type='hidden' name='activeSection' value='./administrator/administrator-edit-transaction.php'>
        <select class="options select-input" name="transaction-options" id="transaction-type">
            <option value="all" class="option" selected>All</option>
            <option value="Deposits" class="option" <?php echo (isset($activity) && $activity == 'Deposits') ? 'selected' : '' ?>>Deposits</option>
            <option value="Loan" class="option" <?php echo (isset($activity) && $activity == 'Loan') ? 'selected' : '' ?>>Loan</option>
            <option value="Loan Payment" class="option" <?php echo (isset($activity) && $activity == 'Loan Payment') ? 'selected' : '' ?>>Loan Payment</option>
        </select> 
    </form>
    <div class="result" id="transaction-container">
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
                    <th>Edit</th> 
                    <th>Delete</th> 
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
                                    <td>
                                        <form action='./database/fetch-a-transaction.php' method='POST'>
                                            <input type='hidden' name='mem_id' value = '". $transaction['mem_id']."'>
                                            <input type='hidden' name='transac-type' value = '". $transaction['activity']."'>
                                            <input type='hidden' name='transac-id' value = '". $transaction['transaction_id']."'>
                                            <button type='submit' name='transactionBtn' value='edit' class='bg-green m-auto'>Edit</button> 
                                        </form>
                                    <td>
                                        <form action='./database/update-transaction.php' method='POST' class='deleteTransactionForm'>
                                            <input type='hidden' class='mem_id' name='mem_id' value = '". $transaction['mem_id']."'>
                                            <input type='hidden' class='transac-type' name='transac-type' value = '". $transaction['activity']."'>
                                            <input type='hidden' class='transac-id' name='transac-id' value = '". $transaction['transaction_id']."'>
                                            <button type='submit' name='delete-btn' value='delete' class='bg-red m-auto'>Delete</button>
                                        </form>
                                    </td> 
                                </tr>";
                        }
                    } else {
                        echo "<td colspan='8' class='no-result-label text-center'>No Transaction Found!</td>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    <div class='load-more mt-3'>
        <form id='form-limit-transactions' action='./administrator/member-transactions.php' method='GET'>
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
<div class="edit-transaction p-1rem <?php echo (isset($transactionInfo)) ? '' : 'hidden'?>">
    <h3 class="title">Edit Here</h3>
    <hr>
    <form action="./database/update-transaction.php" method="POST">
        <input type='hidden' name='mem_id' value='<?php echo $transactionInfo['mem_id']?>'>
        <input type='hidden' name='transaction-type' value='<?php echo $transactionInfo['transacType']?>'>
        <input type='hidden' name='transaction-id' value='<?php echo $transactionInfo['transacId']?>'>

        <div class="info">
            <label for="member-info">Member Name (ID):</label>
            <input type="text" name='member-info' value='<?php echo $transactionInfo['fname'] . " " . $transactionInfo['lname'] . " (" . $transactionInfo['mem_id'] . ")"?>' placeholder="Enter Type" disabled>
        </div>
        <div class="info">
            <label>Type:</label>
            <input type="text" value='<?php echo $transactionInfo['transacType'] ?>' placeholder="Enter Type" disabled>
        </div>
        <div class="info">
            <label>ID: </label>
            <input type="number" id="transaction-id" placeholder="Enter ID" value='<?php echo $transactionInfo['transacId'] ?>' disabled>
        </div>
        <div class="info">
            <label for="transaction-amount">Amount (Editable): <span class="required">*</span></label>
            <input type="number" id="transaction-amount" name='transaction-amount' class="no-spinner" value='<?php echo $transactionInfo['transacAmount'] ?>' placeholder="Enter Amount" required>
        </div>
        <div class="info <?php echo (($transactionInfo['transacType'] !== 'Loan') ? 'hidden' : '');?>">
            <label for="interest-rate">Rate (Editable): <span class="required">*</span></label>
            <input type="number" id="interest-rate" name='interest-rate' class="no-spinner" value='<?php echo (($transactionInfo['transacType'] == 'Loan') ? getLoanInterestRate($conn, $transactionInfo['transacId']) : 0) ?>' placeholder="Enter Interest Rate" required max=100 min=0>
        </div>
        <div class="info">
            <label>Timestamp: </label>
            <input type="date" id="transaction-timestamp" value='<?php echo $transactionInfo['transacDate'] ?>' disabled>
        </div>
        <button id="edit-btn" type="submit" name="edit-btn" value="submit">Apply</button>
    </form>
</div>

<script>
    //select-option tag
    document.getElementById('transaction-type').addEventListener('change', ()=>{
        document.getElementById('filterTransaction').submit();

    });

    let deleteTransactionForms = document.querySelectorAll('.deleteTransactionForm')
    deleteTransactionForms.forEach((deleteTransactionForm)=>{
        let transactionId = deleteTransactionForm.querySelector('.transac-id');
        let transactionType = deleteTransactionForm.querySelector('.transac-type');
        let mem_id = deleteTransactionForm.querySelector('.mem_id');

        deleteTransactionForm.addEventListener('submit', (e)=>{
            let confirmation = confirm(`Do you want to delete ${transactionType.value} with an ID of ${transactionId.value} and Member ID of ${mem_id.value}?`);

            if (!confirmation) {
                e.preventDefault();
            } 
        })
    })


</script>