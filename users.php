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


require("check_login.php");
require("config.php");

// Check for passed arguments
if (isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
} else {
    $cmd = "";
}

if (isset($_POST['first_name'])) {
    $first_name = $_POST['first_name'];
}

if (isset($_POST['last_name'])) {
    $last_name = $_POST['last_name'];
}

if (isset($_POST['newlogin'])) {
    $newlogin = $_POST['newlogin'];
}

if (isset($_POST['email'])) {
    $email = $_POST['email'];
}

if (isset($_POST['admin_priv'])) {
    $admin_priv = $_POST['admin_priv'];
}

if (isset($_POST['staff_flag'])) {
    $staff_flag = $_POST['staff_flag'];
}

if (isset($_POST['userid'])) {
    $userid = $_POST['userid'];
}

if (isset($_POST['authtype'])) {
    $authtype = $_POST['authtype'];
}

if (isset($_POST['password1'])) {
    $password1 = $_POST['password1'];
}

if (isset($_POST['password2'])) {
    $password2 = $_POST['password2'];
}

$activepage = "Users";
require("header.php");
?>
<h2>Registered Users</h2>
<?
$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
    or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
mysql_select_db(DB_DATABASE);

if ($cmd == "add") {
    
    if ($_SESSION['SESSION_ADMIN'] != "Y") {
        printf("Your user account is not authorized to add new users.<br><br>\n");
    } else {
?>
        <h3>Add Information for New User</h3>
        <form action="users.php" method="post" name="adduser">
        <input type='hidden' name='cmd' value='insert'>
        <table cellpadding=0><tr><td>
        <tr><td colspan='2'><b>User Information: &nbsp; </b></td></tr>
        <tr><td align='right'><b>Log-in Name &nbsp; </b></td><td><input type='text' name='newlogin' value='' size=15></td></tr>
        <tr><td align='right'><b>First Name &nbsp; </b></td><td><input type='text' name='first_name' value='' size=15></td></tr>
        <tr><td align='right'><b>Last Name &nbsp; </b></td><td><input type='text' name='last_name' value='' size=15></td></tr>
        <tr><td align='right'><b>Email Address &nbsp; </b></td><td><input type='text' name='email' value='' size=30></td></tr>
        <tr><td colspan='2'><b>Permissions: &nbsp; </b></td></tr>
        <tr><td colspan='2'> &nbsp; <input type='checkbox' name='staff_flag'>Staff member</td></tr>
        <tr><td colspan='2'> &nbsp; <input type='checkbox' name='admin_priv'>Administrative privileges</td></tr>

        <tr><td colspan='2'><b>Authentication: &nbsp; </b></td></tr>
        <tr><td colspan='2'> &nbsp; 
        <input type='radio' name='authtype' value='LDAP' checked onclick='document.adduser.password1.disabled = true; document.adduser.password2.disabled = true;'>LDAP <br> &nbsp;
        <input type='radio' name='authtype' value='local' onclick='document.adduser.password1.disabled = false; document.adduser.password2.disabled = false;'>Local database 
        </td></tr>
        <tr><td align='right'><b>Password &nbsp; </b></td><td><input type='password' name='password1' value='' size=15 disabled='true'></td></tr>
        <tr><td align='right'><b>Confirm Password &nbsp; </b></td><td><input type='password' name='password2' value='' size=15 disabled='true'></td></tr>
        <tr><td colspan='2'><input type='submit' name='submit' value='Submit User'></td></tr>
        </form>
        </td></tr></table><br><br>
<?
    }
    
} else if ($cmd == "insert") {

    if ($_SESSION['SESSION_ADMIN'] == "Y") {
    
        // Convert checked variables to their correct values
        if (isset($admin_priv)) {
            $admin_priv = "Y";
            $admin_checked = "checked";
        } else {
            $admin_priv = "N";
            $admin_checked = "";
        }
        
        if (isset($staff_flag)) {
            $staff_flag = "Y";
            $staff_checked = "checked";
        } else {
            $staff_flag = "N";
            $staff_checked = "";
        }
        
        if ($authtype == 'LDAP') {
            $ldap_checked = "checked";
            $local_checked = "";
            $disabled_status = "true";
        } else if ($authtype == "local") {
            $ldap_checked = "";
            $local_checked = "checked";
            $disabled_status = "false";
        } else {
            $ldap_checked = "checked";
            $local_checked = "";
            $disabled_status = "true";
        }
        
        // Validate form data
        $repeat_entry = 0;
        if (!$newlogin) {
            printf("Error: Login Name was not specified and is required.<br>\n");
            $repeat_entry = 1;
        } else if (!$last_name) {
            printf("Error: Last Name was not specified and is required.<br>\n");
            $repeat_entry = 1;
        } else if (!$email) {
            printf("Error: Email Address was not specified and is required.<br>\n");
            $repeat_entry = 1;
        } else if (($authtype == 'local') && ($password1 != $password2)) {
            printf("Error: Typed passwords did not match.<br>\n");
            $repeat_entry = 1;
        }
        
        if ($repeat_entry) {
    ?>        
            <h3>Add Information for New User</h3>
            <form action="users.php" method="post" name="adduser">
            <input type='hidden' name='cmd' value='insert'>
            <table cellpadding=0><tr><td>
            <tr><td colspan='2'><b>User Information: &nbsp; </b></td></tr>
            <tr><td align='right'><b>Log-in Name &nbsp; </b></td><td><input type='text' name='newlogin' value='<?= $newlogin ?>' size=15></td></tr>
            <tr><td align='right'><b>First Name &nbsp; </b></td><td><input type='text' name='first_name' value='<?= $first_name ?>' size=15></td></tr>
            <tr><td align='right'><b>Last Name &nbsp; </b></td><td><input type='text' name='last_name' value='<?= $last_name ?>' size=15></td></tr>
            <tr><td align='right'><b>Email Address &nbsp; </b></td><td><input type='text' name='email' value='<?= $email ?>' size=30></td></tr>
            <tr><td colspan='2'><b>Permissions: &nbsp; </b></td></tr>
            <tr><td colspan='2'> &nbsp; <input type='checkbox' name='staff_flag' <?= $staff_checked ?>>Staff member</td></tr>
            <tr><td colspan='2'> &nbsp; <input type='checkbox' name='admin_priv' <?= $admin_checked ?>>Administrative privileges</td></tr>
    
            <tr><td colspan='2'><b>Authentication: &nbsp; </b></td></tr>
            <tr><td colspan='2'> &nbsp; 
            <input type='radio' name='authtype' value='LDAP' <?= $ldap_checked ?> onclick='document.adduser.password1.disabled = true; document.adduser.password2.disabled = <?= $disabled_status ?>;'>LDAP <br> &nbsp;
            <input type='radio' name='authtype' value='local' <?= $local_checked ?> onclick='document.adduser.password1.disabled = false; document.adduser.password2.disabled = <?= $disabled_status ?>;'>Local database 
            </td></tr>
            <tr><td align='right'><b>Password &nbsp; </b></td><td><input type='password' name='password1' value='' size=15 disabled='true'></td></tr>
            <tr><td align='right'><b>Confirm Password &nbsp; </b></td><td><input type='password' name='password2' value='' size=15 disabled='true'></td></tr>
            <tr><td colspan='2'><input type='submit' name='submit' value='Submit User'></td></tr>
            </form>
            </td></tr></table><br><br>

    <?        
            
        } else {
        
            if ($authtype == "local") {

                $add_user = mysql_query("insert into users " .
                    "(first_name, last_name, login, email, staff_flag, admin_priv, authentication, password) " .
                    "values (\"$first_name\", \"$last_name\", \"$newlogin\", \"$email\", " .
                    "\"$staff_flag\", \"$admin_priv\", \"local\", password(\"$password1\") ) ")
                    or die(mysql_error());

            } else {

                $add_user = mysql_query("insert into users " .
                    "(first_name, last_name, login, email, staff_flag, admin_priv, authentication) " .
                    "values (\"$first_name\", \"$last_name\", \"$newlogin\", \"$email\", " .
                    "\"$staff_flag\", \"$admin_priv\", \"LDAP\" ) ")
                    or die(mysql_error());
        
            }

            printf("<form action='users.php' method='post'>\n");
            printf("<input type='hidden' name='cmd' value='add'><input type='submit' value='Add New User'></form>\n");
        
        }
    } else {
        printf("To add a new user or edit user information, contact <a href='mailto:$admin_email'>$admin_name</a>.<br><br>\n");
    
    }
        
} else if ($cmd == "view") {

    $get_user = mysql_query("select user_id, first_name, last_name, login, " .
        "email, staff_flag, admin_priv " .
        "from users where user_id = \"$userid\" ");
    if ($row = mysql_fetch_array($get_user)) {
        printf("<table frame=border rules=all border=1 bgcolor=white cellpadding=4 bgcolor='$color2'>\n");
        printf("<tr bgcolor='$color2'><th colspan=2 bgcolor='$heading_color'>View for user <b>$row[1] $row[2]</b></th></tr>\n");
        printf("<tr><td colspan=2>Username: <b>$row[3]</b></td></tr>\n");
        printf("<tr><td colspan=2>Email address: <b>$row[4]</b></td></tr>\n");
        printf("<tr><td valign=top>Permissions:</td><td>\n");
        printf("Staff member: <b>$row[5]</b><br>\n");
        printf("Administrative privileges: <b>$row[6]</b><br>\n");
        printf("</td></table><br><br>\n");
    } else {
        print "Error retrieving user data from database.<br><br>\n";
    }
    mysql_free_result ($get_user);
    
    if ($_SESSION['SESSION_ADMIN'] = "Y") {
    
            printf("<form action='users.php' method='post'>\n");
            printf("<input type='hidden' name='cmd' value='add'><input type='submit' value='Add New User'></form>\n");
        
    } else {
    
            printf("<form action='users.php' method='post'>\n");
            printf("<input type='hidden' name='cmd' value='add'><input type='submit' value='Add New User'></form>\n");
        
    }
    
} else if ($cmd == "edit") {

    $get_user = mysql_query("select user_id, first_name, last_name, login, " .
        "email, staff_flag, admin_priv " .
        "from users where user_id = \"$userid\" ");
    if ($row = mysql_fetch_array($get_user)) {
        $userid = $row[0];
        $first_name = $row[1];
        $last_name = $row[2];
        $newlogin = $row[3];
        $email = $row[4];
        $staff_flag = $row[5];
        $admin_priv = $row[6];
        
        if ($admin_priv == "Y") {
            $admin_checked = "checked";
        } else {
            $admin_checked = "";
        }
        
        if ($staff_flag == "Y") {
            $staff_checked = "checked";
        } else {
            $staff_checked = "";
        }
        
?>        
        <h3>Edit Information for user <?=$first_name?> <?=$last_name?></h3>
        <form action="users.php" method="post">
        <input type='hidden' name='cmd' value='update'>
        <input type='hidden' name='userid' value='<?=$userid?>'>
        <table cellpadding=0><tr><td>
        <tr><td><b>Login Name:</b></td><td><input type='text' name='newlogin' value='<?= $newlogin ?>' size=15></td></tr>
        <tr><td><b>First Name:</b></td><td><input type='text' name='first_name' value='<?= $first_name ?>' size=15></td></tr>
        <tr><td><b>Last Name:</b></td><td><input type='text' name='last_name' value='<?= $last_name ?>' size=15></td></tr>
        <tr><td><b>Email Address:</b></td><td><input type='text' name='email' value='<?= $email ?>' size=30></td></tr>
        <tr><td colspan='2'><b>Permissions:</b></td></tr>
        <tr><td colspan='2'><input type='checkbox' name='staff_flag' <?= $staff_checked ?>>Staff member</td></tr>
        <tr><td colspan='2'><input type='checkbox' name='admin_priv' <?= $admin_checked ?>>Administrative privileges</td></tr>
        <tr><td colspan='2'><input type='submit' name='submit' value='Update User'></td></tr>
        </form>
        </td></tr></table>
        
        </td></tr></table><br><br>
        <form action="users.php" method="post">
        <input type='hidden' name='cmd' value='delete'>
        <input type='hidden' name='userid' value='<?=$userid?>'>
        <input type='submit' value='Delete This User'>
        </form><br>
    
<?        
    
    } else {
        print "Error retrieving user data from database.<br><br>\n";
    }
    mysql_free_result ($get_user);

} else if ($cmd == "update") {

    if (isset($staff_flag)) {
        $staff_flag = "Y";
        $staff_checked = "checked";
    } else {
        $staff_flag = "N";
        $staff_checked = "";
    }
    
    if (isset($admin_priv)) {
        $admin_priv = "Y";
        $admin_checked = "checked";
    } else {
        $admin_priv = "N";
        $admin_checked = "";
    }
    
    // Validate form data
    $repeat_entry = 0;
    if (!$newlogin) {
        printf("<font color='red'>Error: Log-in Name was not specified and is required.</font><br>\n");
        $repeat_entry = 1;
    } else if (!$last_name) {
        printf("<font color='red'>Error: Last Name was not specified and is required.</font><br>\n");
        $repeat_entry = 1;
    } else if (!$email) {
        printf("<font color='red'>Error: Email Address was not specified and is required.</font><br>\n");
        $repeat_entry = 1;
    }
    
    if ($repeat_entry) {
?>        
        <h3>Edit Information for user <?=$first_name?> <?=$last_name?></h3>
        <form action="users.php" method="post">
        <input type='hidden' name='cmd' value='update'>
        <input type='hidden' name='userid' value='<?=$userid?>'>
        <table cellpadding=0><tr><td>
        <tr><td><b>Log-in Name:</b></td><td><input type='text' name='newlogin' value='<?= $newlogin ?>' size=15></td></tr>
        <tr><td><b>First Name:</b></td><td><input type='text' name='first_name' value='<?= $first_name ?>' size=15></td></tr>
        <tr><td><b>Last Name:</b></td><td><input type='text' name='last_name' value='<?= $last_name ?>' size=15></td></tr>
        <tr><td><b>Email Address:</b></td><td><input type='text' name='email' value='<?= $email ?>' size=30></td></tr>
        <tr><td colspan='2'><b>Permissions:</b></td></tr>
        <tr><td colspan='2'><input type='checkbox' name='staff_flag' <?= $staff_checked ?>>Staff member</td></tr>
        <tr><td colspan='2'><input type='checkbox' name='admin_priv' <?= $admin_checked ?>>Administrative privileges</td></tr>
        <tr><td colspan='2'><input type='submit' name='submit' value='Update User'></td></tr>
        </form>
        </td></tr></table>
        
        </td></tr></table><br><br>
        <form action="users.php" method="post">
        <input type='hidden' name='cmd' value='delete'>
        <input type='hidden' name='userid' value='<?=$userid?>'>
        <input type='submit' value='Delete This User'>
        </form><br>
    
<?        
        
    } else {

        $query = "update users " .
            "set first_name=\"$first_name\", last_name=\"$last_name\", login=\"$newlogin\", " .
            "email=\"$email\", staff_flag=\"$staff_flag\", admin_priv=\"$admin_priv\" " .
            "where user_id=\"$userid\" ";
        $edit_user = mysql_query($query) or die(mysql_error());

            
    }
        
} else if ($cmd == "delete") {
    
    $get_user = mysql_query("select user_id, first_name, last_name, login, " .
        "email, staff_flag, admin_priv " .
        "from users where user_id = \"$userid\" ");
        
    if ($row = mysql_fetch_array($get_user)) {
    
        $userid = $row[0];
        $first_name = $row[1];
        $last_name = $row[2];
        $username = $row[3];
        
        printf("<p>You are about to delete user <b>$username</b> (<b>$first_name $last_name</b>).\n");
        printf("Please confirm or cancel this deletion.</p>");
        printf("<table><tr><td>\n");
        printf("<form action='users.php' method='post'>\n");
        printf("<input type='hidden' name='cmd' value='confirm'>\n");
        printf("<input type='hidden' name='userid' value='%s'>\n", $userid);
        printf("<input type='submit' value='Confirm Deletion'>\n");
        printf("</form></td><td>\n");
        printf("<form action='users.php' method='post'>\n");
        printf("<input type='submit' value='Cancel'>\n");
        printf("</form></td></tr></table>\n");
    
        printf("<table frame=border rules=all border=1 bgcolor=white cellpadding=4 bgcolor='$color2'>\n");
        printf("<tr bgcolor='$color2'><th colspan=2>View for user <b>$row[1] $row[2]</b></th></tr>\n");
        printf("<tr><td colspan=2>Username: <b>$row[3]</b></td></tr>\n");
        printf("<tr><td colspan=2>Email address: <b>$row[4]</b></td></tr>\n");
        printf("<tr><td valign=top>Permissions:</td><td>\n");
        printf("Staff member: <b>$row[5]</b><br>\n");
        printf("Administrative privileges: <b>$row[6]</b><br>\n");
        printf("</td></table><br><br>\n");
    } else {
        print "Error retrieving user data from database.<br><br>\n";
    }
    mysql_free_result ($get_user);
    
} else if ($cmd == "confirm") {

    $delete_user = mysql_query("delete from users where user_id=\"$userid\" ")
        or die(mysql_error());
} else {

    if ($_SESSION['SESSION_ADMIN'] == "Y") {
        printf("<form action='users.php' method='post'>\n");
        printf("<input type='hidden' name='cmd' value='add'><input type='submit' value='Add New User'></form>\n");
    } else {
        printf("To add a new user or edit user information, contact <a href='mailto:$admin_email'>$admin_name</a>.<br><br>\n");
    }
}

