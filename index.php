<?php
    include_once('./includes/header.php')
?>
<?php
    require_once('./database/config.php');
?>
<main class="default">
    <?php
        try {
            $sql = "SELECT * FROM accounts WHERE isAdmin = 1";
            $result = $conn->query($sql);
            if ($result -> num_rows > 0) {
                include_once('./login/login.php');
            } else {
                $_SESSION['message'] = 'There is no admin account registered! Register a new admin account to use the system';
                $_SESSION['messageBg'] = 'red';
                include_once('./login/register.php');
            }
        } catch(Exception $e) {
            echo "<script>console.log($e)</script>";
        }
    ?>
</main>

<?php
    include_once('./includes/footer.php')
?>
