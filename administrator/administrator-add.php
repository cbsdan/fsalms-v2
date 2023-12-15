<?php
    $authentication_path = '../functions/user-authenticate.php';
    $authentication_path_index = './functions/user-authenticate.php';
    if (file_exists($authentication_path)) {
        include_once("$authentication_path");
    } elseif (file_exists($authentication_path_index)) {
        include_once("$authentication_path_index");
    }
?>
<h1>Add Member</h1>
<hr>

<div class="add-member p-1rem">
    <form action="./database/add-member.php" method="POST" enctype="multipart/form-data">
        <div class="info">
            <label for="input-fname">Name: <span class="required">*</span></label>
            <div class="input-name-container">
                <input type="text" id="input-fname" name="fname" placeholder="First" required>
                <input type="text" id="input-lname" name="lname" placeholder="Last" required>
            </div>
        </div>
        <div class="info">
            <label for="radio-sex">Sex: <span class="required">*</span></label>
            <div class="sex-radio-container">
                <label for="radio-male" class="sex-label"><input id="radio-male" type="radio" name="sex" value="Male" checked required> Male</label>
                <label for="radio-female" class="sex-label"><input id="radio-female" type="radio" name="sex" value="Female" required> Female</label>
            </div>
        </div>
        <div class="info">
            <label for="input-birthdate">Birthdate: <span class="required">*</span></label>
            <input type="date" id="input-birthdate" name="birthdate" value=<?php echo date('Y-m-d') ?> required>
        </div>
        <div class="info">
            <label for="input-address">Address:</label>
            <input type="text" id="input-address" placeholder="Enter Address" name="address">
        </div>
        <div class="info">
            <label for="input-contact">Contact:</label>
            <input type="text" id="input-contact" placeholder="Enter Contact" name="contact">
        </div>
        <div class="info">
            <label for="input-username">Username: <span class="required">*</span></label>
            <input type="text" id="input-username" name="username" placeholder="Enter username" required>
        </div>
        <div class="info">
            <label for="input-password">Password: <span class="required">*</span></label>
            <input type="password" id="input-password" name="password" placeholder="Enter password" value='1234' required>
        </div>
        <div class="info">
            <label for="upload-img">Profile:</label>
            <input type="file" accept=".jpg, .jpeg, .png" name="user-profile">
        </div>
        <input type="hidden" name="date_added" value="<?php echo date('Y-m-d'); ?>">
        <button id="add-btn" type="submit" name="add-btn" value="submit" >Add</button>
    </form>
</div>

<script>
    const fnameEl = document.querySelector('#input-fname')
    const lnameEl = document.querySelector('#input-lname')
    const usernameEl = document.querySelector('#input-username');

    fnameEl.addEventListener('input', ()=>{
        usernameEl.value = (fnameEl.value.charAt(0) + lnameEl.value.replace(/\s/g, '')).trim().toLowerCase()
    })
    lnameEl.addEventListener('input', ()=>{
        usernameEl.value = (fnameEl.value.charAt(0) + lnameEl.value.replace(/\s/g, '')).trim().toLowerCase()
    })
</script>