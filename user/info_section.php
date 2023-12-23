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
} else if (file_exists($database_path_index)){
    include_once($database_path_index);
}   

?>

<?php
$member_username = $_SESSION['valid'];

$sql = "SELECT mem_id FROM accounts WHERE username = '$member_username'";
$result = query($sql);
$memId = $result['mem_id'];

$sql = "SELECT is_temp_mem FROM members WHERE mem_id = $memId";
$result = $conn->query($sql);
$result = $result->fetch_assoc();
$is_temp_mem = $result['is_temp_mem'];

$total_deposits = getTotalDeposits($conn, $memId);
$week_number = getWeekNumber($conn);
$start_date = getStartDate($conn);
$end_date = getEndDate($conn);

$weekly_payment = getWeeklyPayment($conn) ;
$membership_fee = number_format(getMembershipFee($conn), 2);
$pending_amount= number_format(computePendingAmount($conn, $memId), 2);

$total_loan_balance = number_format(getTotalLoanBalance($conn, $memId), 2);
$interest_share = number_format(getMemberInterestsShare($conn), 2);

?>

<div class='background user-info <?php echo (($is_temp_mem == 0) ? 'hidden' : '')?>'>
    <h1 class="loan-title title">Loan Balance</h1>
    <hr>
    <div class="loan-container content">
        <div class="details">
            <p class='info'><span class='label'>Unpaid Loan: </span><span class="value">₱ <?php echo $total_loan_balance ; ?></span></p>
        </div>
    </div>
</div>

<div class="background user-info <?php echo (($is_temp_mem == 1) ? 'hidden' : '')?>">
    <h1 class="savings-title title">Savings</h1>
    <hr>
    <div class="savings-container content">
        <div class="details"> 
            <p class='info'><span class='label'>Total: </span><span class="value c-green">₱ <?php echo $total_deposits; ?><span class='small-text c-gray'>  (+ ₱ <?php echo $membership_fee;?> Membership Fee)</span></span></p>
            <p class='info'><span class='label'>Pending: </span><span class="value c-red">₱ <?php echo $pending_amount ; ?><span class='small-text c-gray'> (Kindly deposit any pending amount.)</span></span></p>
        </div>
    </div>

    <h1 class="loan-title title">Loan Balance</h1>
    <hr>
    <div class="loan-container content">
        <div class="details">
            <p class='info'><span class='label'>Unpaid Loan: </span><span class="value">₱ <?php echo $total_loan_balance ; ?></span></p>
        </div>
    </div>

    <!--
    <h1 class="interest-title title">Interest Share</h1>
    <hr>
    <div class="interest-container content">
        <div class="details">
            <p class='info'><span class='label'>Total: </span><span class="value">₱ <?php echo $interest_share ; ?></span></p>
            <p class='info t-italic c-gray mt-3'>(This is your share to interest gains from loans)</span></p>
        </div>
    </div>
    -->

    <h1 class="title">System Information</h1>
    <hr>
    <div class="savings-container content">
        <div class="details"> 
            <p class='info'><span class='label'>Week Number: </span><span class="value"><span class='fw-600'><?php echo $week_number ; ?></span><span   > over <?php echo getTotalWeeks($conn)?></span></span></p>
            <p class='info'><span class='label'>Weekly Payment: </span><span class="value">₱ <?php echo $weekly_payment ; ?></span></p>
            <p class='info'><span class='label'>Membership Fee: </span><span class="value">₱ <?php echo $membership_fee ; ?></span></p>
            <p class='info'><span class='label'>Start Date: </span><span class="value"><?php echo $start_date ; ?></span></p>
            <p class='info'><span class='label'>End Date: </span><span class="value"><?php echo $end_date ; ?></span></p>
        </div>
    </div>
</div>