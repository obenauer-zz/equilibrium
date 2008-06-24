<?php
// Copyright 2008, St. Jude Children's Research Hospital.
// Written by Dr. John Obenauer, john.obenauer@stjude.org.

// This file is part of Equilibrium.  Equilibrium is free software:
// you can redistribute it and/or modify it under the terms of the
// GNU General Public License as published by the Free Software
// Foundation, either version 2 of the License, or (at your option)
// any later version.

// Equilibrium is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with Equilibrium.  If not, see <http://www.gnu.org/licenses/>.

require("config.php");

// Check for passed arguments
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = "login";
}

if (isset($_REQUEST['cmd'])) {
    $cmd = $_REQUEST['cmd'];
} else {
    $cmd = "";
}

if (isset($_REQUEST['user'])) {
    $user = $_REQUEST['user'];
} else {
    $user = "";
}

if (isset($_REQUEST['pass'])) {
    $pass = $_REQUEST['pass'];
} else {
    $pass = "";
}

if (isset($_REQUEST['error'])) {
    $error = $_REQUEST['error'];
    $action = "error";
} else {
    $error = "";
}

// Declare PHP functions
//require("equilibrium.php");
function authenticate_local($user, $pass) {

    global $userid;
    global $login;
    global $fullname;
    global $authentication;
    global $staff;
    global $admin;

    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db(DB_DATABASE);
    $query = "SELECT user_id, login, first_name, last_name, " .
        "staff_flag, admin_priv, authentication " .
        "FROM users WHERE login = \"$user\" " .
        "AND password = PASSWORD(\"$pass\") ";
    $result = mysql_query ($query, $conn)
        or die ("Error in query: $query " . mysql_error() . "\n<br>");
    if (mysql_num_rows($result) == 1) {

        if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

            if ($row['authentication'] == "local") {

                // User has been authenticated; set privileges
                $userid = $row['user_id'];
                $login = $row['login'];
                if ($row['first_name']) {
                    $fullname = $row['first_name'] . " " . $row['last_name'];
                } else {
                    $fullname = $row['last_name'];
                }
                $authentication = $row['authentication'];
                $staff = $row['staff_flag'];
                $admin = $row['admin_priv'];
                mysql_free_result($result);
                mysql_close($conn);
                return 1;

            } else if ($row['authentication'] == 'LDAP') {

                // Local authentication not allowed for this user
                mysql_free_result($result);
                mysql_close($conn);
                return 0;
            }

        }

        // User failed authentication -- authentication type unknown
        mysql_free_result($result);
        mysql_close($conn);
        return 0;

    } else {

        // User failed authentication -- not found in local database
        mysql_free_result($result);
        mysql_close($conn);

        return 0;
    }

}

function authenticate_ldap($user, $pass) {

    // Do not allow blank passwords
    if ($pass == "") {
        return 0;
    }

    // Connect to LDAP
    $conn = ldap_connect(LDAP_SERVER_ADDRESS, LDAP_PORT);
    if (!$conn) {
        die("Error: Could not connect to LDAP server " . LDAP_SERVER . ".\n");
    }
    ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

    // Bind LDAP server
    $bind = ldap_bind($conn, LDAP_USER_NAME, LDAP_PASSWORD);
    if (!$bind) {
        die("Error: Could not BIND to LDAP server ->" . LDAP_SERVER . 
            " on port " . LDAP_PORT);
    }

    // Query LDAP for this user
    $authenticated = false;
    $query = 'samaccountname=' . $user;
    $fields = array("ou", "sn", "givenname", "mail", "memberof", "samaccountname", 
        "department", "description", "initials");
    $search = ldap_search($conn, LDAP_BASE_DN, $query, $fields);
    if ($search !== false) {
        $result = @ldap_get_entries($conn, $search);
        if (!$result) {
            $authenticated = false;
        } else {
            if ($result['count'] == 0) {
                // This username was not found; authentication failed
                $authenticated = false;
            } else {

                // Username found in LDAP; check that password matches
                if ($result[0])
                {

                    if (@ldap_bind($conn, $result[0]['dn'], $pass) )
                    {
                        // Password matches; authentication successful
                        $authenticated = true;

                    } else {

                        // Failed authentication
                        $authenticated = false;

                    }
                }
            }
        }
    }

    // Close LDAP connection
    ldap_close($conn);

    if ($authenticated) {

        // User has St. Jude credentials; check local database for access
        global $userid;
        global $login;
        global $fullname;
        global $authentication;
        global $staff;
        global $admin;
        
        // If user is already in local database, retrieve settings
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $query = "SELECT user_id, login, first_name, last_name, " .
            "staff_flag, admin_priv, authentication " .
            "FROM users WHERE login = \"$user\" ";
        $result = mysql_query ($query, $conn)
            or die ("Error in query: $query " . mysql_error() . "\n<br>");
        if (mysql_num_rows($result) == 1) {
    
            if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

                // Verify that LDAP authentication is allowed for this user
                if ($row['authentication'] == "LDAP") {

                    // User has been authenticated; set privileges
                    $userid = $row['user_id'];
                    $login = $row['login'];
                    if ($row['first_name']) {
                        $fullname = $row['first_name'] . " " . $row['last_name'];
                    } else {
                        $fullname = $row['last_name'];
                    }
                    $authentication = $row['authentication'];
                    $staff = $row['staff_flag'];
                    $admin = $row['admin_priv'];
                    mysql_free_result($result);
                    mysql_close($conn);
                    return 1;

                } else if ($row['authentication'] == 'local') {

                    // User failed authentication -- wrong auth type
                    mysql_free_result($result);
                    mysql_close($conn);
                    return 0;
                }
        
                // User failed authentication -- unknown auth type
                mysql_free_result($result);
                mysql_close($conn);
                return 0;

            } else {

                // User failed authentication -- not found in local database
                mysql_free_result($result);
                mysql_close($conn);
                return 0;

            }
    
    
        } else {
            
            // If user is not in local database, deny access
            mysql_free_result($result);
            mysql_close($conn);
            return 0;
        }
        
    } else {

        // User failed authentication
        return 0;
    }

}

