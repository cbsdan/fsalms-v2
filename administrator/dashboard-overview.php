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

$function_path = '../functions/read_db.php';
$function_path_index = './functions/read_db.php';

if (file_exists($function_path)) {
    include_once($function_path);
} else if(file_exists($function_path_index)){
    include_once($function_path_index);
}

?>

<div id="greet-card">
    <h1>Welcome back, <?php echo $_SESSION['valid']; ?></h1>
    <p>Your analytics are ready</p>
</div>
<h1 class="content-title">Overview</h1>
<hr>

<div class="overview">
    <div class="overview-card">
        <div class="card-header">
            <p>Members</p>
        </div>
        <div class="card-body">
            <div class="information">
                <p class="label">Male: </p>
                <p class="data">
                    <span class="detail"><?php echo $lblMaleCount; ?> </span>
                </p>
            </div>
            <div class="information">
                <p class="label">Female:</p>
                <p class="data">
                    <span class="detail"><?php echo $lblFemaleCount; ?></span>
                </p>
            </div>
            <div class="information">
                <p class="label">Total:</p>
                <p class="data">
                    <span class="detail"><?php echo  countMembers($conn); ?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="overview-card">
        <div class="card-header">
            <p>Member Share</p>
        </div>
        <div class="card-body">
            <div class="information">
                <p class="label">Savings: <span class="gray-text small-text fw-600">(Week no. * Weekly payment)</span></p>
                <p class="data">
                    <span class="detail">P <?php echo $memberSavings = number_format(getMemberSavings($conn), 2); ?></span>
                </p>
            </div>
            <div class="information">
                <p class="label">Interest Share: <span class="gray-text small-text fw-600">(Members share / total members)</span></p>
                <p class="data">
                    <span class="detail">P <?php echo $interestShare = number_format(getMemberInterestsShare($conn), 2); ?> </span>
                </p>
            </div>
            <div class="information">
                <p class="label">Total: <span class="gray-text small-text fw-600"></span></p>
                <p class="data">
                    <span class="detail">P <?php echo number_format(getMemberSavings($conn) + getMemberInterestsShare($conn), 2); ?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="overview-card">
        <div class="card-header">
            <p>Savings</p>
        </div>
        <div class="card-body">
            <div class="information">
                <p class="label">Total Deposit: </p>
                <p class="data">
                    <span class="detail">P <?php echo number_format(getTotalSavings($conn), 2); ?></span>
                </p>
            </div>
            <div class="information">
                <p class="label">Available Money: <span class="gray-text small-text fw-600">(+ Interests gain)</span></p>
                <p class="data">
                    <span class="detail">P <?php echo getTotalAvailableMoney($conn); ?></span>
                    
                </p>
            </div>
            <div class="information">
                <p class="label">Currently on Loan:</p>
                <p class="data">
                    <span class="detail">P <?php echo number_format(getTotalUnpaidLoan($conn), 2); ?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="overview-card">
        <div class="card-header">
            <p>Week</p>
        </div>
        <div class="card-body">
            <div class="information">
                <p class="label">Week No.: </p>
                <p class="data">
                    <span class="detail"> <?php echo  getWeekNumber($conn); ?> </span><span class="smaller"> over  <?php echo getTotalWeeks($conn); ?></span><br>
                    
                </p>
            </div>
            <div class="information">
                <p class="label">Start Date:</p>
                <p class="data">
                    <span class="detail"><?php echo  getStartDate($conn); ?></span>
                </p>
            </div>
            <div class="information">
                <p class="label">End Date:</p>
                <p class="data">
                    <span class="detail"><?php echo  getEndDate($conn); ?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="overview-card">
        <div class="card-header">
            <p>Loan</p>
        </div>
        <div class="card-body">
            <div class="information">
                <p class="label">Unpaid Loan</p>
                <p class="data">
                    <span class="detail">P <?php echo  number_format(getTotalUnpaidLoan($conn), 2); ?></span>
                </p>
            </div>
            <div class="information">
                <p class="label">Total Loan: </p>
                <p class="data">
                    <span class="detail">P  <?php echo  number_format(getTotalLoan($conn), 2); ?> </span>
                </p>
            </div>
            <div class="information">
                <p class="label">Paid Loan: </p>
                <p class="data">
                    <span class="detail">P <?php echo  getTotalPaidLoan($conn); ?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="overview-card">
        <div class="card-header">
            <p>Interest Gain</p>
        </div>
        <div class="card-body">
            <div class="information">
                <p class="label">Total Interest: </p>
                <p class="data">
                    <span class="detail">P <?php echo  number_format($interests = getTotalInterest($conn), 2); ?></span>
                </p>
            </div>
            <div class="information">
                <p class="label">Paid:</p>
                <p class="data">
                    <span class="detail">P <?php echo  number_format(getTotalPaidInterests($conn), 2); ?></span>
                </p>
            </div>
            <div class="information">
                <p class="label">Pending:</p>
                <p class="data">
                    <span class="detail">P <?php echo  number_format(getTotalPendingInterests($conn), 2); ?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="overview-card">
        <div class="card-header">
            <p>Interest Share</p>
        </div>
        <div class="card-body">
            <div class="information">
                <p class="label">Collector / Manager:</p>
                <p class="data">
                    <span class="detail">P <?php echo number_format($interests * getManagerPercentage($conn) / 100, 2); ?> </span>
                    <span class="gray-text small-text">(<?php echo getManagerPercentage($conn);?>%)</span>
                </p>
            </div>
            <div class="information">
                <p class="label">All Members:</p>
                <p class="data">
                    <span class="detail">P <?php echo number_format($interests * getMemberPercentage($conn) / 100, 2); ?></span>
                    <span class="gray-text small-text">(<?php echo getMemberPercentage($conn);?>%)</span>
                </p>
            </div>
        </div>
    </div>
</div>