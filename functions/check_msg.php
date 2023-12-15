<?php
    if (isset($_SESSION['message']) && $_SESSION['messageBg']) {
        $message = $_SESSION['message'];
        $messageBg = $_SESSION['messageBg'];
        
        $messageClass = '';
        if ($messageBg == 'red') {
            $messageClass = 'bg-red';
        } elseif ($messageBg == 'green') {
            $messageClass = 'bg-green';
        } else {
            $messageClass = '';
        }
        $_SESSION['message'] = null;
        $_SESSION['messageBg'] = null;
    } else {
        $message = '';
        $messageClass = 'hidden';
    }
?>