// Commands that don't generate HTML output
switch($cmd) {

    case "authenticate":

        // Initialize variables
        $userid = 0;
        $login = "";
        $fullname = "";
        $staff = "N";
        $admin = "N";
        
        if ($use_ldap == "Y") {
        
            // Authenticate some users locally (like admin)
            $status = authenticate_local($user, $pass);
        
            // If local authentication fails, try LDAP
            if ($status == 0) {
                $status = authenticate_ldap($user, $pass);
            }
        
        } else {
        
            // Authenticate all users locally (like admin)
            $status = authenticate_local($user, $pass);
            
        }

        if ($status == 1) {
        
            // User authenticated, so start a session
            session_start();
            session_register("SESSION");
            session_register("SESSION_USERID");
            session_register("SESSION_LOGIN");
            session_register("SESSION_USER");
            session_register("SESSION_STAFF");
            session_register("SESSION_ADMIN");
            $_SESSION['SESSION_USERID'] = $userid;
            $_SESSION['SESSION_LOGIN'] = $login;
            $_SESSION['SESSION_USER'] = $fullname;
            $_SESSION['SESSION_STAFF'] = $staff;
            $_SESSION['SESSION_ADMIN'] = $admin;
        
            // Send user to protected page
            header("Location: $main_page");
            exit();
        
        } else {
        
            // User not authenticated
            header("Location: index.php?error=1");
            exit();
        }
        
        break;

    case "logout":

        // Destroy all session variables
        session_start();
        session_destroy();
        
        // Redirect browser to login page
        header("Location: index.php");

        break;

}

// Start HTML and declare Javascript functions
$activepage = "";
//require("header.php");

// Main functions of page
switch($action) {
    case "error":

        if ($error == 1) {
        
            printf("<html><head><title>Login Failure</title></head>\n");
            printf("<body bgcolor='%s'>\n", $background_color);
            printf("<h2>Login Failed</h2>\n");
            printf("<p>Either your username, password, or both are not recognized.</p>\n");
            printf("<a href='index.php'>Try again</a>\n");
            printf("</body></html>\n");
        
        } else if ($error == 2) {
        
            printf("<html><head><title>Authorization Required</title></head>\n");
            printf("<body bgcolor='%s'>\n", $background_color);
            printf("<h2>Authorization Required</h2>\n");
            printf("<p>You must <a href='index.php'>log in</a> to access this page.  \n");
            printf("Your session may have timed out.</p>\n");
            printf("</body></html>\n");
        
        } else {
        
            printf("<html><head><title>Authentication Error</title></head>\n");
            printf("<body bgcolor='%s'>\n", $background_color);
            printf("<h2>Authentication Error</h2>\n");
            printf("<p>The system was not able to authenticate your username ");
            printf("and password.  ");
            printf("If you were previously logged in, your session may have ");
            printf("timed out.</p>\n");
            printf("<a href='index.php'>Log in again</a>\n");
            printf("</body></html>\n");
        
        }

        break;
    case "":
        break;
    case "":
        break;
    case "":
        break;
    case "login":

        // Show login page
        printf("<html><head><title>%s Projects Database</title></head>\n", 
            $organization_name);
        printf("<body bgcolor='%s'>\n", $background_color);
        printf("<h2>%s Projects Database</h2>\n", $organization_name);
        printf("<table><tr><td>\n");
        printf("  <table cellspacing='5' cellpadding='5'>\n");
        printf("    <form action=\"index.php\" method='POST'>\n");
        printf("    <input type='hidden' name='cmd' value='authenticate'>\n");
        printf("      <tr><td>Username</td><td><input type='text' name='user' ");
        printf("id='username' size='20'></td></tr>\n");
        printf("      <tr><td>Password</td><td><input type='password' ");
        printf("name='pass' size='20'</td></tr>\n");
        printf("      <tr><td colspan='2' align='center'>");
        printf("<input type='submit' name='submit' value='Log In'></td></tr>\n");
        printf("    </form>\n");
        printf("  </table>\n");
        printf("</td>\n");

        // Optional: include an image on login page
        //printf("<td><img src=''></td>\n");

        printf("</tr></table>\n");

        // Set focus to username text box
        printf("<script type='text/javascript'>\n");
        printf("  document.getElementById('username').value = '';\n");
        printf("  document.getElementById('username').focus();\n");
        printf("</script>\n");
        printf("</body></html>\n");

        break;
}

// End page
//require("footer.php");
?>

