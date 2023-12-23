<?php
    include_once("./database/config.php");
    if (isset($_SESSION['valid']) && isset($_SESSION['user-type'])) {
        $user_type = $_SESSION['user-type'];
        
        if ($user_type == 'admin') {
            header('Location: ./administrator-ui.php');
            exit();
        } elseif ($user_type == 'member') {
            header('Location: ./user-ui.php');
            exit();
        }
    } 
    

    if(isset($_POST['login'])){
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
            
        $result = mysqli_query($conn,"SELECT * FROM accounts WHERE username='$username' AND password='$password'") or die("Select Error");
        $row = mysqli_fetch_assoc($result);

        if(is_array($row) && !empty($row)){		
            $_SESSION['valid'] = $row['username'];
            $_SESSION['id'] = $row['id'];
            $message = var_dump($_SESSION);
            echo"<script>alert($message)</script>";
        }else{
            $_SESSION['message'] = "No account found!";
            $_SESSION['messageBg'] = 'red';
        } 
        if(isset($_SESSION['valid'])){
            $result = mysqli_query($conn,"SELECT * FROM accounts WHERE username='$username'");
            $row = mysqli_fetch_assoc($result);
            if (isset($row['isAdmin'])) {
                if ($row['isAdmin']) {
                    $_SESSION['user-type'] = 'admin';
                    header("Location: administrator-ui.php");
                    exit();
                } else {
                    $_SESSION['user-type'] = 'member';
                    header("Location: user-ui.php");
                    exit();
                }
            }
        }
    } 
    
    include_once('./functions/check_msg.php');
?>
<div class="background">
    <div class="login-form">
        <p class="query-message <?php echo $messageClass;?>">
            <?php echo $message; ?>
        </p>
        <h1 class="title">Login</h1>
        <hr>
        <form action="" method="POST">
            <div class="info">
                <label for="username">Username:</label>
                <input type="text" id="username" placeholder="Enter Username" name="username" >
            </div>
            <div class="info">
                <label for="password">Password:</label>
                <input type="password" id="password" placeholder="Enter Password" name="password" >
            </div>
            <button class="submit" type="submit" name="login" value="login">Login</button>
        </form>
    </div>

</div>
<script>
    let logStatus = document.getElementById('log-status');
    logStatus.classList.add('hidden');
    let bodyEl = document.querySelector('body');
    
    bodyEl.style.background= "rgba(100,149,237, 0.7)";
    bodyEl.style.backgroundSize = "contain";
</script>