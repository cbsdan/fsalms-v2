<?php
$authentication_path = '../functions/user-authenticate.php';
$authentication_path_index = './functions/user-authenticate.php';
if (file_exists($authentication_path)) {
    include_once("$authentication_path");
} elseif (file_exists($authentication_path_index)) {
    include_once("$authentication_path_index");
}

//Used to include database
$database_path = '../database/config.php';
$database_path_index = './database/config.php';

if (file_exists($database_path)) {
    include($database_path);
} else if (file_exists($database_path_index)){
    include($database_path_index);
}

$member_username = $_SESSION['valid'];

$sql = "SELECT mem_id FROM accounts WHERE username = '$member_username'";
$result = query($sql);
$memId = $result['mem_id'];

$sql = "SELECT *, m.mem_id, CONCAT(m.fname, ' ', m.lname) AS name, m.fname, m.lname, m.sex, m.address, m.contact, TIMESTAMPDIFF(YEAR, m.birthdate, CURDATE()) AS age, m.birthdate, m.date_added, a.username, a.profile, a.password FROM members m 
        INNER JOIN accounts a ON m.mem_id = a.mem_id
        WHERE m.mem_id = $memId";
$memInfo = query($sql);

if (isset($memInfo['profile']) && $memInfo['profile'] != '') {
    $profileSrc = getImageSrc($memInfo['profile']);
} else {
    $profileSrc = './img/default-profile.png';
}
?>
<div class="user-info">
    <div class="background">
        <div class="profile-container">
            <img src="<?php echo $profileSrc; ?>" id="user-profile">
        </div>
        <div class="info-container">
            <div>
                <h3 id="user-name"><span class='label'><?php echo $memInfo['name']; ?></span><span id='temporary-label' class="bg-red <?php echo (($memInfo['is_temp_mem'] == 0) ? 'hidden': '');?>">Temporary</span></h3>
            </div>
            <div class="details">
                <p><span class="semibold-text">ID: </span><span class='value'><?php echo $memId?></span></p>
                <span class="semibold-text">|</span>
                <p><span class="semibold-text">Age: </span><span class='value'><?php echo $memInfo['age']; ?></span></p>
                <span class="semibold-text">|</span>
                <p><span class="semibold-text">Sex: </span><span class='value'><?php echo $memInfo['sex']; ?></span></p>
            </div>
            <div class="details">
                <p>
                    <span class="semibold-text">Address: </span><span class='value'><?php echo (empty($memInfo['address']) ? 'null' : $memInfo['address']);?></span>
                </p>
                <span class="semibold-text">|</span>
                <p>
                    <span class="semibold-text">Created on: </span><span id="creation-date" class='value'><?php echo $memInfo['date_added']; ?></span>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="navigation-container">
    <div class="background">
        <a class="nav active" id="info-nav" onclick="loadContent('./user/info_section.php', this)">INFO</a>
        <a class="nav" id="request-nav" onclick="loadContent('./user/request_section.php', this)">REQUEST A LOAN</a>
        <a class="nav" id="transaction-nav" onclick="loadContent('./user/transactions_section.php', this)">TRANSACTIONS</a>
        <a class="nav" id="edit-nav" onclick="loadContent('./user/edit_section.php', this)">EDIT ACCOUNT</a>
    </div>
</div>