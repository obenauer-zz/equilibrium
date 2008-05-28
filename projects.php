<?php
require("check_login.php");
require_once("config.php");

// Check for passed arguments
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = "";
}

if (isset($_REQUEST['status'])) {
    $status = $_REQUEST['status'];
} else {
    $status = "Active";
}

if (isset($_REQUEST['staff'])) {
    $staff = $_REQUEST['staff'];
} else {
    if ($_SESSION['SESSION_STAFF'] == "Y") {
        $staff = $_SESSION['SESSION_USERID'];
    } else {
        $staff = 0;
    }
}

if (isset($_REQUEST['maxresults'])) {
    $maxresults = $_REQUEST['maxresults'];
    if ($maxresults == 0) {
        $maxresults = 10;
    }
} else {
    $maxresults = 10;
}

if (isset($_REQUEST['page'])) {
    $page = $_REQUEST['page'];
} else {
    $page = 1;
}

if (isset($_REQUEST['cmd'])) {
    $cmd = $_REQUEST['cmd'];
} else {
    $cmd = "";
}

if (isset($_REQUEST['title'])) {
    $title = $_REQUEST['title'];
} else {
    $title = "";
}

if (isset($_REQUEST['ptype'])) {
    $ptype = $_REQUEST['ptype'];
} else {
    $ptype = "";
}

if (isset($_REQUEST['description'])) {
    $description = $_REQUEST['description'];
} else {
    $description = "";
}

if (isset($_REQUEST['client'])) {
    $client = $_REQUEST['client'];
} else {
    $client = "";
}

if (isset($_REQUEST['contact'])) {
    $contact = $_REQUEST['contact'];
} else {
    $contact = "";
}

if (isset($_REQUEST['enterdate'])) {
    $enterdate = $_REQUEST['enterdate'];
} else {
    $enterdate = "";
}

if (isset($_REQUEST['startdate'])) {
    $startdate = $_REQUEST['startdate'];
} else {
    $startdate = "";
}

if (isset($_REQUEST['completedate'])) {
    $completedate = $_REQUEST['completedate'];
} else {
    $completedate = "";
}

if (isset($_REQUEST['project'])) {
    $project = $_REQUEST['project'];
} else {
    $project = 0;
}

if (isset($_REQUEST['duty'])) {
    $duty = $_REQUEST['duty'];
} else {
    $duty = 0;
}

if (isset($_REQUEST['visibility'])) {
    $visibility = $_REQUEST['visibility'];
} else {
    $visibility = 'Public';
}

if (isset($_REQUEST['newstatus'])) {
    $newstatus = $_REQUEST['newstatus'];
} else {
    $newstatus = "";
}

if (isset($_REQUEST['comment'])) {
    $comment = $_REQUEST['comment'];
} else {
    $comment = "";
}

if (isset($_REQUEST['submitter'])) {
    $submitter = $_REQUEST['submitter'];
} else {
    $submitter = "";
}

if (isset($_REQUEST['dirtree'])) {
    $dirtree = $_REQUEST['dirtree'];
} else {
    $dirtree = "";
}

if (isset($_FILES['filename'])) {
    $filename = $_FILES['filename']['name'];
} else {
    $filename = "";
}

if (isset($_REQUEST['existingfile'])) {
    $existingfile = $_REQUEST['existingfile'];
} else {
    $existingfile = "";
}

if (isset($_REQUEST['filecat'])) {
    $filecat = $_REQUEST['filecat'];
} else {
    $filecat = "";
}

if (isset($_REQUEST['filedesc'])) {
    $filedesc = $_REQUEST['filedesc'];
} else {
    $filedesc = "";
}

if (isset($_REQUEST['file_id'])) {
    $file_id = $_REQUEST['file_id'];
} else {
    $file_id = "";
}

if (isset($_REQUEST['order'])) {
    $order = $_REQUEST['order'];
} else {
    $order = "";
}

if (isset($_REQUEST['edit_priv'])) {
    $edit_priv = $_REQUEST['edit_priv'];
} else {
    $edit_priv = "N";
}

if (isset($_REQUEST['todostatus'])) {
    $todostatus = $_REQUEST['todostatus'];
} else {
    $todostatus = "Pending";
}

// Declare PHP functions
require("equilibrium.php");

