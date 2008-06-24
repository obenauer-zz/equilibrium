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

if (isset($_REQUEST['status'])) {
    $status = $_REQUEST['status'];
} else {
    $status = "Active";
}

if (isset($_REQUEST['todostatus'])) {
    $todostatus = $_REQUEST['todostatus'];
} else {
    $todostatus = "Pending";
}

if (isset($_REQUEST['priority'])) {
    $priority = $_REQUEST['priority'];
} else {
    $priority = "High";
}

if (isset($_REQUEST['content'])) {
    $content = $_REQUEST['content'];
} else {
    $content = "";
}

if (isset($_REQUEST['order'])) {
    $order = $_REQUEST['order'];
} else {
    $order = "";
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

if (isset($_REQUEST['page'])) {
    $page = $_REQUEST['page'];
} else {
    $page = 1;
}

if (isset($_REQUEST['maxresults'])) {
    $maxresults = $_REQUEST['maxresults'];
    if ($maxresults == 0) {
        $maxresults = 20;
    }
} else {
    $maxresults = 20;
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

// Declare PHP functions
require("equilibrium.php");

// Commands that don't generate HTML output
switch($cmd) {
    case "updatelist";
        if ($order) {
            $todo_order = explode(",", $order);

            // Re-order to-do items in response to drag and drop
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
            $errorflag = 0;
            for ($i = 0; $i < count($todo_order); $i++) {
                
                $query = "update todos set order_number = \"" . ($i + 1) . "\" " .
                    "where todo_id = \"" . $todo_order[$i] . "\" ";
                $update = $conn->query($query);
                if ($update != 1) {
                    $errorflag = 1;
                }
            }
            // Close the database connection
            $conn->close();

            $new_todolist = build_todolist($staff, $status, $todostatus, $edit_priv, 
                0, 0, 1, $page, $maxresults);
            //printf($errorflag);
            printf($new_todolist);
            exit;
        } else {
            require("header.php");
            printf("<p>Error updating to-do list: Order is not specified.</p>\n");
            require("footer.php");
            exit;
        }

        break;
    case "toggleComplete";

        // Check whether task is already marked completed
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        $query = "SELECT completed FROM todos " .
            "where todo_id = '" . $content . "'";
        $get_status = $conn->query($query);
        $row = $get_status->fetch_assoc();
        if ($row['completed'] == "Y") {
        
            // mark task NOT completed
            $result = $conn->query('UPDATE todos set completed = "N" WHERE todo_id="' . 
                                            $content . '"');
        } else {
        
            // mark task completed
            $result = $conn->query('UPDATE todos set completed = "Y", completed_date = curdate() WHERE todo_id="' . 
                                            $content . '"');
        }          

        // Close the database connection
        $conn->close();
        //exit;
        header("Location: todolist.php?status=$status&staff=$staff&todostatus=$todostatus&maxresults=$maxresults&page=$page");
        
        break;
    case "";
        break;
    case "";
        break;
}

// Start HTML and declare Javascript functions
$activepage = "ToDo";
require("header.php");

?>
<?php
switch($action) {
    case "";
        printf("<h2>To Do List</h2>\n");
        
        // Button: Add New Item
        if (($_SESSION['SESSION_ADMIN'] == "Y") || ($_SESSION['SESSION_STAFF'] == "Y")) {
            printf("<table><tr>\n");
            printf("<td>\n");
            printf("<input type='button' name='add_item_button' id='add_item_button'></td>\n");
            
            // Button: Assign To Projects
            //printf("<td><form action='todolist.php' method='post'>\n");
            //printf("<input type='hidden' name='action' value='edit'>\n");
            //printf("<input type='submit' value='Assign To Projects'></form></td>\n");
            printf("</tr></table>\n");
        
            // Javascript for add new item button
            printf("<script type='text/javascript'>\n");
            printf("set_add_item_button();");
            printf("</script>\n");

            // Add new item form
            printf("<div id='add_item_form' class='addbox'>\n");
            printf("<table cellpadding='0' cellspacing='0'><tr valign='bottom'><td>Add new item<br>\n");
            printf("<input type='text' id='txtNewItem' name='txtNewItem' " .
            "size='80%%' maxlength='255' onkeydown='handleItemKey(event, \"$staff\", \"$status\", \"$todostatus\", \"0\", \"0\", \"1\", \"0\", \"$page\", \"$maxresults\");'></td>\n");
    
            // Project/duty selection
            printf("<td>");
            fill_pdlist_arrays($staff, $projects, $project_ids, $duties, $duty_ids);
            printf("%s", make_pdlist($staff, $row['comment_id'], $row['project_id'], 
                $projects, $project_ids, $row['duty_id'], $duties, $duty_ids, 1));
            printf("</td>\n");
            printf("</td>\n");
            printf("<td valign='bottom'> &nbsp; <input type='button' value='Add item' onclick='modify_todo(\"txtNewItem\", \"additem\", \"$staff\", \"$status\", \"$todostatus\", \"$priority\", \"0\", \"0\", \"1\", \"0\", \"$page\", \"$maxresults\");'></td></tr></table><font size='1'><br></font>\n");
            printf("</div>\n");

        }

        // Show main controls
        $fields = array('Staff', 'Pstatus', 'Tstatus', 'Priority', 'Results');
        $values = array($staff, $status, $todostatus, $priority, $maxresults);
        printf("<form action='todolist.php' method='get' name='viewform'>\n");
        $displayed_user = display_controls($fields, $values);
        printf("</form>\n");
        
        //showToDoList($staff, $status, $todostatus, $edit_priv);
        $todolist = build_todolist($staff, $status, $todostatus, $priority, 0, 0, 1, 0, 
            $page, $maxresults);
        printf($todolist);

        // Set variable for project/duty flag
        printf("<script type='text/javascript'>\n");
        printf("var pdflag = \"Project\";\n");
        printf("var project = 0;\n");
        printf("var duty = 0;\n");
        printf("populate_projects( $(\"pdlist\"), $(\"projectlabel\"), $(\"dutylabel\"), project_ids, projects, \"0\");\n");
        //printf("var pdlist = $(\"pdlist\");\n");
        //printf("pdlist.options[0].text = 'None';\n");
        //printf("pdlist.options[3].selected = true;\n");
        printf("</script>\n");

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

