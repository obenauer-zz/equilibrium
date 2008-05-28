<?php
require("check_login.php");
require("config.php");

// Check for passed arguments
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = "";
}

if (isset($_REQUEST['cmd'])) {
    $cmd = $_REQUEST['cmd'];
} else {
    $cmd = "";
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

if ((isset($_REQUEST['project'])) && (is_numeric($_REQUEST['project']))) {
    $project = $_REQUEST['project'];
} else {
    $project = 0;
}

if ((isset($_REQUEST['duty'])) && (is_numeric($_REQUEST['duty']))) {
    $duty = $_REQUEST['duty'];
} else {
    $duty = 0;
}

if (isset($_REQUEST['page'])) {
    $page = $_REQUEST['page'];
} else {
    $page = 1;
}

if (isset($_REQUEST['pageflag'])) {
    $pageflag = $_REQUEST['pageflag'];
} else {
    $pageflag = 1;
}

if (isset($_REQUEST['fromdate'])) {
    $fromdate = $_REQUEST['fromdate'];
} else {
    // Default: one month ago
    $prevmonth = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
    $fromdate = date("Y", $prevmonth) . "-" . date("m", $prevmonth) . "-" .    
        date("d", $prevmonth);
}

if (isset($_REQUEST['todate'])) {
    $todate = $_REQUEST['todate'];
} else {
    // Default: today's date
    $todate = date('Y') . "-" . date('m') . "-" . date('d');
}

if (isset($_REQUEST['maxresults'])) {
    $maxresults = $_REQUEST['maxresults'];
    if ($maxresults == 0) {
        $maxresults = 20;
    }
} else {
    $maxresults = 20;
}

// Declare PHP functions
require("equilibrium.php");

// Commands that don't generate HTML output
switch($cmd) {
    case "";
        break;
    case "";
        break;
    case "";
        break;
    case "";
        break;
}

// Start HTML and declare Javascript functions
$activepage = "Log";
require("header.php");

// Main functions of page
switch($action) {
    case "";

        printf("<h2>Log Book</h2>\n");
        
        // Button: Add New Entry
        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
            printf("<table><tr>\n");
            printf("<td>\n");
            printf("<input type='button' name='add_entry_button' id='add_entry_button'></td>\n");
            printf("</tr></table>\n");
        
            // Javascript for add new entry button
            printf("<script type='text/javascript'>\n");
            printf("set_add_entry_button();");
            printf("</script>\n");

            // Add new entry form
            printf("<div id='add_entry_form' class='addbox'>\n");
            printf("<table cellpadding='0' cellspacing='0'><tr valign='top'><td>Add new entry<br>\n");
            printf("<textarea id='txtNewEntry' name='txtNewEntry' " .
            "rows='3' cols='80' class='description'></textarea></td>\n");
//             printf("<textarea name='comment' id='txtNewEntry' rows='3' cols='80'></textarea><br>\n");
    
            // Project/duty selection
            printf("<td>");
            fill_pdlist_arrays($staff, $projects, $project_ids, $duties, $duty_ids);
            printf("%s", make_pdlist($staff, $row['comment_id'], $row['project_id'], 
                $projects, $project_ids, $row['duty_id'], $duties, $duty_ids, 1));
            printf("</td>\n");
            printf("</td>\n");
            printf("<td valign='top'> &nbsp; <br> &nbsp; <input type='button' value='Add entry' onclick='modify_comment(\"txtNewEntry\", \"addcomment\", \"$staff\", \"0\", \"0\", \"$fromdate\", \"$todate\", \"1\", \"$page\", \"$maxresults\");'></td></tr></table><font size='1'><br></font>\n");
            printf("</div>\n");

        }

        // Show main controls
        $fields = array('Staff', 'FromDate', 'ToDate', 'Results');
        $values = array($staff, $fromdate, $todate, $maxresults);
        printf("<form action='logbook.php' method='get' name='viewform'>\n");
        $displayed_user = display_controls($fields, $values);
        printf("</form>\n");

        // Only allow authorized people to sort or mark off to-do items
        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($displayed_user == $_SESSION['SESSION_USER'])) {
            //|| (trim($displayed_user) == "")) {
            $edit_priv = "Y";
        } else {
            $edit_priv = "N";
        }

        $commentlist = build_commentlist($staff, $project, $duty, 
            $fromdate, $todate, $pageflag, $page, $maxresults);
        printf($commentlist);

        //show_project_comments(0, $edit_priv);

        break;
    case "";
        break;
    case "";
        break;
    case "";
        break;
}

// End page
require("footer.php");
?>