function display_project_form($action, $project, $errormsg, $params) {

    require_once("config.php");
    global $background_color;
    global $heading_color;

    // Only the assigned staff or administrators can edit this project, unless it's not assigned
    if (($_SESSION['SESSION_ADMIN'] == "Y") || ($staff == $_SESSION['SESSION_USERID']) 
        || ($staff == 0)) {
        $edit_priv = "Y";
    } else {
        $edit_priv = "N";
    }
        
    // If parameters were passed, use those
    if ($params) {
        $title = $params[0];
        $ptype = $params[1];
        $description = $params[2];
        $staff = $params[3];
        $client = $params[4];
        $contact = $params[5];
        $visibility = $params[6];
        $status = $params[7];
        $enterdate = $params[8];
        $startdate = $params[9];
        $completedate = $params[10];
        
        printf("completedate = $completedate<br>\n");

        if ($enterdate == "0000-00-00") {
            $enterdate = "";
        }
        if ($startdate == "0000-00-00") {
            $startdate = "";
        }
        if ($completedate == "0000-00-00") {
            $completedate = "";
        }
    
    } else {
    
        if ($action == "add") {
            
            // If adding a project, initialize variables
            $title = "";
            $ptype = 0;
            $description = "";
            if ($_SESSION['SESSION_ADMIN'] == "Y") {
                if ($_SESSION['SESSION_STAFF'] == "Y") {
                    $staff = $_SESSION['SESSION_USERID'];
                } else {
                    $staff = 0;
                }
            } else {
                if ($_SESSION['SESSION_STAFF'] == "Y") {
                    $staff = $_SESSION['SESSION_USERID'];    
                } else {
                    $staff = 0;
                }
            }
            $client = 0;
            $contact = "";
            //$enterdate = date("F") . " " . date("j") . ", " . date("Y");
            $enterdate = date("m") . "/" . date("d") . "/" . date("Y");
            $startdate = "";
            $completedate = "";
            $status = "Pending";
            $icon = 0;
            $visibility = "Public";

        } else if ($action == "edit") {
        
            // If editing, retrieve existing fields from database
            $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
                or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
            mysql_select_db(DB_DATABASE);
            $get_fields = mysql_query("select p.title, t.project_type_id, p.description, " .
                "u.user_id, c.client_id, p.contact, " .
                "p.date_entered, p.date_started, p.date_completed, p.status, " .
                "p.visibility, p.icon_id from projects as p " .
                "left join project_types as t on p.project_type_id = t.project_type_id " .
                "left join users as u on p.staff_assigned = u.user_id " .
                "left join clients as c on p.client_id = c.client_id " .
                "where p.project_id = \"$project\" ");
            if ($row = mysql_fetch_array($get_fields)) {
                $title = $row['title'];
                $ptype = $row['project_type_id'];
                $description = $row['description'];
                $staff = $row['user_id'];
                $client = $row['client_id'];
                $contact = $row['contact'];
                $enterdate = standard_date($row['date_entered']);
                $startdate = standard_date($row['date_started']);
                $completedate = standard_date($row['date_completed']);
                $status = $row['status'];
                //$icon = $row['icon_id'];
                $visibility = $row['visibility'];
            } else {
                printf("Error: unable to retrieve this project from database.<br>\n");
            }
            mysql_free_result($get_fields);
            mysql_close($conn);
        
        }
    }

    // If this is a private project, make sure user has access
    if (($visibility == "Private") && ($_SESSION['SESSION_USERID'] != $staff)) {
        printf("<p>This is a private project.  Only its owner has access to this page.</p>\n");
        printf("<p>Return to <a href='projects.php'>Projects page</a>.</p>\n");
        return;
    }
    
    // Add/Edit project form
    printf("<form action='projects.php' method='post' name='project'>\n");
    printf("<table cellpadding='10'><tr valign='top'><td width='50%%'>\n");
    //printf("<div id='project_left'>\n");
    
    printf("<table>\n");

    // Project title
    printf("<tr><td>Title</td>\n");
    printf("<td><input type='text' name='title' style='{width: 25em}' value=\"$title\"></td></tr>\n");
    
    // Project type
    printf("<tr><td>Type</td>\n");
    printf("<td><select name='ptype' id='ptype' size='1' onChange=\"document.getElementById('ptypedesc').firstChild.nodeValue = ptypedescs[document.getElementById('ptype').selectedIndex];\">\n");

    // Retrieve project type names and descriptions from database
    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db(DB_DATABASE);
    $get_types = mysql_query("select project_type_id, name, description from project_types " .
        "order by project_type_id asc ");
    printf("<option value='0' selected>\n");
    $projtypedesclist = array();
    $projtypedesclist[0] = "Select a type for this project.";
    $nprojtypes = 1;
    while ($row = mysql_fetch_array($get_types)) {
        if ($ptype == $row[0]) {
            printf("<option value='$row[0]' selected>$row[1]\n");            
        } else {
            printf("<option value='$row[0]'>$row[1]\n");            
        }
        $projtypedesclist[$nprojtypes] = "$row[2]";
        $nprojtypes++;
    }
    mysql_free_result($get_types);
    mysql_close($conn);
    
    printf("</select></td></tr>\n");
    
    // Create JavaScript array of project type descriptions
    printf("<script language='JavaScript'>\n");
    printf("var ptypedescs = new Array(%d);\n", $nprojtypes);
    for ($i = 0; $i < $nprojtypes; $i++) {
        printf("ptypedescs[$i] = '$projtypedesclist[$i]';\n");
    }
    printf("</script>\n");
    if ($ptype == "") {
        $ptype = 0;
    }
    printf("<tr><td colspan='2'><div id='ptypedesc'>$projtypedesclist[$ptype]</div></td></tr>\n");

    // Project description
    printf("<tr><td colspan='2'>Description</td>\n");
    printf("<tr><td colspan='2'><textarea name='description' class='description' rows='6' cols='80'>" .
        "$description</textarea></td></tr>\n");
    
    printf("</table>\n");

    //printf("</div><div id='project_right'>\n");
    printf("</td><td width='50%%'>\n");
    
    printf("<table>\n");

    // Assigned staff member -- default is whoever's logged in
    printf("<tr><td align='right'>Assigned to: &nbsp; </td>");

    // Only administrators can assign projects to other people
    if ($_SESSION['SESSION_ADMIN'] == "Y") {
        printf("<td><select name='staff' size='1'>\n");
    
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $get_staff = mysql_query("select user_id, first_name, last_name from users " .
            "where staff_flag = \"Y\" order by last_name ");
        if ($staff == 0) {
            printf("<option value='0' selected>\n");
        } else {
            printf("<option value='0'>\n");
        }
        $displayed_user = "";
        while ($row = mysql_fetch_array($get_staff)) {
            if ($staff == $row[0]) {
                printf("<option value='$row[0]' selected>$row[1] $row[2]\n");
                $displayed_user = "$row[1] $row[2]";
            } else {
                printf("<option value='$row[0]'>$row[1] $row[2]\n");
            }
            
        }
        mysql_free_result($get_staff);
        mysql_close($conn);
        printf("</select></td></tr>\n");
    
    } else {

        if ($staff == 0) {
            printf("<td><select name='staff' size='1'>\n");
            printf("<option value='0' selected>\n");
            printf("<option value='%d'>%s\n", $_SESSION['SESSION_USERID'], $_SESSION['SESSION_USER']);
            printf("</select></td></tr>\n");

        } else {
            printf("<td>%s\n", $_SESSION['SESSION_USER']);
            printf("<input type='hidden' name='staff' value='$staff'>");
            printf("</td></tr>\n");

        }
    }
    
    // Client
    printf("<tr><td align='right'>Client: &nbsp; </td>");
    printf("<td><select name='client' size='1'>\n");
    printf("<option value='0' selected>\n");
    
    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db(DB_DATABASE);

    // First list faculty members the logged-in user has worked for before
    $get_past_clients = mysql_query("select h.client_id, count(*) as countval, c.first_name, c.last_name " .
        "from client_history as h, clients as c where h.client_id = c.client_id " .
        "and h.staff_id = \"" . $_SESSION['SESSION_USERID'] . "\" group by h.client_id order by countval desc ");
    while ($row = mysql_fetch_array($get_past_clients)) {
        printf("<option value='$row[0]' style='background-color: %s;'>$row[2] $row[3]\n", $heading_color);
    }
    mysql_free_result($get_past_clients);
    printf("<option value='0'>-----------------------\n");

    // Now list all faculty members
    $get_clients = mysql_query("select client_id, first_name, last_name from clients " .
        "order by last_name, first_name ");
    while ($row = mysql_fetch_array($get_clients)) {
        if ($client == $row[0]) {
            printf("<option value='$row[0]' selected>$row[1] $row[2]\n");
        } else {
            printf("<option value='$row[0]'>$row[1] $row[2]\n");
        }
    }
    mysql_free_result($get_clients);
    mysql_close($conn);
    
    printf("</select></td></tr>\n");
    
    // Contact person
    printf("<tr><td align='right'>Contact: &nbsp; </td>");
    printf("<td><input type='text' name='contact' size='20' value='$contact'></td></tr>\n");

    // Visibility
    printf("<tr><td align='right'>Visibility: &nbsp; </td>");
    printf("<td><select name='visibility' size='1'>\n");
    if ($visibility == 'Private') {
        printf("<option value='Public'>Public\n");
        printf("<option value='Private' selected>Private\n");
    } else {
        printf("<option value='Public' selected>Public\n");
        printf("<option value='Private'>Private\n");
    }
    
    // Only show date forms when editing projects
    if ($action == "edit") {

        // Date entered
        printf("<tr><td align='right'>Date entered:</td><td>\n");
        show_date_form('enterdate', $enterdate);
        printf("</td></tr>\n");
        
        // Date started
        printf("<tr><td align='right'>Date started:</td><td>\n");
        show_date_form('startdate', $startdate);
        printf("</td></tr>\n");
        
        // Date completed
        printf("<tr><td align='right'>Date completed:</td><td>\n");
        show_date_form('completedate', $completedate);
        printf("</td></tr>\n");
    
    }
    
    // Project status
    $statuslist = array("Proposed", "Pending", "Active", "Suspended", "Aborted", "Completed");
    printf("<tr><td align='right'>Status: &nbsp; </td>");
    printf("<td><select name='status' size='1'>\n");
    for ($i = 0; $i < count($statuslist); $i++) {
        if ($status == $statuslist[$i]) {
            printf("<option value='$statuslist[$i]' selected>$statuslist[$i]\n");
        } else {
            printf("<option value='$statuslist[$i]'>$statuslist[$i]\n");
        }
    }
    printf("</select></td></tr>\n");

    printf("</table>\n");

    // Print validation error message, if any
    if ($errormsg) {
        printf("$errormsg");
    }

    //printf("</div>\n");
    printf("</td></tr></table>\n");
    
    if ($action == "add") {
        printf("<input type='hidden' name='cmd' value='insert'>\n");
        printf("<input type='submit' value='Submit project'>\n");
    } else if ($action == "edit") {
        printf("<input type='hidden' name='cmd' value='update'>\n");
        printf("<input type='hidden' name='project' value='$project'>\n");
        printf("<input type='submit' value='Update project'>\n");
    }
    printf("</form>\n");

}

