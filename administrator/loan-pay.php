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

if (isset($_GET['search'])) {
    $searchValue = $_GET['search-value'];
    $searchType = $_GET['search-type'];
    if ($searchType == 'name') {
        //searchtype is name
        $sql = "SELECT m.mem_id, m.is_temp_mem, ld.loan_detail_id, a.profile, lr.request_id, CONCAT(m.fname, ' ', m.lname) AS name, m.sex, TIMESTAMPDIFF(YEAR, m.birthdate, CURDATE()) AS age, ld.loan_amount, ld.month_duration, ld.interest_rate, ld.is_paid
                FROM members m 
                INNER JOIN loan_requests lr
                ON lr.mem_id = m.mem_id
                INNER JOIN accounts a
                ON a.mem_id = m.mem_id
                INNER JOIN loan_details ld
                ON ld.loan_detail_id = lr.loan_detail_id
                WHERE lr.request_status = 'Approved' AND lr.is_claim = 1 AND ld.is_paid = 0 AND CONCAT(m.fname, ' ', m.lname) LIKE '%$searchValue%'";
    } else {
        //searchtype is mem_id
        $sql = "SELECT m.mem_id, m.is_temp_mem, ld.loan_detail_id, a.profile, lr.request_id, CONCAT(m.fname, ' ', m.lname) AS name, m.sex, TIMESTAMPDIFF(YEAR, m.birthdate, CURDATE()) AS age, ld.loan_amount, ld.month_duration, ld.interest_rate, ld.is_paid
                FROM members m 
                INNER JOIN loan_requests lr
                ON lr.mem_id = m.mem_id
                INNER JOIN accounts a
                ON a.mem_id = m.mem_id
                INNER JOIN loan_details ld
                ON ld.loan_detail_id = lr.loan_detail_id
                WHERE lr.request_status = 'Approved' AND lr.is_claim = 1 AND ld.is_paid = 0 AND m.$searchType LIKE '%$searchValue%'";
    }
    $_SESSION['section'] = './administrator/loan-pay.php';
    $_SESSION['activeNavId'] = 'l-payment';
    $_SESSION['sql_command'] = $sql;
    $_SESSION['selectedSearchType'] = $searchType;
    header('Location: ../administrator-ui.php');
    exit();
} 

//Default SQL Command
$sql = "SELECT m.mem_id, m.is_temp_mem, ld.loan_detail_id, a.profile, lr.request_id, CONCAT(m.fname, ' ' , m.lname) AS name, m.sex, TIMESTAMPDIFF(YEAR, m.birthdate, CURDATE()) AS age, ld.loan_amount, ld.month_duration, ld.interest_rate, ld.is_paid
        FROM members m 
        INNER JOIN loan_requests lr
        ON lr.mem_id = m.mem_id
        INNER JOIN accounts a
        ON a.mem_id = m.mem_id
        INNER JOIN loan_details ld
        ON ld.loan_detail_id = lr.loan_detail_id
        WHERE lr.request_status = 'Approved' AND lr.is_claim = 1 AND ld.is_paid = 0";

$searchType = 'name';

//Search SQL Command
if (isset($_SESSION['sql_command']) && $_SESSION['selectedSearchType']) {
    $sql = $_SESSION['sql_command'];
    $searchType = $_SESSION['selectedSearchType'];
    $_SESSION['sql_command'] = null;
    $_SESSION['selectedSearchType'] = null;
}

if (isset($_SESSION['mem_info'])) {
    $memInfo = $_SESSION['mem_info'];
    $_SESSION['mem_info'] = null;
}

$result = $conn->query($sql);

