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
   $profileData = query("SELECT profile FROM accounts WHERE username = '" . $_SESSION['valid']. "'");
   $profileSrc = getImageSrc($profileData['profile']);
   if ($profileSrc == '') {
      $profileSrc = './img/default-profile.png'; //default profile
   }
?>
<div class="left-side-navbar active">
    <div class="navbar-container">
        <div class="admin-card" onclick="loadContent('./administrator/settings.php', this)" title="Administrator">
            <div class="profile"><img src="<?php echo $profileSrc; ?>" alt="admin" id="admin-profile"></div>
            <h2 class='nav-name'>ADMINISTRATOR</h2>
        </div>
        <div class="dashboard nav-container">
            <p class="text">DASHBOARD</p>
            <div class="nav" id="d-overview" onclick="loadContent('./administrator/dashboard-overview.php', this)" title="Overview">
                <div class="nav-logo"><img src="./img/overview-icon.png" class="logo"></div>
                <p class="nav-name">Overview</p>
            </div>
        </div>
        <div class="members nav-container">
            <p class="text">MEMBERS</p>
            <div class="nav" id="m-information" onclick="loadContent('./administrator/member-information.php', this)" title="Member Information">
                <div class="nav-logo"><img src="./img/member-information-icon.png" class="logo"></div>
                <p class="nav-name">Member Information</p>
            </div>
            <div class="nav active" id="m-transactions" onclick="loadContent('./administrator/member-transactions.php', this)" title="Member Transaction">
                <div class="nav-logo"><img src="./img/member-transactions.png" class="logo"></div>
                <p class="nav-name">Member Transactions</p>
            </div>
        </div>
        <div class="savings nav-container">
            <p class="text">SAVINGS</p>
            <div class="nav" id="s-deposits" onclick="loadContent('./administrator/savings-deposits.php', this)" title="Deposit">
                <div class="nav-logo"><img src="./img/savings-deposits.png" class="logo"></div>
                <p class="nav-name">Deposit</p>
            </div>
        </div>
        <div class="loan nav-container">
            <p class="text">LOAN</p>
            <div class="nav" id="l-requests" onclick="loadContent('./administrator/loan-requests.php', this)" title="Loan Requests">
                <div class="nav-logo"><img src="./img/loan-request.png" class="logo"></div>
                <p class="nav-name">Loan Requests</p>
                <div class='notification <?php echo ((countLoanRequests($conn) == 0) ? 'hidden' : '');?>'><?php echo countLoanRequests($conn);?></div>
            </div>
            <div class="nav" id="l-payment" onclick="loadContent('./administrator/loan-pay.php', this)" title="Loan Payment">
                <div class="nav-logo"><img src="./img/loan-payment.png" class="logo"></div>
                <p class="nav-name">Loan Payment</p>
            </div>
        </div>
        <div class="administrator nav-container">
            <p class="text">ADMINISTRATOR</p>
            <div class="nav" id="a-addMember" onclick="loadContent('./administrator/administrator-add.php', this)" title="Add Member">
                <div class="nav-logo"><img src="./img/administrator-add-member.png" class="logo"></div>
                <p class="nav-name">Add Member</p>
            </div>
            <div class="nav" id="a-editMember" onclick="loadContent('./administrator/administrator-edit-member.php', this)" title="Edit Member">
                <div class="nav-logo"><img src="./img/administrator-edit-member.png" class="logo"></div>
                <p class="nav-name">Edit Member</p>
            </div>
            <div class="nav" id="a-editTransaction" onclick="loadContent('./administrator/administrator-edit-transaction.php', this)" title="Edit Transaction">
                <div class="nav-logo"><img src="./img/edit-transaction.png" class="logo"></div>
                <p class="nav-name">Edit Transaction</p>
            </div>
        </div>
        <div class="settings nav" id="settings-nav" onclick="loadContent('./administrator/settings.php', this)" title="Settings">
            <div class="nav-logo"><img src="./img/setting-icon-light.svg" class="logo setting-logo"></div>
            <p class="nav-name">Settings</p>
        </div>
        <a class="logout nav" id="logout-nav" href="./database/logout.php" title="Logout">
            <div class="nav-logo"><img src="./img/logout.svg" class="logo setting-logo"></div>
            <p class="nav-name">Logout</p>
        </a>
    </div>
</div>