function display_project_details($project) {

    require_once("config.php");
    global $background_color;
    global $heading_color;

    // Initialize variables
    $title = "";
    $ptype = 0;
    $description = "";
    $staff = $_SESSION['SESSION_USERID'];
    $client = 0;
    $contact = "";
    $enterdate = "";
    $startdate = "";
    $completedate = "";
    $status = "";
    $icon = 0;
    $visibility = "";

    // Retrieve existing fields from database
    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db(DB_DATABASE);
    $get_fields = mysql_query("select p.title, t.name, p.description, " .
        "u.first_name, u.last_name, c.first_name as client_first, " .
        "c.last_name as client_last, p.contact, p.visibility, " .
        "p.date_entered, p.date_started, " .
        "p.date_completed, p.status, p.icon_id from projects as p " .
        "left join project_types as t on p.project_type_id = t.project_type_id " .
        "left join users as u on p.staff_assigned = u.user_id " .
        "left join clients as c on p.client_id = c.client_id " .
        "where p.project_id = \"$project\" ");
    if ($row = mysql_fetch_array($get_fields)) {
        $title = $row['title'];
        $ptype = $row['name'];
        $description = $row['description'];
        $displayed_user = $row['first_name'] . " " . $row['last_name'];
        $client = $row['client_first'] . " " . $row['client_last'];
        $contact = $row['contact'];
        $enterdate = pretty_date($row['date_entered']);
        $startdate = pretty_date($row['date_started']);
        $completedate = pretty_date($row['date_completed']);
        $status = $row['status'];
        $icon = $row['icon_id'];
        $visibility = $row['visibility'];
    } else {
        printf("Error: unable to retrieve this project from database.<br>\n");
    }
    mysql_free_result($get_fields);
    mysql_close($conn);

    // Only the assigned staff or administrators can edit this project, unless it's not assigned
    if (($_SESSION['SESSION_ADMIN'] == "Y") || ($displayed_user == $_SESSION['SESSION_USER']) 
        || (trim($displayed_user) == "")) {
        $edit_priv = "Y";
    } else {
        $edit_priv = "N";
    }

    // If this is a private project, make sure user has access
    if (($visibility == "Private") && ($_SESSION['SESSION_USERID'] != $staff)) {
        printf("<p>This is a private project.  Only its owner has access to this page.</p>\n");
        printf("<p>Return to <a href='projects.php'>Projects page</a>.</p>\n");
        return;
    }
    
    // Page heading
    printf("<h2>Project Details: $title</h2>\n");
        
    // Button: Add New Project
    printf("<table><tr>\n");
    if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
        printf("<td><form action='projects.php' method='post'>\n");
        printf("<input type='hidden' name='action' value='add'><input type='submit' value='Add New Project'></form></td>\n");
    }
    // Button: Edit This Project
    if ($edit_priv == "Y") {
        printf("<td><form action='projects.php' method='post'>\n");
        printf("<input type='hidden' name='action' value='edit'>\n");
        printf("<input type='hidden' name='project' value='$project'>\n");
        printf("<input type='submit' value='Edit This Project'></form></td>\n");
    }
    printf("</tr></table>\n");
        
    // Project description
    $description = ereg_replace("%", "%%", $description);
    printf("<p>$description</p>\n");
    
    printf("<table cellpadding='10'><tr valign='top'><td width='50%%'>\n");
    //printf("<table cellpadding='10'><tr valign='top'><td>\n");
    
    printf("<table>\n");

    // Project type
    printf("<tr><td>Type: &nbsp; </td>\n");
    printf("<td class='values' style='border: 1px solid $heading_color;'>$ptype<br></td></tr>\n");

    // Assigned staff member -- default is whoever's logged in
    printf("<tr><td>Assigned to: &nbsp; </td>");
    printf("<td class='values' style='border: 1px solid $heading_color;'>$displayed_user<br></td></tr>\n");
    
    // Client
    printf("<tr><td>Client: &nbsp; </td>");
    printf("<td class='values' style='border: 1px solid $heading_color;'>$client<br></td></tr>\n");
    
    // Contact person
    printf("<tr><td>Contact: &nbsp; </td>");
    printf("<td class='values' style='border: 1px solid $heading_color;'>$contact<br></td></tr>\n");

    // Visibility
    printf("<tr><td>Visibility: &nbsp; </td>");
    if ($visibility == "Private") {
        printf("<td class='values' style='border: 1px solid $heading_color; background-color: $heading_color'>$visibility<br></td></tr>\n");
    } else {
        printf("<td class='values' style='border: 1px solid $heading_color;'>$visibility<br></td></tr>\n");
    }

    printf("</table>\n");

    //printf("</div><div id='project_right'>\n");
    printf("</td><td width='50%%' valign='bottom'>\n");
    //printf("</td><td valign='bottom'>\n");
    
    printf("<table>\n");

    // Date entered
    printf("<tr><td align='right'>Date entered: &nbsp; </td>");
    printf("<td class='values' style='border: 1px solid $heading_color;'>$enterdate<br></td></tr>\n");

    // Date started
    printf("<tr><td align='right'>Date started: &nbsp; </td>");
    printf("<td class='values' style='border: 1px solid $heading_color;'>$startdate<br></td></tr>\n");

    // Date completed
    printf("<tr><td align='right'>Date completed: &nbsp; </td>");
    printf("<td class='values' style='border: 1px solid $heading_color;'>$completedate<br></td></tr>\n");

    // Project status
    printf("<tr><td align='right'>Status: &nbsp; </td>");
    printf("<td class='values' style='border: 1px solid $heading_color;'>$status<br></td></tr>\n");
    
    // Project icon
    printf("<tr><td align='right'>Icon: &nbsp; </td>");
    if ($icon == 0) {
        printf("<td class='values' style='border: 1px solid $heading_color;'>None<br></td></tr>\n");
    } else {
        printf("<td class='values' style='border: 1px solid $heading_color;'><img src='icons18/icon_%d.png'><br></td></tr>\n", $icon);
    }

    printf("</table>\n");

    printf("</td></tr></table>\n");

    return;
}


