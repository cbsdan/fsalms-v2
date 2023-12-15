<?php 
    session_start();
    $isLogin = false;

    if (isset($_SESSION['valid'])) {
        $isLogin = true;
    } else {
        $isLogin = false;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FSALMS</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <link rel="stylesheet" href="./styles/user-section.css">
    <link rel="stylesheet" href="./styles/administrator.css">
    <link rel="stylesheet" href="./styles/admin-sections.css">
    <link rel="stylesheet" href="./styles/other.css">
    <link rel="stylesheet" href="./styles/login.css">
    <link rel="stylesheet" href="./styles/variables.css">
    <link rel="icon" href="./img/favicon.ico" type="image/x-icon">

    <script src="./scripts/script.js" defer></script>
    <script type="module" src="./scripts/functions.js" defer></script>
</head>
<body>
    <header class="user">
        <div class="content">
            <a class="logo-container" href="./index.php">
                <div class="fsalms-container"><img src="./img/logo.png" id="fsalms-logo"></div>
                <div class="title-container"><h1 id="fsalms-text">FSALMS</h1></div>
            </a>
            <div class="log-container">
                <button id="log-status">
                    <a href=" <?php if($isLogin){echo './database/logout.php';} else {echo './database/logout.php';}?>">
                        <?php 
                            if($isLogin) {
                                echo 'Logout';
                            } else {
                                echo 'Login';
                            }
                        ?>
                    </a>
                </button>
            </div>
        </div>
    </header>
