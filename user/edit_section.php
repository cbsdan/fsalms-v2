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

$_SESSION['valid'];
$sql = "SELECT mem_id FROM accounts WHERE username = '" . $_SESSION['valid'] ."'";
$result = query($sql);
$mem_id = $result['mem_id'];

$sql = "SELECT *,  members.mem_id, members.fname, members.lname, CONCAT(members.fname, ' ', members.lname) AS name, members.sex, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) AS age, accounts.username, accounts.profile 
        FROM members
        LEFT JOIN accounts ON members.mem_id = accounts.mem_id 
        WHERE members.mem_id = $mem_id";
$memInfo = query($sql);

?>

<div class="background">
    <h1 class="title">Edit Account</h1>
    <hr>
    <div class="edit-container content">
        <form action="./database/user-edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="mem_id" value="<?php echo $mem_id?>">
            <div class="info">
                <label for="input-username">Username: <span class="required">*</span></label>
                <input type="text" id="input-username" name="username" placeholder="Enter username" value="<?php echo $memInfo['username']?>" required>
            </div>
            <div class="info">
                <label for="input-name">Name: <span class="required">*</span></label>
                <div class="input-name-container" id="input-name">
                    <input type="text" id="input-fname" name="fname" placeholder="First" value="<?php echo $memInfo['fname']?>" required>
                    <input type="text" id="input-lname" name="lname" placeholder="Last" value="<?php echo $memInfo['lname']?>" required>
                </div>
            </div>
            <div class="info">
                <label>Sex: <span class="required">*</span></label>
                <div class="sex-radio-container">
                    <label for="radio-male" class="sex-label"><input id="radio-male" type="radio" name="sex" value="Male" <?php if ($memInfo['sex'] === 'Male') echo 'checked'; ?> required>Male</label>
                    <label for="radio-female" class="sex-label"><input id="radio-female" type="radio" name="sex" value="Female" <?php if ($memInfo['sex'] === 'Female') echo 'checked'; ?> required> Female</label>
                </div>
            </div>
            <div class="info">
                <label for="input-birthdate">Birthdate: <span class="required">*</span></label>
                <input type="date" id="input-birthdate" name="birthdate" value="<?php echo $memInfo['birthdate']?>" required>
            </div>
            <div class="info">
                <label for="input-address">Address:</label>
                <input type="text" id="input-address" name="address"placeholder="Enter Address" value="<?php echo $memInfo['address']?>">
            </div>
            <div class="info">
                <label for="input-contact">Contact:</label>
                <input type="text" id="input-contact" name="cnumber"placeholder="Enter Contact" value="<?php echo $memInfo['contact']?>">
            </div>
            <div class="info">
                <label for="upload-img">Profile:</label>
                <input type="file" id="upload-img" accept=".jpg, .jpeg, .png" name="user-profile">
            </div>
            <button id="edit-btn" type="submit" name="submit" value="edit" >Apply</button>
        </form>
    </div>

    <h1 class="password-title title">Change Password</h1>
    <hr>
    <div class="password-container content">
        <form action="./database/user-edit.php" method="POST">
            <input type="hidden" name="mem_id" value="<?php echo $mem_id?>">    
            <div class="info">
                <label for="old_password">Old Password: <span class="required">*</span></label>
                <input id="old_password" type="password" name="old_password" placeholder="Enter Old Password" required>
            </div>
            <div class="info">
                <label for="new_password">New Password: <span class="required">*</span></label>
                <input id="new_password" type="password" name="new_password" placeholder="Enter New Password" required>
            </div>
            <div class="info">
                <label for="confirm_password">Confirm Password: <span class="required">*</span></label>
                <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button id="pw-change-btn" type="submit" name="submit" value="change-pw">Change Password</button>
        </form>
    </div>
</div>