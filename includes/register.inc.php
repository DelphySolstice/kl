<?php
include_once 'db_connect.php';
include_once 'psl-config.php';
 
$error_msg = "";
 
if (isset($_POST['email'], $_POST['p'], $_POST['lastName'], $_POST['firstName'])) {
    // Sanitize and validate the data passed in
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Not a valid email
        $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
 
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    $fn = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $ln = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    //if (strlen($password) != 128) {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        //$error_msg .= '<p class="error">Invalid password configuration.</p>';
    //}
 
    // Username validity and password validity have been checked client side.
    // This should should be adequate as nobody gains any advantage from
    // breaking these rules.
    //
 
    $prep_stmt = "SELECT ID FROM kl_users WHERE user_email = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
 
   // check existing email  
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
 
        if ($stmt->num_rows == 1) {
            // A user with this email address already exists
            $error_msg .= '<p class="error">A user with this email address already exists.</p>';
                        $stmt->close();
        }
                $stmt->close();
    } else {
        $error_msg .= '<p class="error">Database error Line 39</p>';
                $stmt->close();
    }
 
    // TODO: 
    // We'll also have to account for the situation where the user doesn't have
    // rights to do registration, by checking what type of user is attempting to
    // perform the operation.
 
    if (empty($error_msg)) {
        // Create a random salt
        //$random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE)); // Did not work
        //$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
 
        // Create salted password 
        //$password = hash('sha512', $password . $random_salt);
        $password = password_hash($password, PASSWORD_DEFAULT);
 
        // Insert the new user into the database 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO kl_users (user_email, user_pass, user_first, user_last) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $email, $password, $fn, $ln);
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                header('Location: ../error.php?err=Registration failure: INSERT');
            }
        }
        header('Location: ./register_success.php');
    }
}