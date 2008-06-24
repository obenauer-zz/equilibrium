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

if (isset($_REQUEST['edit_priv'])) {
    $edit_priv = $_REQUEST['edit_priv'];
} else {
    $edit_priv = "N";
}

if (isset($_REQUEST['content'])) {
    $content = $_REQUEST['content'];
} else {
    $content = "";
}

if (isset($_GET['filetype'])) {
    $filetype = $_GET['filetype'];
} else {
    $filetype = "Report";
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

if (isset($_REQUEST['maxresults'])) {
    $maxresults = $_REQUEST['maxresults'];
    if ($maxresults == 0) {
        $maxresults = 20;
    }
} else {
    $maxresults = 20;
}

if (isset($_REQUEST['pdflag'])) {
    $pdflag = $_REQUEST['pdflag'];
} else {
    $pdflag = "";
}

if (isset($_REQUEST['pdchange'])) {
    $pdchange = $_REQUEST['pdchange'];
} else {
    $pdchange = 0;
}

if (isset($_REQUEST['staffchange'])) {
    $staffchange = $_REQUEST['staffchange'];
} else {
    $staffchange = 0;
}

if (isset($_REQUEST['visibility'])) {
    $visibility = $_REQUEST['visibility'];
} else {
    $visibility = "Public";
}

if (isset($_REQUEST['fromdate'])) {
    $fromdate = $_REQUEST['fromdate'];
} else {
    // Default: one year ago
    $prevmonth = mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1);
    $fromdate = date("Y", $prevmonth) . "-" . date("m", $prevmonth) . "-" .    
        date("d", $prevmonth);
}

if (isset($_REQUEST['todate'])) {
    $todate = $_REQUEST['todate'];
} else {
    // Default: today's date
    $todate = date('Y') . "-" . date('m') . "-" . date('d');
}

// Declare PHP functions
require("equilibrium.php");

function show_files($staff, $filetype, $fromdate, $todate, $pageflag, 
    $page, $maxresults) {

    global $heading_color;
    
    // Define filetype clause
    if (($filetype == "") || ($filetype == "NULL")) {
        $fileclause = "and file_type is null ";
    } else {
        $fileclause = "and file_type = \"$filetype\" ";
    }

    // Define staff clause
    if ($staff == 0) {
        $staffclause = "";
    } else {
        $staffclause = "and uploaded_by = \"$staff\" ";
    }

    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db(DB_DATABASE);
    $query = "select file_id, file_type, file_name, description, " .
        "upload_date, upload_time, file_path from files " .
        "where upload_date >= \"$fromdate\" " .
        "and upload_date <= \"$todate\" " .
        $staffclause .
        $fileclause .
        "order by upload_time desc ";
    $list_full = mysql_query($query);

    // Allow paging option
    if ($pageflag) {
        $list_page = mysql_query($query . "limit " . (($page - 1) * $maxresults) .
            ", $maxresults ");
        $page_url = "files.php?staff=$staff&filetype=$filetype&fromdate=$fromdate" .
            "&todate=$todate";
        printf("%s", page_results(mysql_num_rows($list_page), $page, $maxresults,
            $page_url));
    } else {
        $list_page = $list_full;
    }

    if (mysql_num_rows($list_page)) {
        while ($row = mysql_fetch_array($list_page, MYSQL_ASSOC)) {
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
            printf("<form action='duties.php#file_input' method='post'>\n");
            printf("<input type='hidden' name='file_id' value='%d'>\n", 
                $row['file_id']);
            printf("<input type='hidden' name='dirtree' value='%s'>\n", 
                $row['file_path']);
            printf("<input type='hidden' name='existingfile' value='%s'>\n", 
                $row['file_name']);
            printf("<input type='hidden' name='cmd' value='deletefile'>\n");
            printf("<input type='hidden' name='duty' value='$duty'>\n");
            printf("<input type='submit' value='Confirm Deletion'>  &nbsp; &nbsp; ");
            printf("<input type='button' value='Cancel' onclick='hide_item(\"confirm_" . 
                $row['file_id'] . "\");'></center><br>");
            printf("</form>\n</div>\n");

        }

    } else {
        printf("<p>No files found for the selected criteria.</p>\n");
    }

    mysql_free_result($list_page);
    @mysql_free_result($list_full);
    mysql_close($conn);
    
    return;
}

// Commands that don't generate HTML output
switch($cmd) {
    case "":
        break;
    case "":
        break;
    case "":
        break;
    case "":
        break;
}

// Start HTML and declare Javascript functions
$activepage = "Files";
require("header.php");

// Main functions of page
switch($action) {
    //case "":
    //    printf("Aloha!<br>\n");

    //    break;
    //case "":
    //    break;
    //case "":
    //    break;
    default;
        printf("<h2>Files</h2>\n");
        
        // Button: Add New File
//         if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
//             printf("<table><tr>\n");
//             printf("<td>\n");
//             printf("<input type='button' name='add_file_button' id='add_file_button'></td>\n");
//             printf("</tr></table>\n");
//         
//             // Javascript for add new file button
//             printf("<script type='text/javascript'>\n");
//             printf("set_add_file_button();");
//             printf("</script>\n");
// 
//             // Add new file form
//             printf("<div id='add_file_form' class='addbox'>\n");
//             printf("<table cellpadding='0' cellspacing='0'><tr valign='top'><td>Add new file<br>\n");
//             printf("<textarea id='txtNewFile' name='txtNewFile' " .
//             "rows='3' cols='80' class='description'></textarea></td>\n");
// //             printf("<textarea name='comment' id='txtNewEntry' rows='3' cols='80'></textarea><br>\n");
//     
//             // Project/duty selection
//             printf("<td>");
//             fill_pdlist_arrays($staff, $projects, $project_ids, $duties, $duty_ids);
//             printf("%s", make_pdlist($staff, $row['comment_id'], $row['project_id'], 
//                 $projects, $project_ids, $row['duty_id'], $duties, $duty_ids, 1));
//             printf("</td>\n");
//             printf("</td>\n");
//             printf("<td valign='top'> &nbsp; <br> &nbsp; <input type='button' value='Add entry' onclick='modify_comment(\"txtNewEntry\", \"addcomment\", \"$staff\", \"0\", \"0\", \"$fromdate\", \"$todate\", \"1\", \"$page\", \"$maxresults\");'></td></tr></table><font size='1'><br></font>\n");
//             printf("</div>\n");
// 
//         }

        // Show main controls
        $fields = array('Staff', 'Ftype', 'FromDate', 'ToDate', 'Results');
        $values = array($staff, $filetype, $fromdate, $todate, $maxresults);
        printf("<form action='files.php' method='get' name='viewform'>\n");
        $displayed_user = display_controls($fields, $values);
        printf("</form>\n");

        // Only allow authorized people to sort or mark off to-do items
        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($displayed_user == $_SESSION['SESSION_USER'])) {
            //|| (trim($displayed_user) == "")) {
            $edit_priv = "Y";
        } else {
            $edit_priv = "N";
        }

        $filelist = show_files($staff, $filetype, 
            $fromdate, $todate, $pageflag, $page, $maxresults);
        printf($filelist);

        break;
}

// End page
require("footer.php");
?>