function show_project_files($project) {

    global $heading_color;
    
    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db(DB_DATABASE);
    $get_files = mysql_query("select file_id, file_type, file_name, description, " .
        "upload_date, upload_time, file_path from files where project_id = \"$project\" " .
        "order by upload_time ");
    if (mysql_num_rows($get_files)) {
        while ($row = mysql_fetch_array($get_files, MYSQL_ASSOC)) {
            printf("<a href=\"%s/%s\">%s</a>, %s \n", $row['file_path'], 
                $row['file_name'], $row['file_name'], $row['description']);
            if ($row['file_type']) {
                printf("(%s, %s) \n", $row['file_type'], 
                    short_date($row['upload_date']));
            } else {
                printf("(%s) &nbsp; \n", short_date($row['upload_date']));
            }
            printf("<img height='16' src='images/delete.png' title='Delete' " .
                "onclick='show_item(\"confirm_" . $row['file_id'] . "\");'><br>\n");

            // Div to confirm deletion
            printf("<div class='confirm' id='confirm_" . $row['file_id'] . 
                "' ><font color='red'>" .
                "<center>Are you sure you want to delete this file?</font><br>");
            printf("<form action='projects.php#file_input' method='post'>\n");
            printf("<input type='hidden' name='file_id' value='%d'>\n", 
                $row['file_id']);
            printf("<input type='hidden' name='dirtree' value='%s'>\n", 
                $row['file_path']);
            printf("<input type='hidden' name='existingfile' value='%s'>\n", 
                $row['file_name']);
            printf("<input type='hidden' name='cmd' value='deletefile'>\n");
            printf("<input type='hidden' name='project' value='$project'>\n");
            printf("<input type='submit' value='Confirm Deletion'>  &nbsp; &nbsp; ");
            printf("<input type='button' value='Cancel' onclick='hide_item(\"confirm_" . 
                $row['file_id'] . "\");'></center><br>");
            printf("</form>\n</div>\n");

        }

    } else {
        printf("<p>No files uploaded for this project.</p>\n");
    }

    mysql_free_result($get_files);
    mysql_close($conn);
    
    return;
}

