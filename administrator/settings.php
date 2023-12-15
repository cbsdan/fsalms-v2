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

$system_info = query("SELECT * FROM system_info");
?>

<h1>Settings</h1>
<hr>
<div class="settings">
    <form action="./database/modify-admin.php" method="POST" enctype="multipart/form-data">
        <div class="info">
            <label for="admin-username">Admin Username:</label>
            <input type="text" id="admin-username" name="admin-username" placeholder="Enter username" value="<?php echo $_SESSION['valid']; ?>" required>
        </div>
        <div class="info">
            <label for="weekly-payment">Weekly Payment: (₱)<span class="required"></span></label>
            <input type="number" id="weekly-payment" class="no-spinner" name="weekly-payment" placeholder="Enter weekly payment" value="<?php echo $system_info['weekly_payment']; ?>" readonly>
        </div>
        <div class="info">
            <label for="membership-fee">Membership Fee: (₱)<span class="required"></span></label>
            <input type="number" id="membership-fee" class="no-spinner" name="membership-fee" placeholder="Enter membership fee" value="<?php echo $system_info['membership_fee']; ?>" readonly>
        </div>
        <div class="info">
            <label for="starting-date">Starting Date:</label>
            <input type="date" id="starting-date" name="starting-date" value="<?php echo $system_info['start_date']; ?>" required>
        </div>
        <div class="info">
            <label for="ending-date">Ending Date:</label>
            <input type="date" id="ending-date" name="ending-date" value="<?php echo $system_info['end_date']; ?>" required>
        </div>
        <div class="info">
            <label for="manager-percentage">Manager Percent <span class='small-text'>(%)</span>: <span class="required"></span></label>
            <input type="number" id="manager-percentage" name="manager-percentage" value='<?php echo $system_info['manager_percentage']?>' required max=100 min=0>
        </div>
        <div class="info">
            <label for="members-percentage">Members Percent <span class='small-text'>(%)</span>: <span class="required"></span></label>
            <input type="number" id="members-percentage" name="members-percentage" value='<?php echo $system_info['member_percentage']?>' required max=100 min=0>
        </div>
        <div class="info">
            <label for="upload-img">Profile:</label>
            <input type="file" accept=".jpg, .jpeg, .png" name="admin-profile">
        </div>
        <button id="edit-btn" type="submit" name="changeSettings" value="editAdmin" >Apply</button>
    </form>
</div>

<h1 class="reset-title">Reset</h1>
<hr>
<div class="reset-container">
    <p class="description">This will reset the database, you will have to create a new admin first if you want to use the system again.</p>
    <form action="./database/modify-admin.php" method="POST" id="resetForm">
        <button id="reset-system" class="bg-red" type="submit" name="changeSettings" value="resetDb">Reset</button>
    </form>
</div>
<h1 class="password-title">Change Password</h1>
<hr>
<div class="password-container">
    <form action="./database/modify-admin.php" method="POST">
        <div class="info">
            <label for="old_password">Old Password: <span class="required">*</span></label>
            <input id="old_password" type="password" name="old_password" placeholder="Enter Old Password">
        </div>
        <div class="info">
            <label for="new_password">New Password: <span class="required">*</span></label>
            <input id="new_password" type="password" name="new_password" placeholder="Enter New Password">
        </div>
        <div class="info">
            <label for="confirm_password">Confirm Password: <span class="required">*</span></label>
            <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm Password">
        </div>
        <button id="pw-change-btn" type="submit" name="changeSettings" value="changePw">Change Password</button>
    </form>
</div>

<script>
    const resetForm = document.getElementById('resetForm');

    resetForm.addEventListener('submit', ()=>{
        let confirmDeletion = confirm(`Do you want to RESET the system which include the DATABASE?`);

        if (!confirmDeletion) {
            event.preventDefault();
        }
    })

    managerPercentageEl = document.querySelector('input#manager-percentage');
    membersPecentageEl = document.querySelector('input#members-percentage')

    managerPercentageEl.addEventListener('input', ()=>{
        const value = managerPercentageEl.value;
        if (value > 100 || value < 0) {
            membersPecentageEl.value = 0;
        } else {
            membersPecentageEl.value = 100 - value;
        }
    })    
    membersPercentageEl.addEventListener('input', ()=>{
        const value = membersPercentageEl.value;
        if (value > 100 || value < 0) {
            managerPecentageEl.value = 0;
        } else {
            managerPecentageEl.value = 100 - value;
        }
    }) 
</script>