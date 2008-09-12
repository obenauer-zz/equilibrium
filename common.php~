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
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = "";
}

if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];
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

if (isset($_REQUEST['status'])) {
    $status = $_REQUEST['status'];
} else {
    $status = "Active";
}

if (isset($_REQUEST['todostatus'])) {
    $todostatus = $_REQUEST['todostatus'];
} else {
    $todostatus = "Completed";
}

if (isset($_REQUEST['priority'])) {
    $priority = $_REQUEST['priority'];
} else {
    $priority = "High";
}

if (isset($_REQUEST['calmode'])) {
    $calmode = $_REQUEST['calmode'];
} else {
    $calmode = 0;
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

if (isset($_REQUEST['order'])) {
    $order = $_REQUEST['order'];
} else {
    $order = "";
}

if (isset($_REQUEST['page'])) {
    $page = $_REQUEST['page'];
} else {
    $page = 1;
}

if (isset($_REQUEST['pageflag'])) {
    $pageflag = $_REQUEST['pageflag'];
} else {
    $pageflag = 0;
}

if (isset($_REQUEST['dragonly'])) {
    $dragonly = $_REQUEST['dragonly'];
} else {
    $dragonly = 0;
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

if (isset($_REQUEST['newtext'])) {
    $newtext = $_REQUEST['newtext'];
} else {
    $newtext = 0;
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

if (isset($_REQUEST['scheduledate'])) {
    $scheduledate = $_REQUEST['scheduledate'];
} else {
    $scheduledate = "";
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

// Declare PHP functions
require("equilibrium.php");

// Commands that don't generate HTML output
switch($cmd) {
    case "updatelist";
        if ($order) {
            $todo_order = explode(",", $order);
            
            if ($project) {
                $order_var = "project_order";
            } else if ($duty) {
                $order_var = "duty_order";
            } else {
                $order_var = "order_number";
            }
            
            if ($calmode) {

                // Remove alphabetic div's from to-do order
                $modlist = array();
                $modcount = 0;
                for ($i = 0; $i < count($todo_order); $i++) {
                    if (is_numeric($todo_order[$i])) {
                        $modlist[$modcount] = $todo_order[$i];
                        $modcount++;
                    }
                }
                $numeric_todos = implode(",", $modlist);

                // Retrieve existing order of to-do's
                $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
                    or die ("Cannot connect to database. " . mysql_error() . "\n<br>\n");
                mysql_select_db(DB_DATABASE);
                $get_orig_order = mysql_query("select todo_id from todos " .
                    "where todo_id in ($numeric_todos) order by $order_var asc ");
                $oldorder = array();
                $oldcount = 0;
                while ($row = mysql_fetch_array($get_orig_order, MYSQL_ASSOC)) {
                    $oldorder[$oldcount] = $row['todo_id'];
                    $oldcount++;
                }
                mysql_free_result($get_orig_order);

                // Get schedule dates of to-do's near the moved one
                $mover = 0;
                $datepartner = 0;
                find_mover($oldorder, $todo_order, $mover, $datepartner);

                // Re-assign scheduled date of moved to-do item
                $get_copy_date = mysql_query("select schedule_date from todos " .
                    "where todo_id = \"" . $datepartner . "\" ");
                if ($row = mysql_fetch_array($get_copy_date, MYSQL_ASSOC)) {
                    $copied_date = $row['schedule_date'];
                }
                mysql_free_result($get_copy_date);
                if (($copied_date == "0000-00-00") || ($copied_date == "")) {
                    $copied_date = "NULL";
                } else {
                    // Don't allow scheduling a to-do to a past date
                    $parts = explode("-", $copied_date);
                    $calday = mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]);
                    $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                    if ($calday < $today) {
                        $copied_date = "NULL";
                    }
                }
                $query = "update todos set schedule_date = \"" . $copied_date . "\" " .
                    "where todo_id = \"" . $mover . "\" ";
                $update = mysql_query($query);
                if ($update != 1) {
                    $errorflag = 1;
                }
            }

            // Re-order to-do items in response to drag and drop
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
            $errorflag = 0;
            $offset = ($page - 1) * $maxresults;
            for ($i = 0; $i < count($todo_order); $i++) {
                
                $query = "update todos set $order_var = \"" . ($offset + $i + 1) . "\" " .
                    "where todo_id = \"" . $todo_order[$i] . "\" ";
                //printf("query = $query (page = $page)<br>\n");
                $update = $conn->query($query);
                if ($update != 1) {
                    $errorflag = 1;
                }
            }
            // Close the database connection
            $conn->close();

            $new_todolist = build_todolist($staff, $status, $todostatus, $priority, 
                $calmode, $project, $duty, $pageflag, $dragonly, $page, $maxresults);
            printf($new_todolist);
            exit;
        } else {
            printf("<p>Error updating to-do list: Order is not specified.</p>\n");
            require("footer.php");
            exit;
        }

        break;

    // Delete to-do item
    case "deleteitem";

        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $content = trim(mysql_real_escape_string($content));
        $result = mysql_query("DELETE FROM todos WHERE todo_id = '$content'");
        mysql_close($conn);
        $new_todolist = build_todolist($staff, $status, $todostatus, $priority, 
            $calmode, $project, $duty, $pageflag, $dragonly, $page, $maxresults);
        printf($new_todolist);
        exit;
        break;

    case "edititem";

        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        //$newtext = trim(mysql_real_escape_string($newtext));
        if (($scheduledate) && ($scheduledate != "NULL") && ($scheduledate != "000-00-00")) {
            $scheduleclause = "schedule_date = \"$scheduledate\", ";
        } else {
            $scheduleclause = "schedule_date = NULL, ";
        }
        if ($pdflag == "Project") {
            $edit_item = mysql_query("UPDATE todos set description = \"" . $newtext . 
                "\", project_id = \"$pdchange\", duty_id = \"0\", " .
                "staff_assigned = \"$staffchange\", $scheduleclause " . 
                "visibility = \"$visibility\" WHERE todo_id = '$content' ");
        
        } else if ($pdflag == "Duty") {
            $edit_item = mysql_query("UPDATE todos set description = \"" . $newtext . 
                "\", duty_id = \"$pdchange\", project_id = \"0\", " .
                "staff_assigned = \"$staffchange\", $scheduleclause " . 
                "visibility = \"$visibility\" WHERE todo_id = '$content' ");
                
        } else {
            $edit_item = mysql_query("UPDATE todos set description = \"" . $newtext . 
                "\", staff_assigned = \"$staffchange\", $scheduleclause " . 
                "visibility = \"$visibility\" WHERE todo_id = '$content' ");
        }
        mysql_close($conn);

        $new_todolist = build_todolist($staff, $status, $todostatus, $priority, 
            $calmode, $project, $duty, $pageflag, $dragonly, $page, $maxresults);
        //$new_todolist .= "UPDATE todos set description = \"" . $newtext . 
        //        "\", duty_id = \"$pdchange\", project_id = \"0\", " .
        //        "staff_assigned = \"$staffchange\", " . 
        //        "visibility = \"$visibility\" WHERE todo_id = '$content' ";
        printf($new_todolist);
        exit;

        break;

    case "additem";

        // Add new item
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $newtext = trim(mysql_real_escape_string($content));
        if ($newtext) {
            if ($pdflag == "Project") {
                
                // Move existing to-do items up in order
                $start_trans = mysql_query("start transaction ");
                $get_order = mysql_query("SELECT todo_id from todos " .
                    "where project_id = \"$pdchange\" " .
                    "and completed = 'N' " .
                    "order by project_order asc ");
                $count = 2;
                while ($row = mysql_fetch_array($get_order, MYSQL_ASSOC)) {
                    $set_order = mysql_query("update todos set project_order = \"$count\" " .
                        "where todo_id = \"" . $row['todo_id'] . "\" ");
                    $count++;
                }
                mysql_free_result($get_order);
                    
                // Assign this to-do the same visibility as its project
                $get_vis = mysql_query("SELECT visibility from projects " .
                    "where project_id = \"$pdchange\" ");
                if ($row = mysql_fetch_array($get_vis, MYSQL_ASSOC)) {
                    $visibility = $row['visibility'];
                } else {
                    $visibility = 'Public';
                }
                mysql_free_result($get_vis);
                
                // Add new item at top of list
                $add_item = mysql_query("INSERT into todos (description, " .
                    "project_id, duty_id, staff_assigned, project_order, " .
                    "visibility) " .
                    "values (\"$newtext\", \"$pdchange\", \"0\", \"$staff\", " .
                    "\"1\", \"$visibility\" ) ");
                $stop_trans = mysql_query("commit ");
                
            } else if ($pdflag == "Duty") {
                
                // Move existing to-do items up in order
                $start_trans = mysql_query("start transaction ");
                $get_order = mysql_query("SELECT todo_id from todos " .
                    "where duty_id = \"$pdchange\" " .
                    "and completed = 'N' " .
                    "order by duty_order asc ");
                $count = 2;
                while ($row = mysql_fetch_array($get_order, MYSQL_ASSOC)) {
                    $set_order = mysql_query("update todos set duty_order = \"$count\" " .
                        "where todo_id = \"" . $row['todo_id'] . "\" ");
                    $count++;
                }
                mysql_free_result($get_order);
                
                // Assign this to-do the same visibility as its duty
                $get_vis = mysql_query("SELECT visibility from duties " .
                    "where duty_id = \"$pdchange\" ");
                if ($row = mysql_fetch_array($get_vis, MYSQL_ASSOC)) {
                    $visibility = $row['visibility'];
                } else {
                    $visibility = 'Public';
                }
                mysql_free_result($get_vis);
                
                // Add new item at top of list
                $add_item = mysql_query("INSERT into todos (description, " .
                    "project_id, duty_id, staff_assigned, duty_order, " .
                    "visibility) " .
                    "values (\"$newtext\", \"0\", \"$pdchange\", \"$staff\", " .
                    "\"1\", \"$visibility\" ) ");
                $stop_trans = mysql_query("commit ");
            
            } else {
                
                // Query conditions for To Do page
                if ($status == "All") {
                    $statusclause = "";
                } else if ($status == "Open") {
                    $statusclause = "and ((p.status in ('Pending', 'Active', 'Suspended') " .
                        "or t.project_id = 0) " .
                        "and (d.status = \"Active\"  or t.duty_id = 0)) ";
                } else {
                    $statusclause = "and ((p.status = \"$status\" " .
                        "or t.project_id = 0) " .
                        "and (d.status = \"Active\"  or t.duty_id = 0)) ";
                }
                
                // Only let people add items as themselves, but can reassign later
                $staffclause = "and t.staff_assigned = \"" . 
                    $_SESSION['SESSION_USERID'] . "\" ";
                
                // Move existing to-do items up in order
                $start_trans = mysql_query("start transaction ");
                $get_order = mysql_query("SELECT t.todo_id from todos as t " .
                    "left join projects as p on t.project_id = p.project_id " .
                    "left join duties as d on t.duty_id = d.duty_id " .
                    "where project_id = \"$pdchange\" " .
                    "and completed = 'N' " .
                    $statusclause . $staffclause .
                    "order by order_number asc ");
                $count = 2;
                while ($row = mysql_fetch_array($get_order, MYSQL_ASSOC)) {
                    $set_order = mysql_query("update todos set order_number = \"$count\" " .
                        "where todo_id = \"" . $row['todo_id'] . "\" ");
                    $count++;
                }
                mysql_free_result($get_order);
                
                // Add new item at top of list
                $add_item = mysql_query("INSERT into todos (description, project_id, " .
                    "duty_id, staff_assigned, order_number) values (\"" . $newtext . "\", \"0\", " .
                    "\"0\", \"$staff\", \"1\" ) ");
                $stop_trans = mysql_query("commit ");
            
            }
        }
        mysql_close($conn);

        $new_todolist = build_todolist($staff, $status, $todostatus, $priority, 
            $calmode, $project, $duty, $pageflag, $dragonly, $page, $maxresults);
        printf($new_todolist);
        exit;

        break;

      // Toggle task priority
      case 'togglepriority':

        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        // escape input data
        $content = trim(mysql_real_escape_string($content));

        // Check whether task is high or low priority already
        $result = mysql_query('SELECT priority FROM todos ' .
            'where todo_id = "' . $content . '"');
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        if ($row['priority'] == "High") {

            // mark task low priority
            $result = mysql_query('UPDATE todos set priority = "Low" WHERE todo_id="'
                . $content . '"');

        } else {

            // mark task completed
            $result = mysql_query('UPDATE todos set priority = "High" WHERE todo_id="'
                . $content . '"');
        }
        mysql_close($conn);

        $new_todolist = build_todolist($staff, $status, $todostatus, $priority, 
            $calmode, $project, $duty, $pageflag, $dragonly, $page, $maxresults);
        printf($new_todolist);
        exit;

        break;
    //}

    case "togglecomplete";

        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $content = trim(mysql_real_escape_string($content));
        
        // Check whether task is already marked completed
        $result = mysql_query('SELECT completed FROM todos ' .
            'where todo_id = "' . $content . '"');
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        if ($row['completed'] == "Y") {
        
            // mark task NOT completed
            $result = mysql_query('UPDATE todos set completed = "N", ' .
                'completed_date = NULL, completed_time = NULL ' .
                'WHERE todo_id="' . $content . '"');

        } else {
        
            // mark task completed
            $result = mysql_query('UPDATE todos set completed = "Y", ' .
                'completed_date = CURDATE(), completed_time = NOW() ' .
                'WHERE todo_id="' . $content . '"');
        }          
        mysql_close($conn);
        
        $new_todolist = build_todolist($staff, $status, $todostatus, $priority, 
            $calmode, $project, $duty, $pageflag, $dragonly, $page, $maxresults);
        printf($new_todolist);
        exit;
        break;

    case "togglecomplete_twolists";

        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $content = trim(mysql_real_escape_string($content));
        
        // Check whether task is already marked completed
        $result = mysql_query('SELECT completed FROM todos ' .
            'where todo_id = "' . $content . '"');
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        if ($row['completed'] == "Y") {
        
            // mark task NOT completed
            $result = mysql_query('UPDATE todos set completed = "N", ' .
                'completed_date = NULL WHERE todo_id="' . $content . '"');

        } else {
        
            // mark task completed
            $result = mysql_query('UPDATE todos set completed = "Y", ' .
                'completed_date = CURDATE() WHERE todo_id="' . $content . '"');
        }          
        mysql_close($conn);
        
        $new_twolists = build_two_lists($staff, $status, $priority, $calmode,
            $project, $duty, $pageflag, $dragonly, $page, $maxresults);
        printf($new_twolists);
        exit;
        break;

    // Delete comment
    case "deletecomment";

        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $content = trim(mysql_real_escape_string($content));
        $result = mysql_query("DELETE FROM comments WHERE comment_id = '$content' ");
        mysql_close($conn);
        $new_commentlist = build_commentlist($staff, $project, $duty, 
            $fromdate, $todate, $pageflag, $page, $maxresults);
        printf($new_commentlist);
        break;

    // Edit comment
    case "editcomment";

        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $newtext = trim(mysql_real_escape_string($newtext));
        if ($pdflag == "Project") {
            $edit_comment = mysql_query("UPDATE comments set comment_text = \"" . $newtext . 
                "\", project_id = \"$pdchange\", duty_id = \"0\", " .
                "submitter_id = \"" . $_SESSION['SESSION_USERID'] . "\", " . 
                "visibility = \"$visibility\" WHERE comment_id = '$content' ");
        
        } else if ($pdflag == "Duty") {
            $edit_comment = mysql_query("UPDATE comments set comment_text = \"" . $newtext . 
                "\", duty_id = \"$pdchange\", project_id = \"0\", " .
                "submitter_id = \"" . $_SESSION['SESSION_USERID'] . "\", " . 
                "visibility = \"$visibility\" WHERE comment_id = '$content' ");
                
        } else {
            $edit_comment = mysql_query("UPDATE comments set comment_text = \"" . $newtext . 
                "\", submitter_id = \"" . $_SESSION['SESSION_USERID'] . "\", " . 
                "visibility = \"$visibility\" WHERE comment_id = '$content' ");
        }
        mysql_close($conn);

        $new_commentlist = build_commentlist($staff, $project, $duty, 
            $fromdate, $todate, $pageflag, $page, $maxresults);
        printf($new_commentlist);
        exit;

        break;

    // Add comment
    case "addcomment";

        // Add new comment
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
            or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
        mysql_select_db(DB_DATABASE);
        $newtext = trim(mysql_real_escape_string($content));
        if ($newtext) {
            if ($pdflag == "Project") {
                
                // Assign this comment the same visibility as its project
                $get_vis = mysql_query("SELECT visibility from projects " .
                    "where project_id = \"$pdchange\" ");
                if ($row = mysql_fetch_array($get_vis, MYSQL_ASSOC)) {
                    $visibility = $row['visibility'];
                } else {
                    $visibility = 'Public';
                }
                mysql_free_result($get_vis);
                
                // Add new comment
                $add_comment = mysql_query("INSERT into comments (comment_text, " .
                    "project_id, duty_id, submitter_id, submit_date, submit_time, " .
                    "visibility) " .
                    "values (\"$newtext\", \"$pdchange\", \"0\", \"" . 
                    $_SESSION['SESSION_USERID'] . "\", curdate(), now(), " .
                    "\"$visibility\" ) ");
                
            } else if ($pdflag == "Duty") {
                
                // Assign this comment the same visibility as its duty
                $get_vis = mysql_query("SELECT visibility from duties " .
                    "where duty_id = \"$pdchange\" ");
                if ($row = mysql_fetch_array($get_vis, MYSQL_ASSOC)) {
                    $visibility = $row['visibility'];
                } else {
                    $visibility = 'Public';
                }
                mysql_free_result($get_vis);
                
                // Add new comment
                $add_comment = mysql_query("INSERT into comments (comment_text, " .
                    "project_id, duty_id, submitter_id, submit_date, submit_time, " .
                    "visibility) " .
                    "values (\"$newtext\", \"0\", \"$pdchange\", \"" . 
                    $_SESSION['SESSION_USERID'] . "\", curdate(), now(), " .
                    "\"$visibility\" ) ");
            
            } else {
                
                // Add new comment
                $add_comment = mysql_query("INSERT into comments (comment_text, " .
                    "project_id, duty_id, submitter_id, submit_date, submit_time) " .
                    "values (\"$newtext\", \"0\", \"$pdchange\", \"" . 
                    $_SESSION['SESSION_USERID'] . "\", curdate(), now() ) ");
                // Add new item at top of list
            
            }
        }
        mysql_close($conn);

        $new_commentlist = build_commentlist($staff, $project, $duty, 
            $fromdate, $todate, $pageflag, $page, $maxresults);
        printf($new_commentlist);
        exit;
        break;

}

?>