//use to identify later if there is atleast a member fetch from database 
$isThereMember = false;
?>
<h1>Pay Loan</h1>
<hr>    
<div class="pay-loan">
    <div class="section first">
        <h3 class="title">1. Select a Member</h3>
        <div class="inner-section">
            <div class="left left-section">
                <form action="./administrator/loan-pay.php" method="GET">
                    <div class="search-section">
                        <input class="search-input" type="text" class="search-input" placeholder="Search here" name="search-value">
                        <select class="options select-input" name="search-type">
                            <option value="mem_id" class="option" <?php if ($searchType == 'mem_id') echo "selected"?>>ID</option>
                            <option value="name" class="option" <?php if ($searchType == 'name') echo "selected"?>>Name</option>
                        </select> 
                        <input type="submit" class="hidden" name="search" value="search">
                    </div>
                </form>
                <h4 class="mb-3">Total: <span class="value"><?php echo $result->num_rows?></span></h4>
                <div class="result">
                    <table class="result-table">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Member ID</th>
                                <th>Name</th>
                                <th>Member Type</th>
                                <th>Sex</th>
                                <th>Age</th> 
                                <th>Loan Detail ID</th>
                                <th>Loan Amount</th> 
                                <th>Month Duration</th> 
                                <th>Interest Rate</th> 
                                <th>Loan Balance</th>
                                <th>Select</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                               
                   
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $memId = $row['mem_id'];
                                        $loan_detail_id = $row['loan_detail_id'];

                                        $isSelected = false;

                                        if (isset($memInfo['loan_detail_id'])) {
                                            if ($loan_detail_id == $memInfo['loan_detail_id']) {
                                                $isSelected = true;
                                            }
                                        }

                                        if ($row['is_temp_mem'] == 0 || $row['is_temp_mem'] == false) { 
                                            $member_type = 'Member';
                                         } else {
                                            $member_type = 'Temporary';
                                         } 

                                        echo "<tr>";
                                        if (empty($row["profile"])) {
                                            echo "<td class='profile-img'><img src='./img/default-profile.png' alt='img'></td>";
                                        } else {
                                            $imgSrc = getImageSrc($row['profile']);
                                            echo "<td class='profile-img'><img src='$imgSrc' alt='img'></td>";
                                        }
                                        echo "<td>$memId</td>";
                                        echo "<td>" . $row['name']. "</td>";
                                        echo "<td class='text-center'>" . $member_type . "</td>";
                                        echo "<td>" . $row["sex"] . "</td>";
                                        echo "<td>" . $row['age']. "</td>";
                                        echo "<td>" . $row['loan_detail_id']. "</td>";
                                        echo "<td>₱" . $row['loan_amount']. "</td>";
                                        echo "<td class='text-center'>" . $row['month_duration']. "</td>";
                                        echo "<td class='text-center'>" . $row['interest_rate']. "%</td>";
                                        echo "<td class='text-center'>₱" . getLoanBalance($conn, $memId, $row['loan_detail_id']) . "</td>";
                                        echo "<td>
                                                <form action='database/fetch_member_info.php' method='POST'>
                                                    <input type='hidden' name='mem_id' value='$memId'>
                                                    <input type='hidden' name='loan_detail_id' value='$loan_detail_id '>
                                                    <input type='hidden' name='page' value='./administrator/loan-pay.php'>
                                                    <input type='hidden' name='activeNavId' value='l-payment'>
                                                    <button type='submit' name='select' value='select' class='select-btn m-auto ". ($isSelected ? "c-gold" : '') ."' " . ($isSelected ? "disabled" : '') . ">" . 
                                                        ($isSelected ? "Selected" : 'Select') . 
                                                    "</button>
                                                </form>
                                            </td>";
                                        echo "</tr>";
                                    }

                                    $isThereMember = true;
                                } else {
                                    echo "<tr><td class='no-result-label text-center' colspan='12'>No members found</td></tr>";
                                    $isThereMember = false;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
    
            </div>

            <div class="right right-section member-information-section <?php echo (!isset($memInfo)) ? 'hidden' : ''?>">
                <div class="member-header">
                    <div class="title">Member Information</div>
                    <div class="content">
                        <?php
                            if (empty($memInfo["profile"])) {
                                $imgSrc = './img/default-profile.png';
                            } else {
                                $imgSrc = getImageSrc($memInfo["profile"]);
                            }
                            echo "<img class='profile' src='$imgSrc'>";
                        ?>
                        <div class="right">
                            <p class="name data"><?php echo $memInfo['name']?></p>
                            <div class="other-info">
                                <p class="data" >ID: <span class="value"><?php echo $memInfo['mem_id']?></p>
                                <p>|</p>
                                <p class="data">Age: <span class="value"><?php echo $memInfo['age']?></p>
                                <p>|</p>
                                <p class="data">Sex: <span class="value"><?php echo $memInfo['sex']?></p>
                            </div>
                            <div class="other-info">
                                <p class="data"> <?php if ($memInfo['contact']!=""){echo $memInfo['contact'];}else{echo 'null';} ?></p>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="member-body">
                    <hr>
                    <div class="info <?php echo (($memInfo['is_temp_mem'] == '1') ? 'hidden' : '')?>">
                        <p class="label">Total Savings: </p>
                        <p class="data">
                            <span class="detail">₱<?php echo ((isset($memInfo['mem_id'])) ? getTotalDeposits($conn, $memInfo['mem_id']) : 0 ); ?></span>
                        </p>
                    </div>
                    <div class="info">
                        <p class="label">Loan Balance: </p>
                        <p class="data">
                        <span class="detail">₱<?php echo ((isset($memInfo['mem_id'])) ? getTotalLoanBalance($conn, $memInfo['mem_id']) . " <span class='c-gray small-text'>(+ ₱" . getTotalInterests($conn, $memInfo['mem_id']) . ' Interests)</span>' : 0);?></span>
                    </p>
                    </div>
                    <div class="info <?php echo (($memInfo['is_temp_mem'] == '1') ? 'hidden' : '')?>">
                        <p class="label">Interest Share: </p>
                        <p class="data">
                            <span class="detail">₱<?php echo number_format(getMemberInterestsShare($conn), 2); ?></span>
                        </p>
                    </div>
                    <div class="info mt-3 <?php echo (($memInfo['is_temp_mem'] == '0') ? 'hidden' : '')?>">
                        <p class="label c-red">Temporary Member for Purpose of Requesting Loan</p>
                    </div>
                </div>
                <div class="member-footer">
                    <div class="title">Other Info</div>
                    <hr>
                    <div class="content">
                        <p class="info"><span class="label">Username: </span><span class="value"><?php echo $memInfo['username']; ?></span></p>
                        <p class="info"><span class="label">Address: </span><span class="value"><?php if ($memInfo['address']!=""){echo $memInfo['address'];}else{echo 'null';} ?></span></p>
                        <p class="info"><span class="label">Added On: </span><span class="value"><?php echo $memInfo['date_added']; ?></span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="section second">
        <h3 class="title">2. Enter Amount</h3>
        <div class="content">
            <form action="./database/loan-payment.php" method="POST">
                <div class="info">
                    <label for="payment-amount">Amount: (₱)</label>
                    <input type="number" id="payment-amount" class="no-spinner" placeholder="<?php if(!$isThereMember || empty($memInfo['mem_id'])) {echo "Disabled";} else {echo "Enter here";}?>" name="payment-amount" <?php if(!$isThereMember || empty($memInfo['mem_id'])) echo "disabled"?>>
                    <input type="hidden" name="mem_id" value="<?php if (isset($memInfo['mem_id'])) {echo $memInfo['mem_id']; }?>">
                    <input type="hidden" name="loan_detail_id" value="<?php if (isset($memInfo['loan_detail_id'])) {echo $memInfo['loan_detail_id']; }?>">
                </div>
                <button class="submit" type="submit" name="submit" value="submit" <?php if(!$isThereMember || empty($memInfo['mem_id'])) echo "disabled"?>>Save</button>
            </form>
        </div>
    </div>
</div>


<?php
    $paidLoanQuery = "SELECT m.mem_id, m.is_temp_mem, ld.loan_detail_id, a.profile, lr.request_id, CONCAT(m.fname, ' ', m.lname) AS name, m.sex, TIMESTAMPDIFF(YEAR, m.birthdate, CURDATE()) AS age, ld.loan_amount, ((ld.interest_rate/100) * ld.loan_amount) AS interest, ld.month_duration, ld.is_paid
    FROM members m 
    INNER JOIN loan_requests lr
    ON lr.mem_id = m.mem_id
    INNER JOIN accounts a
    ON a.mem_id = m.mem_id
    INNER JOIN loan_details ld
    ON ld.loan_detail_id = lr.loan_detail_id
    WHERE lr.request_status = 'Approved' AND lr.is_claim = 1 AND ld.is_paid = 1";

    $paidLoans = $conn->query($paidLoanQuery);
    
?>
<h1 class="my-3 mt-8 <?php echo (($paidLoans->num_rows <= 0) ? "hidden" : "") ?>">Paid Loans</h1>
<hr class="<?php echo (($paidLoans->num_rows <= 0) ? "hidden" : "") ?>">    
<div class="paid-loan mb-5 <?php echo (($paidLoans->num_rows <= 0) ? "hidden" : "") ?>">
<h4 class="mb-3">Total: <span class="value"><?php echo $paidLoans->num_rows?></span></h4>
    <div class="result">
        <table class="result-table">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Member ID</th>
                    <th>Name</th>
                    <th>Member Type</th>
                    <th>Sex</th>
                    <th>Age</th> 
                    <th>Loan Detail ID</th>
                    <th>Total Loan Amount</th> 
                    <th>Month Duration</th> 
                    <th>Payment Status</th> 
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($paidLoans->num_rows > 0) {
                        while ($row = $paidLoans->fetch_assoc()) {
                            //get is_paid
                            if ($row['is_paid'] == 0) {
                                $payment_status = "Pending";
                            } else {
                                $payment_status = "Paid";
                            }

                            if ($row['is_temp_mem'] == 0 || $row['is_temp_mem'] == false) { 
                                $member_type = 'Member';
                            } else {
                                $member_type = 'Temporary';
                            } 

                            echo "<tr>";
                            if (empty($row["profile"])) {
                                echo "<td class='profile-img'><img src='./img/default-profile.png' alt='img'></td>";
                            } else {
                                $imgSrc = getImageSrc($row['profile']);
                                echo "<td class='profile-img'><img src='$imgSrc' alt='img'></td>";
                            }
                            echo "<td>" . $row['mem_id'] . "</td>";
                            echo "<td>" . $row['name']. "</td>";
                            echo "<td class='text-center'>" . $member_type. "</td>";
                            echo "<td>" . $row["sex"] . "</td>";
                            echo "<td>" . $row['age']. "</td>";
                            echo "<td>" . $row['loan_detail_id']. "</td>";
                            echo "<td>₱" . $row['loan_amount'] + $row['interest'] . "</td>";
                            echo "<td class='text-center'>" . $row['month_duration']. "</td>";
                            echo "<td class='text-center " . (($payment_status == "Paid") ? "c-green" : "") . "'>" . $payment_status. "</td>";
                            echo "</tr>";
                        }
                    } 
                ?>
            </tbody>
        </table>
    </div>
</div>
