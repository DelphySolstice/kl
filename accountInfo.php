<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>KeeperLeagues - <?php echo htmlentities($_SESSION['firstName']) . ' ' . htmlentities($_SESSION['lastName']); ?></title>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <?php if (login_check($mysqli) == true) : ?>
            <p>Welcome <?php echo htmlentities($_SESSION['firstName']) . ' ' . htmlentities($_SESSION['lastName']); ?>!</p>
            <p>
                Account Information - Add information, change password/email ...
            </p>
            <p>Return to <a href="home.php">homepage</a></p>
        <?php else : ?>
            <p>
                <span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a>.
            </p>
        <?php endif; ?>
    </body>
</html>