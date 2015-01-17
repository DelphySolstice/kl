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
        <?php if (login_check($mysqli) == true) :        
                if(get_leagues($_SESSION['user_id'], $mysqli)){
                    echo $_SESSION['leagues_arr'];
                } else {
                    echo "You are not part of any leagues at the moment.<br />";
                    echo "<a href='leagueActivity.php?action=1'>Create a League</a> - 
                          <a href='leagueActivity.php?action=2'>Join a League</a>";
                }

        ?>
            <p>Welcome <?php echo htmlentities($_SESSION['firstName']) . ' ' . htmlentities($_SESSION['lastName']); ?>!</p>
            <p><a href="accountInfo.php">Account Information</a></p>
            <p><a href="includes/logout.php">Logout</a></p>

        <?php else : ?>
            <p>
                <span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a>.
                REDIRECT TO INDEX
            </p>
        <?php endif; ?>
    </body>
</html>