<?php
// Check that authentication has been passed already
session_start();
if (!session_is_registered("SESSION")) {

    // No session established -- exit
    header("Location: index.php?error=2");
    exit();
}

// Register session variables
session_register("SESSION_USERID");
session_register("SESSION_USER");
session_register("SESSION_LOGIN");
session_register("SESSION_STAFF");
session_register("SESSION_ADMIN");

?>
