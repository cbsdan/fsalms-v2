<?php
    if (!(session_status() === PHP_SESSION_ACTIVE)) {
        session_start();
        $index_path1 = './fsalms/index.php';
        $index_path2 = './index.php';
        $index_path3 = '../index.php';
        $real_path;
        if (file_exists($index_path1)) {
            $real_path = $index_path1;
        } else if (file_exists($index_path2)){
            $real_path = $index_path2;
        } else if (file_exists($index_path3)) {
            $real_path = $index_path3;
        }

        if (!isset($_SESSION['valid']) || !isset($_SESSION['user-type'])) {
            echo "ERROR : <a href='$real_path'><button>LOGIN</button></a> FIRST";
            exit();
        } 
    } else {
        if (!isset($_SESSION['valid']) || !isset($_SESSION['user-type'])) {
            echo "ERROR : <a href='$real_path'><button>LOGIN</button></a> FIRST";
            exit();
        } 
    }

    $userType = $_SESSION['user-type'];
    $path_name = $_SERVER['REQUEST_URI'];
    $adminPages = [
        '/fsalms/administrator/administrator-add.php',
        '/fsalms/administrator/administrator-edit-member.php',
        '/fsalms/administrator/administrator-edit-transaction.php',
        '/fsalms/administrator/dashboard-overview.php',
        '/fsalms/administrator/left-side-navbar.php',
        '/fsalms/administrator/loan-pay.php',
        '/fsalms/administrator/loan-requests.php',
        '/fsalms/administrator/member-information.php',
        '/fsalms/administrator/member-transactions.php',
        '/fsalms/administrator/member-verification.php',
        '/fsalms/administrator/savings-deposits.php',
        '/fsalms/administrator/settings.php',
    ];
    $userPages = [
        '/fsalms/user/edit_section.php',
        '/fsalms/user/info_section.php',
        '/fsalms/user/request_section.php',
        '/fsalms/user/transactions_section.php',
        '/fsalms/user/user-info-nav.php',
    ];
    if ($userType === 'admin') {
        foreach($userPages as $userPage) {
            if ($userPage == $path_name) {
                $_SESSION['message'] = "Please Login as member first";
                $_SESSION['messageBg'] = "red";
                header('Location: ../administrator-ui.php');
                exit();
            }
        } 

    } else {
        foreach($adminPages as $adminPage) {
            if ($adminPage == $path_name) {
                $_SESSION['message'] = "Please Login as admin first";
                $_SESSION['messageBg'] = "red";
                header('Location: ../user-ui.php');
                exit();
            }
        } 
    }

?>