// Commands that don't generate HTML output
switch($cmd) {
    case "insert":

        //$enterdate = mysql_date($enterdate);
        //$startdate = mysql_date($startdate);

        // Validate submitted data
        if ((!trim($title)) || (!trim($ptype)) || (!trim($description))) {
        //if ((!trim($title)) || (!trim($description))) {

            // Missing fields; return to form
            $errormsg = "<p><font color='red'>* A title, type, and description are required for each project.</font></p>\n";
            $activepage = "Projects";
            require("header.php");
            printf("<h2>Add New Project</h2>\n");
    
            if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
                //$enterdate = standard_date($enterdate);
                //$startdate = standard_date($startdate);
                $params = array($title, $ptype, $description, $staff, $client, $contact, 
                    $visibility, $status, "", "", "");
                display_project_form("add", 0, "$errormsg", $params);
            } else {
                printf("<h3>Add New Project -- Not Authorized</h3>");
                printf("<p>Only staff members and administrators can add new projects.</p>\n");
                printf("<p>Contact $admin_name (<a href='mailto:$admin_email'>" .
                    "$admin_email)</a> to enable your user account to add projects.</p>\n");
                require("footer.php");
            }

            exit;

        } else {

            // Data passes inspection; pick an icon for this duty
            $icons = array();
            for ($i = 0; $i < $number_icons; $i++) {
                $icons[$i] = 0;
            }
            $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
                or die ("Cannot connect to database. " . mysql_error() . 
                "\n<br>");
            mysql_select_db (DB_DATABASE);
            $get_used_icons = mysql_query("select icon_id, count(*) as countval from icon_usage " .
                //"group by icon_id order by icon_id asc, countval asc ");
                "group by icon_id order by countval asc ");
            $defaulticon = 0;
            if (mysql_num_rows($get_used_icons) == 0) {
                $icon = 1;
            } else {
                while ($row = mysql_fetch_array($get_used_icons)) {
                    if (!$defaulticon) {
                        $defaulticon = $row[0]; // default icon is least used one
                    }
                    $icons[$row[0]] = $row[1];
                }
            }
            mysql_free_result($get_used_icons);
            mysql_close($conn);
            $foundzero = 0;
            $iconstart = mt_rand(1, $number_icons);
            if ($iconstart < ($number_icons / 2)) {

                // If randomly chosen start position is closer to the
                // beginning of the icon list, go toward the end to
                // look for zeroes
                for ($i = $iconstart; $i <= $number_icons; $i++) {
                    if ($icons[$i] == 0) {
                        // Find first zero, and use that icon
                        $icon = $i;
                        $foundzero = 1;
                        break;
                    }
                }
            } else {

                // If randomly chosen start position is closer to the
                // end of the icon list, go toward the beginning to
                // look for zeroes
                for ($i = $iconstart; $i >= 1; $i--) {
                    if ($icons[$i] == 0) {
                        // Find first zero, and use that icon
                        $icon = $i;
                        $foundzero = 1;
                        break;
                    }
                }
            }
            if (!$foundzero) {
                $icon = $defaulticon;  // If no zero-used icons found, pick least-used one
            }

            // Add new project to database
            $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
                or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
            mysql_select_db (DB_DATABASE);
            $title = mysql_real_escape_string($title);
            $description = mysql_real_escape_string($description);
            $start_trans = mysql_query("start transaction ");
            $add_project = mysql_query("insert into projects " .
                "(title, project_type_id, description, staff_assigned, client_id, " .
                "contact, date_entered, status, visibility, icon_id) " .
                "values (\"$title\", \"$ptype\", \"$description\", \"$staff\", " .
                "\"$client\", \"$contact\", curdate(), \"$status\", " .
                "\"$visibility\", \"$icon\") ");

            // If initial status is Active, mark today as the actual start date
            $project = mysql_insert_id($conn);
            if ($status == "Active") {
                $mark_active = mysql_query("update projects set date_started = curdate() " .
                    "where project_id = \"$project\" ");
            }

            // If initial status is Completed, mark the completion date
            if ($status == "Completed") {
                $mark_completed = mysql_query("update projects set date_completed = curdate() " .
                    "where project_id = \"$project\" ");
            }
                    
            // Add current values to project history
            $add_history = mysql_query("insert into project_history " .
                "(project_id, status, " .
                "modification_date, modification_time) " .
                "values (\"$project\", \"$status\", " .
                "curdate(), now()) ");

            // Insert this project in client history
            $add_client = mysql_query("insert into client_history " .
                "(staff_id, client_id, project_id, client_entered_date, client_entered_time) " .
                "values (\"$staff\", \"$client\", \"$project\", curdate(), now()) ");

            // Insert the selected icon into the icon usage table
            $add_icon = mysql_query("insert into icon_usage " .
                "(icon_id, project_id) " .
                "values (\"$icon\", \"$project\") ");

            // Commit transaction
            $stop_trans = mysql_query("commit ");
            if (!$stop_trans) {
                require("header.php");
                printf("<p>Error adding project \"$title\": %s</p>\n", mysql_error());
                require("footer.php");
                mysql_close($conn);
                exit;
            }
            mysql_close($conn);

            // Send user to project detail page
            header("Location: projects.php?action=view&project=$project");
            exit();

        }

        break;

    case "update":

        // Validate submitted data
        if ((!trim($title)) || (!trim($ptype)) || (!trim($description))) {

            // Missing fields; return to form
            $errormsg = "<p><font color='red'>* A title, type, and description are required for each project.</font></p>\n";
            $activepage = "Projects";
            require("header.php");
            printf("<h2>Edit Project</h2>\n");
    
            if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
                $params = array($title, $ptype, $description, $staff, $client, $contact, 
                    $visibility, $status, $enterdate, $startdate, $completedate);
                //$params = "";
                display_project_form("edit", $project, $errormsg, $params);
            } else {
                printf("<h3>Edit Project -- Not Authorized</h3>");
                printf("<p>Only staff members and administrators can edit projects.</p>\n");
                printf("<p>Contact $admin_name (<a href='mailto:$admin_email'>" .
                    "$admin_email)</a> to enable your user account to edit projects.</p>\n");
                require("footer.php");
            }

            exit;

        } else {

            // Data passes inspection; update project in database
            $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
                or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
            mysql_select_db (DB_DATABASE);
            $title = mysql_real_escape_string($title);
            $description = mysql_real_escape_string($description);
            $start_trans = mysql_query("start transaction ");

            // Convert date formats to MySQL
            $enterdate = mysql_date($enterdate);
            $startdate = mysql_date($startdate);
            $completedate = mysql_date($completedate);

            // Check what the existing project status is
            $check_status = mysql_query("select status from projects " .
                "where project_id = \"$project\" ");
            if ($row = mysql_fetch_array($check_status)) {
                $starting_status = $row[0];
            } else {
                $starting_status = "";
            }
            mysql_free_result($check_status);
            
            // Update project fields
            $edit_project = mysql_query("update projects " .
                "set title = \"$title\", project_type_id = \"$ptype\", " .
                "description = \"$description\", staff_assigned = \"$staff\", client_id = \"$client\", " .
                "contact = \"$contact\", date_entered = \"$enterdate\", " .
                "date_started = \"$startdate\", date_completed = \"$completedate\", " .
                "status = \"$status\", visibility = \"$visibility\" " .
                "where project_id = \"$project\" ");
                
            // If new status is Active, mark the actual start date
            $get_start_date = mysql_query("select date_started from projects " .
                "where project_id = \"$project\" ");
            if ($row = mysql_fetch_array($get_start_date)) {
                if (($row[0] == NULL) || ($row[0] == "0000-00-00")) {
                    if ($status == "Active") {
                        $mark_active = mysql_query("update projects set date_started = curdate() " .
                            "where project_id = \"$project\" ");
                    }
                    
                }
            }
            mysql_free_result($get_start_date);

            // If new status is Completed, mark the actual stop date
            if (($status == "Completed") && ($starting_status != "Completed")) {
                $mark_completed = mysql_query("update projects set date_completed = curdate() " .
                    "where project_id = \"$project\" ");
            }
                    
            // Update project history if status changed
            if ($starting_status != $status) {
                $edit_history = mysql_query("insert into project_history " .
                    "(project_id, status, modification_date, modification_time) " .
                    "values (\"$project\", \"$status\", curdate(), now()) ");
            }

            // Commit transaction
            $stop_trans = mysql_query("commit ");
            if (!$stop_trans) {
                require("header.php");
                printf("<p>Error updating project: %s</p>\n", mysql_error());
                require("footer.php");
                mysql_close($conn);
                exit;
            }
            mysql_close($conn);

            // Send user to project detail page
            header("Location: projects.php?action=view&project=$project");
            exit();

        }

        break;

    case "change_status":

        // Change project status
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db (DB_DATABASE);
        $start_trans = mysql_query("start transaction ");

        // Check what the existing project status is
        $check_status = mysql_query("select status from projects " .
            "where project_id = \"$project\" ");
        if ($row = mysql_fetch_array($check_status)) {
            $starting_status = $row[0];
        } else {
            $starting_status = "";
        }
        mysql_free_result($check_status);
            
        $edit_project = mysql_query("update projects " .
            "set status = \"$newstatus\" where project_id = \"$project\" ");

        // If new status is Active, mark the actual start date
        $get_date_started = mysql_query("select date_started from projects " .
            "where project_id = \"$project\" ");
        if ($row = mysql_fetch_array($get_date_started)) {
            if (($row[0] == NULL) || ($row[0] == "0000-00-00")) {
                if ($newstatus == "Active") {
                    $mark_active = mysql_query("update projects set date_started = " .
                        "curdate() where project_id = \"$project\" ");
                }
                
            }
        }
        mysql_free_result($get_date_started);

        // If new status is Completed, mark the actual stop date
        if (($newstatus == "Completed") && ($starting_status != "Completed")) {
            $mark_completed = mysql_query("update projects set act_stop_date = curdate() " .
                "where project_id = \"$project\" ");
        }
                    
        // Update project history
        $edit_history = mysql_query("insert into project_history " .
            "(project_id, status, modification_date, modification_time) " .
            "values (\"$project\", \"$newstatus\", curdate(), now()) ");

        // Commit transaction
        $stop_trans = mysql_query("commit ");
        if (!$stop_trans) {
            $activepage = "Projects";
            require("header.php");
            printf("<p>Error updating project: %s</p>\n", mysql_error());
            require("footer.php");
            mysql_close($conn);
            exit;
        }
        mysql_close($conn);

        // Send user to project page
        header("Location: projects.php?status=$status&staff=$staff&page=$page&maxresults=$maxresults");
        exit();

        break;

    case "updatecommentlist":
        // create TasksList object
        //$myTasksList = new TasksList();
        // read parameters
        $action = $_GET['action'];
        $content = $_GET['content'];
        $project = $_GET['project'];
        // clear the output
        //if(ob_get_length()) ob_clean();
        // headers are sent to prevent browsers from caching
        
        //header('Expires: Fri, 25 Dec 1980 00:00:00 GMT'); // time in the past
        //header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . 'GMT');
        //header('Cache-Control: no-cache, must-revalidate');
        //header('Pragma: no-cache');
        //header('Content-Type: text/html');
        // execute the client request and return the updated tasks list
        //echo $myTasksList->Process($content, $action, $project);
        echo process_comment_changes($content, $action, $project);

        break;

    case "addcomment":

        // Add comment to comments table
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db (DB_DATABASE);
        $comment = mysql_real_escape_string($comment);
        $add_comment = mysql_query("insert into comments " .
            "(project_id, comment_text, submitter_id, submit_date, submit_time) " .
            "values (\"$project\", \"$comment\", \"$submitter\", curdate(), now()) ");

        // Print error message if there's a problem
        if (!$add_comment) {
            $activepage = "Projects";
            require("header.php");
            printf("<p>Error adding comment: %s</p>\n", mysql_error());
            require("footer.php");
            mysql_close($conn);
            exit;
        }
        mysql_close($conn);

        // Return user to project details page
        header("Location: projects.php?action=view&project=$project");
        break;

    case "uploadfile":

        if ($filename) {

            $dirtree = "$upload_dir/P" . $project;
            if (!file_exists($dirtree)) {
                mkdir($dirtree);
                chmod($dirtree, 0775);
            }

            // Save uploaded file to file path
            if (!file_exists("$dirtree/$filename")) {
                $try_upload = move_uploaded_file($_FILES['filename']['tmp_name'], 
                    "$dirtree/$filename");
                if (!$try_upload) {
                    $activepage = "Projects";
                    require("header.php");
                    printf("<p>The file upload failed.</p>\n");
                    require("footer.php");
                }
                chmod("$dirtree/$filename", 0775);
            } else {
                $activepage = "Projects";
                require("header.php");
                printf("<p>File upload aborted: A file with this name ($dirtree/$filename) already exists.</p>\n");
                require("footer.php");
                exit;
            }

            if ((!$filecat) || ($filecat == "Null") || ($filecat == NULL)) {
    
                // Insert file into files table
                $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
                    or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
                mysql_select_db (DB_DATABASE);
                $add_file = mysql_query("insert into files " .
                    "(project_id, file_name, file_path, uploaded_by, upload_date, upload_time) " .
                    "values (\"$project\", \"$filename\", \"$dirtree\", \"$submitter\", " .
                    "curdate(), now()) ");
        
                // Print error message if there's a problem
                if (!$add_file) {
                    $activepage = "Projects";
                    require("header.php");
                    printf("<p>Error uploading file: %s</p>\n", mysql_error());
                    require("footer.php");
                    mysql_close($conn);
                    exit;
                }
                mysql_close($conn);
    
            } else {
    
                // Insert file into files table
                $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
                    or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
                mysql_select_db (DB_DATABASE);
                $add_file = mysql_query("insert into files " .
                    "(project_id, file_name, file_path, file_type, description, uploaded_by, " .
                    "upload_date, upload_time) values (\"$project\", \"$filename\", \"$dirtree\", " .
                    "\"$filecat\", \"$filedesc\", \"$submitter\", curdate(), now()) ");
        
                // Print error message if there's a problem
                if (!$add_file) {
                    $activepage = "Projects";
                    require("header.php");
                    printf("<p>Error uploading file: %s</p>\n", mysql_error());
                    require("footer.php");
                    mysql_close($conn);
                    exit;
                }
                mysql_close($conn);
    
            }

        } else {
            
            // No filename specified; show error
            $activepage = "Projects";
            require("header.php");
            printf("<p>Error: No filename specified for upload.</p>\n");
            printf("<p>Back to <a href='projects.php?action=view&project=$project'>project page</a>.</p>");
            require("footer.php");
            exit;

        }

        // Return user to project details page
        header("Location: projects.php?action=view&project=$project");

        break;

    case "deletefile":

        // Delete file from filesystem
        $err = unlink("$dirtree/$existingfile");
        if (!$err) {
            $activepage = "Projects";
            require("header.php");
            printf("<p>Error deleting file: Could not be removed from filesystem.</p>\n");
            require("footer.php");
            exit;

        }

        // Delete file from files table
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db (DB_DATABASE);
        $delete_file = mysql_query("delete from files " .
            "where file_id = \"$file_id\"; ");

        // Print error message if there's a problem
        if (!$delete_file) {
            $activepage = "Projects";
            require("header.php");
            printf("<p>Error deleting file: %s</p>\n", mysql_error());
            require("footer.php");
            mysql_close($conn);
            exit;
        }
        mysql_close($conn);

        // Return user to project details page
        header("Location: projects.php?action=view&project=$project");

        break;
}

