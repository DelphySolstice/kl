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
        <!--<link rel="stylesheet" href="styles/main.css" />-->
    </head>
    <body>
        <?php if (login_check($mysqli) == true) { ?>
            <p>Welcome <?php echo htmlentities($_SESSION['firstName']) . ' ' . htmlentities($_SESSION['lastName']); ?>!</p>
             <?php  if(isset($_GET['action'])){
                        $action = $_GET['action'];
                        if($action == 1){
                            //Create League Action
                            echo "Create"; 
                            $l_types = get_league_types($mysqli);
                            //echo "<pre>";
                            //print_r($l_types);
                            //echo "</pre>";
             ?>
                        <div class="create-league">
                            <form action="" method="post" name="create_league_form">
                                <label for="leagueName">Name: </label><input type="text" placeholder="League Name" name="leagueName" id="leagueName"><br />
                                <label for="leagueType">Type: </label>
                                <select id="leagueType">
                                    <?php 
                                        foreach ($l_types as $key => $value) {
                                        echo "<option value='" . $key . "'>" . $value . "</option>";
                                        }
                                    ?>
                                </select><br />
                                <label for="salaryCap">Salary Cap: </label><input type="text" placeholder="" name="" id=""><br />
                                <label for="seasonCap">Season Cap: </label><input type="text" placeholder="" name="" id=""><br />
                                <label for="rounds">Rounds: </label><input type="text" placeholder="" name="" id=""><br />
                                <label for="farmCap">Farm Cap: </label><input type="text" placeholder="" name="" id=""><br />
                                <label for="protected">Protected Players: </label><input type="text" placeholder="" name="" id=""><br />
                            </form>
                        </div>
             <?php
                        }
                        elseif($action == 2){
                            //Join League Action
                            echo "Join";
                        } else {
                            //Not a Valid Action
                            echo "Not a valid action.";
                        }
                    }
             ?>
            
            <p></p>
            <p>Return to <a href="home.php">home page</a></p>
        <?php } else { ?>
            <p>
                <span class="error">You are not authorized to access this page.</span> Please <a href="index.php">login</a>.
            </p>
        <?php } ?>
    </body>
</html>