# Display existing users
printf("<b>Current User List</b>\n");
printf("<table frame=border rules=all border=1 bgcolor=white cellspacing=2 bgcolor='$heading_color'>\n");
printf("<tr bgcolor='$heading_color'><th></th>");
if ($_SESSION['SESSION_ADMIN'] == "Y") {
    printf("<th></th>");
}
printf("<th>&nbsp; Real Name&nbsp; </th><th>&nbsp; Username &nbsp; </th><th>&nbsp; Email address &nbsp; </th></tr>\n");
$get_users = mysql_query("select user_id, first_name, last_name, login, " .
    "email from users order by last_name ");
while ($row = mysql_fetch_array($get_users))
{
    printf("<tr><td bgcolor='$heading_color'>");
    printf("<form action='users.php' method='post'>");
    printf("<input type='hidden' name='cmd' value='view'>");
    printf("<input type='hidden' name='userid' value='%s'>", $row[0]);
    printf("<input type='submit' value='View'></form>\n");
    printf("</td>\n");
    if ($_SESSION['SESSION_ADMIN'] == "Y") {
        printf("<td bgcolor='$heading_color'>");
        printf("<form action='users.php' method='post'>");
        printf("<input type='hidden' name='cmd' value='edit'>");
        printf("<input type='hidden' name='userid' value='%s'>", $row[0]);
        printf("<input type='submit' value='Edit'></form>\n");
        printf("</td>\n");
    }
    printf("<td> &nbsp; %s %s &nbsp; </td><td> &nbsp; %s &nbsp; </td><td> &nbsp; %s &nbsp;</td></tr>\n", $row[1], $row[2], $row[3], $row[4]);
}
mysql_free_result($get_users);
mysql_close($conn);
printf("</table>\n");

require("footer.php");
?>