switch($action) {

    case "add":

        // Start HTML and declare Javascript functions
        $activepage = "Projects";
        require("header.php");

        // Page heading
        printf("<h2>Add New Project</h2>\n");

        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
        
            display_project_form($action, 0, "", "");

        } else {
            
            printf("<h3>Add New Project -- Not Authorized</h3>");
            printf("<p>Only staff members and administrators can add new projects.</p>\n");
            printf("<p>Contact $admin_name (<a href='mailto:$admin_email'>" .
                "$admin_email)</a> to enable your user account to add projects.</p>\n");
        }

        // End page
        require("footer.php");
        break;

    case "edit":

        // Start HTML and declare Javascript functions
        $activepage = "Projects";
        require("header.php");

        // Page heading
        printf("<h2>Edit Project</h2>\n");

        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
        
            display_project_form($action, $project, "", "");

        } else {
            
            printf("<h3>Edit Project -- Not Authorized</h3>");
            printf("<p>Only staff members and administrators can edit projects.</p>\n");
            printf("<p>Contact $admin_name (<a href='mailto:$admin_email'>" .
                "$admin_email)</a> to enable your user account to edit projects.</p>\n");
        }

        // End page
        require("footer.php");
        break;

    case "view":

        // Start HTML and declare Javascript functions
        $activepage = "Projects";
        require("header.php");

        // DEBUG
        //printf("<p>cmd = $cmd, action = $action</p>\n");

        display_project_details($project);

        // Only the assigned staff or administrators can edit this project, unless it's not assigned
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $get_user = mysql_query("select u.first_name, u.last_name from projects as p " .
            "left join users as u on p.staff_assigned = u.user_id " .
            "where p.project_id = \"$project\" ");
        if ($row = mysql_fetch_array($get_user)) {
            $displayed_user = "$row[0] $row[1]";
        } else {
            printf("Error: unable to retrieve user for this project from database.<br>\n");
        }
        mysql_free_result($get_user);
        mysql_close($conn);
        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($displayed_user == $_SESSION['SESSION_USER']) 
            || (trim($displayed_user) == "")) {
            $edit_priv = "Y";
        } else {
            $edit_priv = "N";
        }

        // Fill Javascript arrays with open projects and active duties
        fill_pdlist_arrays($staff, $projects, $project_ids, $duties, $duty_ids);
        
        // To-do items
        printf("<h2>To Do Items</h2>\n");
        if ($edit_priv == "Y") {
            printf("<div>\n<input type='text' id='txtNewItem' name='txtNewItem' " .
                "size='80%%' maxlength='255' onkeydown='handleItemKey(event, \"$staff\", \"$status\", \"$todostatus\", \"$project\", \"0\", \"0\", \"0\", \"$page\", \"$maxresults\");'>\n");
            printf("<input type='button' name='submit' value='Add item' " .
                "onclick='modify_todo(\"txtNewItem\", \"additem\", \"$staff\", \"$status\", \"$todostatus\", \"$project\", \"0\", \"0\", \"0\", \"$page\", \"$maxresults\");'>\n</div><br>\n");
        }
        printf("<div id='todo_section'>\n");
        //printf("%s", build_todolist($staff, $status, "Pending", $project, '0', '0', '0', $page, $maxresults));
        //printf("<br>\n");
        //printf("%s", build_todolist($staff, $status, "Completed", $project, '0', '0', '0', $page, $maxresults));
        printf(build_two_lists($staff, $status, $project, '0', '0', '0', $page, $maxresults));
        printf("</div>\n");

        // Comments
        printf("<h2>Comments</h2>\n");
        if ($edit_priv == "Y") {
            
            printf("<textarea name='txtNewEntry' id='txtNewEntry' rows='3' cols='100' class='description'></textarea><br>\n");
            printf("<input type='button' name='submit' value='Submit comment' " .
                "onclick='modify_comment(\"txtNewEntry\", \"addcomment\", \"$staff\", " .
                "\"$project\", \"0\", \"$fromdate\", \"$todate\", \"1\", \"$page\", " .
                "\"$maxresults\");'>\n<br>\n");
                
        }
    
        // Show existing comments
        $commentlist = build_commentlist($staff, $project, $duty, 
            $fromdate, $todate, 0, $page, $maxresults);
        printf($commentlist);
        //printf("%s", show_project_comments($project, $edit_priv));

        // Files
        printf("<a name='file_input'><h2>Files</h2></a>\n");
        if ($edit_priv == "Y") {
            printf("<form action='projects.php#file_input' method='post' enctype='multipart/form-data'>\n");
            printf("<table><tr>");
            printf("<td><label>Upload file: </label></td>\n");
            printf("<td><input type='file' name='filename' value='' size='50'></td></tr>\n");
            printf("<td><label>Category: </label></td>\n");
            printf("<td><select name='filecat'>\n");
            printf("<option value='Null' selected>\n");
            printf("<option value='Report'>Report\n");
            printf("<option value='Program'>Program\n");
            printf("<option value='Protocol'>Protocol\n");
            printf("</select></td></tr>\n");
            printf("<td colspan='2'><label>File description (optional): </label><br>\n");
            printf("<input type='text' name='filedesc' value='' size='60'>\n");
            printf("<input type='hidden' name='project' value='$project'></td></tr>\n");
            printf("<input type='hidden' name='submitter' value='%d'>\n", $_SESSION['SESSION_USERID']);
            printf("<input type='hidden' name='cmd' value='uploadfile'></td></tr>\n");
            printf("<tr><td colspan='2'><input type='submit' name='submit' value='Upload file'></td></tr>\n");
            printf("</table></form>\n");
        }

        // Show uploaded files
        show_project_files($project);

        printf("<br><br>\n");

        // Make to-do list sortable
        if ($edit_priv == "Y") {
            printf("<script type='text/javascript'>\n");
            //printf("Sortable.create('pendingTasksList', {tag:'div',handle:'draghandle'});\n");
            printf("Sortable.create('Pending_todolist', {tag:'div',handle:'draghandle'});\n");
        
            // Put focus on to-do box
            printf("document.getElementById('txtNewItem').value = '';\n");
            printf("document.getElementById('txtNewItem').focus();\n");
            printf("</script>\n");
        }

        // End page
        require("footer.php");
        break;

    default: // Default action: list projects

        // Start HTML and declare Javascript functions
        $activepage = "Projects";
        require("header.php");

        // Page heading
        printf("<h2>Project List</h2>\n");

        // Button: Add New Project
        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
            printf("<form action='projects.php' method='post'>\n");
            printf("<input type='hidden' name='action' value='add'><input type='submit' value='Add New Project'></form>\n");
        }
        
        // Show main controls
        $fields = array('Staff', 'Pstatus', 'Results');
        $values = array($staff, $status, $maxresults);
        printf("<form action='projects.php' method='get' name='viewform'>\n");
        $displayed_user = display_controls($fields, $values);
        printf("</form>\n");

        // Only the assigned staff or administrators can edit this project, unless it's not assigned
        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($displayed_user == $_SESSION['SESSION_USER'])) {
//            || (trim($displayed_user) == "")) {
            $edit_priv = "Y";
        } else {
            $edit_priv = "N";
        }

        // Define visibility clause
        if ($staff == $_SESSION['SESSION_USERID']) {
            $visclause = "";
        } else {
            $visclause = "and p.visibility = 'Public' ";
        }
        
        if ($staff == 0) {
        
            if ($status == "All") {
                
                $query = "select p.project_id, p.title, p.description, " .
                    "c.last_name as clientname, u.first_name, u.last_name, p.date_entered, p.status, p.visibility, " .
                    "p.icon_id from projects as p " .
                    "left join clients as c on p.client_id = c.client_id " .
                    "left join users as u on p.staff_assigned = u.user_id " .
                    "where p.visibility = 'Public' " .
                    "order by p.date_entered desc ";

            } else if ($status == "Open") {

                $query = "select p.project_id, p.title, p.description, " .
                    "c.last_name as clientname, u.first_name, u.last_name, p.date_entered, p.status, p.visibility, " .
                    "p.icon_id from projects as p " .
                    "left join clients as c on p.client_id = c.client_id " .
                    "left join users as u on p.staff_assigned = u.user_id " .
                    "where p.status in (\"Pending\", \"Active\", \"Suspended\") " . 
                    $visclause . "order by p.date_entered desc ";

            } else {

                $query = "select p.project_id, p.title, p.description, " .
                    "c.last_name as clientname, u.first_name, u.last_name, p.date_entered, p.status, p.visibility, " .
                    "p.icon_id from projects as p " .
                    "left join clients as c on p.client_id = c.client_id " .
                    "left join users as u on p.staff_assigned = u.user_id " .
                    "where p.status = \"$status\" " . 
                    $visclause . "order by p.date_entered desc ";
            }
        
        } else {
        
            if ($status == "All") {
            
                $query = "select p.project_id, p.title, p.description, " .
                    "c.last_name as clientname, u.first_name, u.last_name, p.date_entered, p.status, p.visibility, " .
                    "p.icon_id from projects as p " .
                    "left join clients as c on p.client_id = c.client_id " .
                    "left join users as u on p.staff_assigned = u.user_id " .
                    "where p.staff_assigned = $staff " . 
                    $visclause . "order by p.date_entered desc ";

            } else if ($status == "Open") {

                $query = "select p.project_id, p.title, p.description, " .
                    "c.last_name as clientname, u.first_name, u.last_name, p.date_entered, p.status, p.visibility, " .
                    "p.icon_id from projects as p " .
                    "left join clients as c on p.client_id = c.client_id " .
                    "left join users as u on p.staff_assigned = u.user_id " .
                    "where p.status in (\"Pending\", \"Active\", \"Suspended\") " . 
                    "and p.staff_assigned = $staff " .
                    $visclause . "order by p.date_entered desc ";

            } else {
            
                $query = "select p.project_id, p.title, p.description, " .
                    "c.last_name as clientname, u.first_name, u.last_name, p.date_entered, p.status, p.visibility, " .
                    "p.icon_id from projects as p " .
                    "left join clients as c on p.client_id = c.client_id " .
                    "left join users as u on p.staff_assigned = u.user_id " .
                    "where p.status = \"$status\" and p.staff_assigned = $staff " . 
                    $visclause . "order by p.date_entered desc ";
                    
            }
            
        }
        
        // Retrieve projects from database
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db (DB_DATABASE);
        $get_projects_full = mysql_query($query);
        //printf("Query = $query<br>\n");
        $get_projects = mysql_query($query . "limit " . (($page - 1) * $maxresults) .
            ", $maxresults ");    
        if (mysql_num_rows($get_projects_full) == 0) {
            if ($displayed_user == "All") {
                if ($status == "All") {
                    printf("<b>No projects found in database.</b><br>\n");
                } else {
                    printf("<b>No " . strtolower($status) . " projects found in database.</b><br>\n");
                }
            } else {
                if ($status == "All") {
                    printf("<b>No projects found for $displayed_user.</b><br>\n");
                } else {
                    printf("<b>No " . strtolower($status) . " projects found for $displayed_user.</b><br>\n");
                }
            }
        } else {
        
            // Show page numbers for results
            $page_url = "projects.php?staff=$staff&status=$status&todostatus=" .
                "$todostatus";
            printf("%s\n", page_results(mysql_num_rows($get_projects_full), 
                $page, $maxresults, $page_url));

            printf("<table cellpadding='5' class='project'>");
            printf("<tr><td><br></th>\n");
            printf("<th>Project</th>\n");
            printf("<th>Description</th>\n");
            if ($displayed_user == "All") {
                printf("<th>Staff</th>\n");
            }
            //printf("<th>Requested</th>\n");
            printf("<th>Status</th>\n");
            printf("<th>Entered</th></tr>\n");
            $rowcount = 0;
            while ($row = mysql_fetch_array($get_projects, MYSQL_ASSOC))
            {
                printf("<tr valign='top'>");

                // Icon
                printf("<td><img src='icons18/icon_%d.png'></td>\n", $row['icon_id']);

                // Project title
                if ($row['visibility'] == "Private") {
                    printf("<td style='background-color: $heading_color'><a href='projects.php?action=view&project=%d'>%s</a></td>\n", 
                        $row['project_id'], $row['title']);
                } else {
                    printf("<td><a href='projects.php?action=view&project=%d'>%s</a></td>\n", 
                        $row['project_id'], $row['title']);
                }

                // Description
                printf("<td>%s", $row['description']);
                if ($row['clientname']) {
                    printf(" (<i>%s</i>)", $row['clientname']);
                }
                printf("</td>\n");

                // Staff (only if all users are being displayed)
                if ($displayed_user == "All") {
                    printf("<td align='center'>%s</td>\n", substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1));
                }

                // Status
                printf("<td align='center'>\n");
                if ($edit_priv == "Y") {
                    printf("<form action='projects.php' method='post' name='changeStatus_%d'>\n", $row['project_id']);
                    printf("<select name='newstatus' onChange='changeStatus_%d.submit()' " .
                        "size='1'>\n", $row['project_id']);
                    if ($row['status'] == "Proposed") {
                        printf("<option value='Proposed' selected>Proposed\n");
                    } else {
                        printf("<option value='Proposed'>Proposed\n");
                    }
                    if ($row['status'] == "Pending") {
                        printf("<option value='Pending' selected>Pending\n");
                    } else {
                        printf("<option value='Pending'>Pending\n");
                    }
                    if ($row['status'] == "Active") {
                        printf("<option value='Active' selected>Active\n");
                    } else {
                        printf("<option value='Active'>Active\n");
                    }
                    if ($row['status'] == "Suspended") {
                        printf("<option value='Suspended' selected>Suspended\n");
                    } else {
                        printf("<option value='Suspended'>Suspended\n");
                    }
                    if ($row['status'] == "Aborted") {
                        printf("<option value='Aborted' selected>Aborted\n");
                    } else {
                        printf("<option value='Aborted'>Aborted\n");
                    }
                    if ($row['status'] == "Completed") {
                        printf("<option value='Completed' selected>Completed\n");
                    } else {
                        printf("<option value='Completed'>Completed\n");
                    }
                    printf("</select>\n");
                    printf("<input type='hidden' name='project' value='%d'>\n", $row['project_id']);
                    printf("<input type='hidden' name='cmd' value='change_status'>\n");
                    printf("<input type='hidden' name='status' value='%s'>\n", $status);
                    printf("<input type='hidden' name='staff' value='%s'>\n", $staff);
                    printf("<input type='hidden' name='page' value='%s'>\n", $page);
                    printf("<input type='hidden' name='maxresults' value='%s'>\n", $maxresults);
                    printf("</form>");
                } else {
                    printf("%s", $row['status']);
                }
                printf("</td>\n");

                // Date entered
                if (($row['date_entered']) && ($row['date_entered'] != "0000-00-00") && ($row['date_entered'] != "NULL")) {
                    printf("<td>%s</td>\n", shorter_date($row['date_entered']));
                } else {
                    printf("<td><br></td>\n");
                }

                printf("</tr>\n");
                
            }
            printf("</table>");
        }

        mysql_free_result($get_projects);
        mysql_free_result($get_projects_full);
        mysql_close($conn);
        
        // End page
        require("footer.php");
        break;
}

?>

