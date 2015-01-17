<?php
include_once 'psl-config.php';
 
function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id(true);    // regenerated the session, delete the old one. 
}

function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT ID, user_email, user_pass, user_first, user_last
        FROM kl_users
        WHERE user_email = ?
        AND user_status = 1
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $user_email, $db_password, $fn, $ln);
        $stmt->fetch();
 
        // hash the password with the unique salt.
        //$password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            //if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
            //    return false;
            //} else {
                // Check if the password in the database matches
                // the password the user submitted.
                if (password_verify($password, $db_password)) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $user_email = preg_replace("/[^a-zA-Z0-9_\-@.]+/", 
                                                                "", 
                                                                $user_email);
                    //$fn = preg_replace("[^A-Za-z]", "", $fn);
                    //$ln = preg_replace("[^A-Za-z]", "", $ln);
                    $_SESSION['useremail'] = $user_email;
                    $_SESSION['login_string'] = $db_password . $user_browser;
                    $_SESSION['firstName'] = $fn;
                    $_SESSION['lastName'] = $ln;
                    // Login successful.
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    //$now = time();
                    //$mysqli->query("INSERT INTO login_attempts(user_id, time)
                    //                VALUES ('$user_id', '$now')");
                    return false;
                }
            
        } else {
            // No user exists.
            return false;
        }
    }
}

function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], 
              $_SESSION['useremail'], 
              $_SESSION['login_string'],
              $_SESSION['firstName'],
              $_SESSION['lastName'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $useremail = $_SESSION['useremail'];
        $firstName = $_SESSION['firstName'];
        $lastName = $_SESSION['lastName'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT user_pass 
                                      FROM kl_users
                                      WHERE ID = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = $password . $user_browser;
 
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

function get_leagues($id, $mysqli){
    if ($stmt = $mysqli->prepare("SELECT l.ID, l.league_name 
        FROM kl_user_league ul, kl_leagues l
        WHERE user_id = ?
        AND ul.league_id = l.ID")) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            //User is part of no leagues
            return false;
        } else {
            //User is part of one or more leagues
            $stmt->bind_result($league_ids, $league_names);            
            $l_array = $stmt->fetch_assoc();
            $_SESSION['leagues_arr'] = $l_array;
            return true;
        }        
    }   
}

function get_league_types($mysqli){
    if ($result = $mysqli->query("SELECT league_type_id, league_type_name 
        FROM kl_league_type")) {
        if ($result->num_rows == 0) {
            //No Types
            return false;
        } else {
            //Send back types 
            while($row = $result->fetch_assoc()){          
                $arr[$row['league_type_id']] = $row['league_type_name'];
            }
            return $arr;
        } 
    }
    return false;
}

function create_league(){
    echo "CR FUNC";
}