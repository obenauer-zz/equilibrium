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

function build_todolist($staff, $status, $todostatus, $priority, $project, 
    $duty, $pageflag, $dragonly, $page, $maxresults) {

    global $displayed_user;
    //global $page;
    //global $maxresults;
    global $heading_color;
    global $background_color;
    
    // Only allow authorized people to sort or mark off to-do items
    if (($_SESSION['SESSION_ADMIN'] == "Y") 
        || ($staff == $_SESSION['SESSION_USERID'])) {
        $edit_priv = "Y";
    } else {
        $edit_priv = "N";
    }
    
    if ($staff == 0) {
        $staffclause = "";
    } else {
        $staffclause = "and t.staff_assigned = \"$staff\" ";
    }

    if ($priority == "All") {
        $priorityclause = "";
    } else {
        $priorityclause = "and t.priority = \"$priority\" ";
    }

    if (($project == 0) && ($duty == 0)) {
    
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
        
        if ($todostatus == "Pending") {
            $orderclause = "order by u.last_name, u.first_name, " .
                "t.order_number asc ";
        } else {
            $orderclause = "order by u.last_name, u.first_name, t.completed_date desc, " .
                "t.completed_time desc ";
        }
    
    } else if ($project == 0) {
    
        // Query conditions for Duties page
        $statusclause = "and t.duty_id = \"$duty\" ";

        //$staffclause = "";        
    
        if ($todostatus == "Pending") {
            $orderclause = "order by t.priority, t.duty_order asc ";
        } else {
            $orderclause = "order by t.completed_date desc, t.completed_time desc ";
        }
    } else {
    
        // Query conditions for Projects page
        $statusclause = "and t.project_id = \"$project\" ";
        
        //$staffclause = "";        
                
        if ($todostatus == "Pending") {
            $orderclause = "order by t.priority, t.project_order asc ";
        } else {
            $orderclause = "order by t.completed_date desc, t.completed_time desc ";
        }
    }
    
    if ($todostatus == "Completed") {
        $todoflag = "Y";
        $dateheading = "Completed";
        $checkimage = "checkmark.png";
        $checkother = "checkbox.png";
        $marktext = "Undo Completion";
    } else {
        $todoflag = "N";
        $dateheading = "Schedule";
        $checkimage = "checkbox.png";
        $checkother = "checkmark.png";
        $marktext = "Mark Completed";
    }

    // Get staff list for reassigning
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $query = "select user_id, first_name, last_name " .
        "from users where staff_flag = \"Y\" " .
        "order by last_name asc, first_name asc ";
    $get_staff = $conn->query($query);
    $stafflist = array();
    $staffnames = array();
    $nstaff = 0;
    while ($row = $get_staff->fetch_array(MYSQLI_ASSOC)) {
        $stafflist[$nstaff] = $row['user_id'];
        $staffnames[$nstaff] = $row['first_name'] . " " . $row['last_name'];
        $nstaff++;
    }
    $get_staff->free_result();
    $conn->close();
    
    // Create JavaScript array of staff names
    printf("<script language='JavaScript'>\n");
    printf("var stafflist = new Array(%d);\n", $nstaff);
    printf("var staffnames = new Array(%d);\n", $nstaff);
    for ($i = 0; $i < $nstaff; $i++) {
        printf("stafflist[$i] = '$stafflist[$i]';\n");
        printf("staffnames[$i] = '$staffnames[$i]';\n");
    }
    printf("</script>\n");
    
    // Get open projects for this staff member
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $query = "select t.project_id, t.title from projects as t " .
        "where t.status in ('Pending', 'Active', 'Suspended') " .
        $staffclause .
        "order by t.date_entered desc ";
    //printf("query = $query<br>\n");
    $get_projects = $conn->query($query);
    $project_ids = array();
    $projects = array();
    $nprojects = 0;
    while ($row = $get_projects->fetch_array(MYSQLI_ASSOC)) {
        if (strlen($row['title']) > 40) {
            $project_title = substr($row['title'], 0, 37) . "...";
        } else {
            $project_title = $row['title'];
        }
        $project_ids[$nprojects] = $row['project_id'];
        $projects[$nprojects] = $project_title;
        $nprojects++;
    }
    $get_projects->free_result();
    $conn->close();
    
    // Get active duties for this staff member
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $query = "select t.duty_id, t.title from duties as t " .
        "where t.status = 'Active' " .
        $staffclause .
        "order by t.date_entered desc ";
    $get_duties = $conn->query($query);
    $duty_ids = array();
    $duties = array();
    $nduties = 0;
    while ($row = $get_duties->fetch_array(MYSQLI_ASSOC)) {
        if (strlen($row['title']) > 40) {
            $duty_title = substr($row['title'], 0, 37) . "...";
        } else {
            $duty_title = $row['title'];
        }
        $duty_ids[$nduties] = $row['duty_id'];
        $duties[$nduties] = $duty_title;
        $nduties++;
    }
    $get_duties->free_result();
    $conn->close();
    
    // Choose dates to display in schedule options
    $weekend = 0;
    $dates = array();
    if ($weekend) {
    
        // Show all dates for next two weeks
        for ($i = 0; $i <= 14; $i++) {
            $day = mktime(0, 0, 0, date("m"), date("d") + $i, date("Y"));
            $dates[$i] = date("Y", $day) . "-" . date("m", $day) . "-" . 
                date("d", $day);
        }
    } else {
    
        // Show only weekdays for next two weeks
        $count = 0;
        for ($i = 0; $i <= 14; $i++) {
            $day = mktime(0, 0, 0, date("m"), date("d") + $i, date("Y"));
            $dayofweek = date('l', $day);
            if (($dayofweek != "Saturday") && ($dayofweek != "Sunday")) {
                $dates[$count] = date("Y", $day) . "-" . date("m", $day) . "-" . 
                    date("d", $day);
                $count++;
            }
        }
    }
                
    // Put relevant to-do's in order
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $query = "select t.todo_id " .
        "from todos as t left join users as u on t.staff_assigned = u.user_id " .
        "left join projects as p on t.project_id = p.project_id " .
        "left join duties as d on t.duty_id = d.duty_id " .
        "where t.completed = \"$todoflag\" " .
        $staffclause .
        $statusclause .
        $priorityclause .
        $orderclause;
    $todo_order = $conn->query($query);
    $count = 1;
    if ($project) {
        $order_var = "project_order";
    } else if ($duty) {
        $order_var = "duty_order";
    } else {
        $order_var = "order_number";
    }
    while ($row = $todo_order->fetch_array(MYSQLI_ASSOC)) {
        $set_order = $conn->query("update todos set $order_var = \"$count\" " .
            "where todo_id = \"" . $row['todo_id'] . "\" ");
        $count++;
    }
    $todo_order->free_result();
    
    // Define visibility clause
    if ($_SESSION['SESSION_USERID'] != $staff) {
        $visclause = "and t.visibility = 'Public' ";
    } else {
        $visclause = "";
    }
    
    // Get to-do items and display them    
    $query = "select t.todo_id, t.completed, t.schedule_date, t.completed_date, " .
        "t.priority, t.visibility, u.first_name, u.last_name, u.user_id, " .
        "u2.user_id as project_owner, u3.user_id as duty_owner, " .
        "p.project_id, p.icon_id as project_icon, p.title as project_title, " .
        "d.duty_id, d.icon_id as duty_icon, d.title as duty_title, t.description " .
        "from todos as t left join users as u on t.staff_assigned = u.user_id " .
        "left join projects as p on t.project_id = p.project_id " .
        "left join users as u2 on p.staff_assigned = u2.user_id " .
        "left join duties as d on t.duty_id = d.duty_id " .
        "left join users as u3 on d.staff_assigned = u3.user_id " .
        "where t.completed = \"$todoflag\" " .
        $staffclause .
        $statusclause .
        $visclause .
        $priorityclause .
        $orderclause;
    //printf("query = $query limit " . (($page - 1) * $maxresults) . ", $maxresults <br>\n");
    $list_full = $conn->query($query);

    if ($pageflag) {
        $list_page = $conn->query($query . "limit " . (($page - 1) * $maxresults) . 
            ", $maxresults ");
    } else {
        $list_page = $list_full;
    }

    $list = "<div id='$todostatus" . "_todoblock'>\n";
    //printf("query = $query limit %d, %d<br>\n", (($page - 1) * $maxresults), $maxresults);
    //$list .= "query = $query<br>\n";
    //$list .= "projectclause = $projectclause<br>\n";

    if ($list_full->num_rows) {

        // Show "Pending" or "Completed" label for individual projects and duties
        if (($project) || ($duty)) {
            $list .= "<b>$todostatus</b><br>\n";
        }
        
        // Show page numbers for results
        if ($pageflag) {
            $page_url = "todolist.php?staff=$staff&status=$status&todostatus=$todostatus";
            $list .= page_results($list_full->num_rows, $page, $maxresults, $page_url);
            //$list .= "BUILD_TODOLIST PAGEFLAG=$pageflag<BR>\n";
        }
        
        // Start div for to do list
        $list .= "<div id='$todostatus" . "_todolist' class='sortable_list'>\n";

        //$list .= "<div id='todolist' onmouseup='update_list(\"todolist\", " .
        //    "\"common.php?cmd=updatelist&staff=$staff&status=$status" .
        //    "&todostatus=$todostatus&edit_priv=$edit_priv&project=$project&duty=$duty&pageflag=$pageflag\");'>\n";

        while ($row = $list_page->fetch_array(MYSQLI_ASSOC)) {

            // If schedule date is present, convert it to Unix time
            $pastscheduledate = 0;
            if (($row['schedule_date']) && ($row['schedule_date'] != "NULL") && ($row['schedule_date'] != "000-00-00")) {
                $parts = explode("-", $row['schedule_date']);
                $calday = mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]);
                $schedule_column = $row['schedule_date'];
                // Check if scheduled date is before current day
                $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                if ($calday < $today) {
                    $pastscheduledate = 1;
                }
            } else {
                $schedule_column = "";
            }

            // If all staff members selected, show their initials
            if ($staff == 0) {
                $initials = substr($row['first_name'], 0, 1) .
                    substr($row['last_name'], 0, 1) . " ";
            } else {
                $initials = "";
            }

            // Identify to-do owner
            if ($row['user_id']) {
                $todo_owner = $row['user_id'];
            } else {
                $todo_owner = 0;
            }
            
            // Identify project owner
            if ($row['project_owner']) {
                $project_owner = $row['project_owner'];
            } else {
                $project_owner = 0;
            }
            
            // Identify duty owner
            if ($row['duty_owner']) {
                $duty_owner = $row['duty_owner'];
            } else {
                $duty_owner = 0;
            }
            
            // Check whether this is a private or public to-do item
            if ($row['visibility'] == 'Private') {
                $bgcolor = "style='background-color: $heading_color'";
                
            } else {
                $bgcolor = "";
            }
            
            $list .= "<div class='todo_item' id='" . $row['todo_id']. "' style='background-color: " . $background_color . "'>\n";
            $list .= "<table style='font-size: 14px'><tr valign='top'>\n";

            // Mark to-do completed, or undo it
            if ($dragonly == 0) {
                if ($edit_priv == "Y") {
                    //$list .= "<td style=\"width:30px\">\n";
                    $list .= "<td>\n";
                    $list .= "<img id='check_image_" . $row['todo_id'] . 
                        "' src=\"images/$checkimage\" title='$marktext' ";
                    if (($project) || ($duty)) {
                        $list .= "onclick='{ $(\"check_image_" . $row['todo_id'] . 
                            "\").src=\"images/$checkother\"; modify_two_lists(\"" . 
                            $row['todo_id'] . "\", \"togglecomplete\", \"$staff\", " .
                            "\"$status\", \"$project\", \"$duty\", \"$pageflag\", " .
                            "\"$dragonly\", \"$page\", \"$maxresults\");}'>\n";
                    } else {
                        $list .= "onclick='{ $(\"check_image_" . $row['todo_id'] . 
                            "\").src=\"images/$checkother\"; modify_todo(\"" . 
                            $row['todo_id'] . "\", \"togglecomplete\", \"$staff\", " .
                            "\"$status\", \"$todostatus\", \"$priority\", \"$project\", \"$duty\", " .
                            "\"$pageflag\", \"$dragonly\", \"$page\", \"$maxresults\"" .
                            ");}'>\n";
                    }
                    $list .= "</td>\n";
                } else {
                    //$list .= "<td style=\"width:30px\">\n";
                    $list .= "<td>\n";
                    $list .= "<img id='check_image_" . $row['todo_id'] . 
                        "' src=\"images/$checkimage\" ";
                    $list .= "title='$marktext'>\n";
                    $list .= "</td>\n";
    
                }
            }
            
            // Show staff initials if "All" staff is selected
            $list .= "<td><font color='blue'>$initials</font>\n";

            // Show project icon (if any), unless only one project is being viewed
            if (!$project) {
                if (($row['project_id'])  && ($row['project_id'] != "NULL")) {
                    $list .= "<a href='projects.php?action=view&project=" . 
                        $row['project_id'] . "'><img src=\"icons18/icon_" . 
                        $row['project_icon'] . ".png\" title='" . 
                        $row['project_title'] . "' border='0'></a>\n";
                }
            }
                
            // Show duty icon (if any), unless only one duty is being viewed
            if (!$duty) {
                if (($row['duty_id'])  && ($row['duty_id'] != "NULL")) {
                    $list .= "<a href='duties.php?action=view&duty=" . 
                        $row['duty_id'] . "'><img src=\"icons18/icon_" . 
                        $row['duty_icon'] . ".png\" title='" . 
                        $row['duty_title'] . "' border='0'></a>\n";
                }
            }
            
            // Show to-do item description
            $list .= "<span class='draghandle' onclick='modify_todo(\"$todostatus" . 
            "_todolist\", \"updatelist\", \"$staff\", \"$status\", \"$todostatus\", " .
            "\"$priority\", \"$project\", \"$duty\", \"$pageflag\", " .
            "\"$dragonly\", \"$page\", " .
            "\"$maxresults\");' $bgcolor>" . $row['description'] . "</span>\n";

            // Show to-do owner if different from project owner
            if ($project && $project_owner && $todo_owner) {
                if ($project_owner != $todo_owner) {
                    $todoname = $staffnames[array_search($todo_owner, $stafflist, true)];
                } else {
                    $todoname = "";
                }
            }
            
            if ($duty) {
                if ($duty_owner != $todo_owner) {
                    $todoname = $staffnames[array_search($todo_owner, $stafflist, true)];
                } else {
                    $todoname = "";
                }
            }
            
            if ($dragonly) {
                
                //$list .= "</td></tr></table></div>\n";
                $list .= "</td></tr></table>\n";

            } else if ($todostatus == "Completed") {
                
                if ($todoname) {
                    $list .= " (<b>$todoname</b>, " . short_date($row['completed_date']) . ")\n";
                } else {
                    $list .= " (" . short_date($row['completed_date']) . ")\n";
                }               
                $list .= "</tr></table>\n";

            } else {
                
                if ($todoname && $schedule_column) {
                    $list .= " (<b>$todoname</b>, " . short_date($schedule_column) . ")\n";
                } else if ($schedule_column) {
                    $list .= " (" . short_date($schedule_column) . ")\n";
                } else if ($todoname) {
                    $list .= " (<b>$todoname</b>)\n";
                }

                // Priority icon
                if ($row['priority'] == 'High') {
                    $list .= "&nbsp; <img src='images/plus.png' height='16' " .
                        "title='Priority' onclick='modify_todo(\"" . $row['todo_id'] . 
                        "\", \"togglepriority\", \"$staff\", \"$status\", " . 
                        "\"$todostatus\", \"$priority\", \"$project\", \"$duty\", " .
                        "\"$pageflag\", \"$dragonly\", \"$page\", " .
                        "\"$maxresults\");'>";
                } else {
                    $list .= "&nbsp; <img src='images/minus.png' height='16' " .
                        "title='Priority' onclick='modify_todo(\"" . $row['todo_id'] . 
                        "\", \"togglepriority\", \"$staff\", \"$status\", " . 
                        "\"$todostatus\", \"$priority\", \"$project\", \"$duty\", " .
                        "\"$pageflag\", \"$dragonly\", \"$page\", " .
                        "\"$maxresults\");'>";
                }

                // Edit icon
                $list .= "&nbsp; <img src='images/edit.png' height='16' " .
                "title='Edit' " .
                "onclick='show_item(\"edit_" . $row['todo_id'] . "\");'>";

                // Delete icon
                $list .= "&nbsp; <img src='images/delete.png' height='16' " .
                "title='Delete' " .
                "onclick='show_item(\"confirm_" . $row['todo_id'] . "\");'>";
                $list .= "</td>";
                $list .= "</tr></table>\n";
                //$list .= "</div>\n";

                // Div for edit box
                $list .= "<div style='display: none' id='edit_" . $row['todo_id'] . "' >" .
                "<center>Edit: <input type='text' " .
                " id='txtEditItem_" . 
                $row['todo_id'] . 
                "' size='130' maxlength='255' value=\"" . $row['description'] ."\" " .
                ">\n<br>";
                
                // Project/duty selection
                if ($row['project_id']) {
                    $pstyle = "style='font-weight: bold' ";
                    $dstyle = "style='font-weight: normal' ";
                    $selectname = "name = 'projectchange_" . $row['todo_id'] . 
                    "' ";
                } else if ($row['duty_id']) {
                    $pstyle = "style='font-weight: normal' ";
                    $dstyle = "style='font-weight: bold' ";
                    $selectname = "name = 'dutychange_" . $row['todo_id'] . 
                    "' ";
                } else {
                    $pstyle = "style='font-weight: bold' ";
                    $dstyle = "style='font-weight: normal' ";
                    $selectname = "name = 'projectchange_" . $row['todo_id'] . 
                    "' ";
                }
                $list .= "<font id='projectlabel_" . $row['todo_id'] . "' onclick='pdflag_" . $row['todo_id'] . " = \"Project\"; populate_projects( $(\"pdlist_" . $row['todo_id'] . "\"), $(\"projectlabel_" . $row['todo_id'] . "\"), $(\"dutylabel_" . $row['todo_id'] . "\"), project_ids, projects, \"" . 
                $row['project_id'] . "\");' $pstyle>";
                $list .= "Project</font> / ";
                $list .= "<font id='dutylabel_" . $row['todo_id'] . "' onclick='pdflag_" . $row['todo_id'] . " = \"Duty\"; populate_duties( $(\"pdlist_" . $row['todo_id'] . "\"), $(\"projectlabel_" . $row['todo_id'] . "\"), $(\"dutylabel_" . $row['todo_id'] . "\"), duty_ids, duties, \"" . 
                $row['duty_id'] . "\");' $dstyle>Duty</font>: \n";
                $list .= "<select id='pdlist_" . $row['todo_id'] . "' size='1' onChange=\"if (pdflag_" . $row['todo_id'] . " == 'Project') {duty = 0; project = project_ids[$('pdlist_" . $row['todo_id'] . "').selectedIndex - 1];} else {project = 0; duty = duty_ids[$('pdlist_" . $row['todo_id'] . "').selectedIndex - 1];}\" $selectname>\n";
                if ($row['project_id']) {
                    $list .= "<option value='P0'></option>\n";
                    for ($i = 0; $i < $nprojects; $i++) {
                        if ($row['project_id'] == $project_ids[$i]) {
                            $list .= "<option value='P" . $project_ids[$i] . 
                                "' selected>" . $projects[$i] . "</option>\n";
                        } else {
                            $list .= "<option value='P" . $project_ids[$i] . 
                                "'>" . $projects[$i] . "</option>\n";
                        }
                    }
                } else if ($row['duty_id']) {
                    $list .= "<option value='D0'></option>\n";
                    for ($i = 0; $i < $nduties; $i++) {
                        if ($row['duty_id'] == $duty_ids[$i]) {
                            $list .= "<option value='D" . $duty_ids[$i] . 
                                "' selected>" . $duties[$i] . "</option>\n";
                        } else {
                            $list .= "<option value='D" . $duty_ids[$i] . 
                                "'>" . $duties[$i] . "</option>\n";
                        }
                    }
                } else {
                    $list .= "<option value='P0'></option>\n";
                    for ($i = 0; $i < $nprojects; $i++) {
                        if ($row['project_id'] == $project_ids[$i]) {
                            $list .= "<option value='P" . $project_ids[$i] . 
                                "' selected>" . $projects[$i] . "</option>\n";
                        } else {
                            $list .= "<option value='P" . $project_ids[$i] . 
                                "'>" . $projects[$i] . "</option>\n";
                        }
                    }
                }
                $list .= "</select> &nbsp; \n";
                
                // Staff assigned selection
                $list .= "Staff assigned: ";
                $list .= "<select id='staff_assigned_" . $row['todo_id'] . 
                    "' name='staff_assigned_" . $row['todo_id'] . 
                    "'>\n";
                for ($i = 0; $i < $nstaff; $i++) {
                    if ($stafflist[$i] == $todo_owner) {
                        $list .= "<option value='" . $stafflist[$i]. 
                            "' selected>" . $staffnames[$i] . "</option>\n";
                    } else {
                        $list .= "<option value='" . $stafflist[$i]. 
                            "'>" . $staffnames[$i] . "</option>\n";
                    }
                }
                $list .= "</select><br>\n";
                
                // Visibility selection
                $list .= "Visibility: ";
                $list .= "<select id='visibility_" . $row['todo_id'] . 
                    "' name='visibility_" . $row['todo_id'] . 
                    "'>\n";
                if ($row['visibility'] == 'Public') {
                    $list .= "<option value='Public' selected>Public</option>\n";
                } else {
                    $list .= "<option value='Public'>Public</option>\n";
                }
                if ($row['visibility'] == 'Private') {
                    $list .= "<option value='Private' selected>Private</option>\n";
                } else {
                    $list .= "<option value='Private'>Private</option>\n";
                }
                $list .= "</select>\n";
                
                // Schedule date selection
                $list .= "Schedule: ";
                $list .= "<select id='schedule_" . $row['todo_id'] . 
                    "' name='schedule_" . $row['todo_id'] . 
                    "'>\n";
                if ($pastscheduledate) {
                    $list .= "<option value='$schedule_column' selected " .
                        "style='background-color: $heading_color'>" . 
                        short_date($schedule_column) . "</option>\n";
                }
                if ($schedule_column == "") {
                    $list .= "<option value='NULL' selected></option>\n";
                } else {
                    $list .= "<option value='NULL'></option>\n";
                }
                for ($i = 0; $i < count($dates); $i++) {
                    if ($schedule_column == $dates[$i]) {
                        $list .= "<option value='$dates[$i]' selected>" . 
                            short_date($dates[$i]) . "</option>\n";
                    } else {
                        $list .= "<option value='$dates[$i]'>" . 
                            short_date($dates[$i]) . "</option>\n";
                    }
                }
                $list .= "</select><br>\n";
                
                // Update and cancel icons
                $list .= "<input type='button' value='Update' " .
                "onclick='{modify_todo(\"" . $row['todo_id'] . 
                "\", \"edititem\", \"$staff\", \"$status\", \"$todostatus\", " .
                "\"$priority\", \"$project\", \"$duty\", \"$pageflag\", " .
                "\"$dragonly\", \"$page\", \"$maxresults\");}'>  &nbsp; &nbsp; " .
                "<input type='button' value='Cancel' onclick='hide_item(\"edit_" . 
                $row['todo_id'] . "\");'></center></div>\n";

                // Set variable for project/duty flag
                printf("<script type='text/javascript'>\n");
                if ($row['project_id']) {
                    printf("var pdflag_" . $row['todo_id'] . " = \"Project\";\n");
                } else if ($row['duty_id']) {
                    printf("var pdflag_" . $row['todo_id'] . " = \"Duty\";\n");
                } else {
                    printf("var pdflag_" . $row['todo_id'] . " = \"Project\";\n");
                }
                printf("</script>\n");

                // Div to confirm deletion
                $list .= "<div class='confirm' id='confirm_" . $row['todo_id'] . 
                "' ><font color='red'>" .
                "<center>Are you sure you want to delete this item?</font><br>" .
                "<input type='button' value='Confirm Deletion' " .
                "onclick='{Element.hide(\$(\"confirm_" . $row['todo_id'] . 
                "\").id); modify_todo(\"" . $row['todo_id'] . 
                "\", \"deleteitem\", \"$staff\", \"$status\", \"$todostatus\", " .
                "\"$priority\", \"$project\", \"$duty\", \"$pageflag\", " .
                "\"$dragonly\", \"$page\", \"$maxresults\");" .
                "}'>  &nbsp; &nbsp; " .
                "<input type='button' value='Cancel' onclick='hide_item(\"confirm_" . 
                $row['todo_id'] . "\");'></center></div>\n";
    
                // End the div for this to-do item (containing edit and delete divs)
                $list .= "</div>\n";
            }

        }

        // End the div for all to-do items
        $list .= "</div>\n";
        
    } else {
        if ($project) {
            $list .= "<b>No " . strtolower($todostatus) . " to-do items found " .
                "for this project.</b><br>\n";
        } else if ($duty) {
            $list .= "<b>No " . strtolower($todostatus) . " to-do items found " .
                "for this duty.</b><br>\n";
        } else {
            if ($displayed_user == "All") {
                if ($status == "All") {
                    $list .= "<b>No " . strtolower($todostatus) . " to-do items found " .
                        "in database.</b><br>\n";
                } else {
                    $list .= "<b>No " . strtolower($todostatus) . " to-do items for " . 
                        strtolower($status) . " projects found in database.</b><br>\n";
                }
            } else {
                if ($status == "All") {
                    $list .= "<b>No " . strtolower($todostatus) . " to-do items found " .
                        "for $displayed_user.</b><br>\n";
                } else {
                    $list .= "<b>No " . strtolower($todostatus) . " to-do items for " . 
                        strtolower($status) . " projects found for " .
                        "$displayed_user.</b><br>\n";
                }
            }
        }
    }

    // End the "to do block" div 
    $list .= "</div>\n";
        
    // Close the database connection
    $list_full->free_result();
    if ($pageflag) {
        $list_page->free_result();
    }
    $conn->close();

    // Make list sortable
    if (($todostatus == "Pending") && ($_SESSION['SESSION_USERID'] == $staff)) {
        $list .= "<script type='text/javascript'>\n";
        $list .= "Sortable.create('$todostatus" . "_todolist', {tag:'div', handle:'draghandle'});\n"; 
        $list .= "</script>\n";
    }

    return $list;
}

function build_two_lists($staff, $status, $project, $duty, $pageflag, 
    $dragonly, $page, $maxresults) {

    $two_lists = build_todolist($staff, $status, "Pending", "All", $project, $duty, '0', 
        '0', $page, $maxresults);
    $two_lists .= "<br>\n";
    $two_lists .= build_todolist($staff, $status, "Completed", "All", $project, $duty, '0', 
        '0', $page, $maxresults);

    return $two_lists;
}

function build_commentlist($staff, $project, $duty, $fromdate, $todate, 
    $pageflag, $page, $maxresults) {

    global $displayed_user;
    //global $page;
    //global $maxresults;
    global $heading_color;
    global $background_color;

    // Only the assigned staff or administrators can edit this project, unless it's not assigned
    if (($_SESSION['SESSION_ADMIN'] == "Y") || ($staff == $_SESSION['SESSION_USERID'])) {
        $edit_priv = "Y";
    } else {
        $edit_priv = "N";
    }

    if ($staff == 0) {
        $staffclause = "";
    } else {
        $staffclause = "where c.submitter_id = \"$staff\" ";
    }

    // Define visibility clause
    if ($_SESSION['SESSION_USERID'] != $staff) {
        $visclause = "and c.visibility = 'Public' ";
    } else {
        $visclause = "";
    }
    
    // Define date clause
    if ($project) {
        $dateclause = "";
        $pdclause = "and c.project_id = \"$project\" ";
    } else if ($duty) {
        $dateclause = "";
        $pdclause = "and c.duty_id = \"$duty\" ";
    } else {
        $dateclause = "and c.submit_date >= \"$fromdate\" " .
            "and c.submit_date <= \"$todate\" ";
        $pdclause = "";
    }
    
    // Fill Javascript arrays with open projects and active duties
    fill_pdlist_arrays($staff, $projects, $project_ids, $duties, $duty_ids);
        
    // Get to-do items for active projects and display them
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $query = "select c.comment_id, c.comment_text, c.submit_date, " .
        "u.user_id, u.first_name, u.last_name, c.visibility, " .
        "p.project_id, p.icon_id as project_icon, p.title as project_title, " .
        "d.duty_id, d.icon_id as duty_icon, d.title as duty_title " .
        "from comments as c left join users as u on c.submitter_id = u.user_id " .
        "left join projects as p on c.project_id = p.project_id " .
        "left join duties as d on c.duty_id = d.duty_id " .
        $staffclause . $visclause . $dateclause . $pdclause . 
        "order by c.submit_time desc ";
        //printf("query = $query<br>\n");
    $list_full = $conn->query($query);
    if ($pageflag) {
        $list_page = $conn->query($query . "limit " . (($page - 1) * $maxresults) . 
            ", $maxresults ");
    } else {
        $list_page = $list_full;
    }

    $list = "<div id='commentblock'>\n";
    //$list .= "fromdate = $fromdate, todate = $todate<br>\n";
    
    if ($list_page->num_rows) {

        // Show page numbers for results
        if ($pageflag) {
            $page_url = "logbook.php?staff=$staff&fromdate=$fromdate&todate=$todate";
            $list .= page_results($list_full->num_rows, $page, $maxresults, $page_url);
        }
        
        while ($row = $list_page->fetch_array(MYSQLI_ASSOC)) {
    
            // If all staff members selected, show their initials
            if ($staff == 0) {
                $initials = substr($row['first_name'], 0, 1) .
                    substr($row['last_name'], 0, 1) . " ";
            } else {
                $initials = "";
            }

            $list .= "<div id='comment_" . $row['comment_id'] . "'>";

            // Check whether this is a private or public to-do item
            if ($row['visibility'] == 'Private') {
                $bgcolor = "$heading_color";
                
            } else {
                $bgcolor = "white";
            }
        
            // Date submitted
            $list .= "<table><tr valign='top'><td>" . short_date($row['submit_date']) . "</td>\n";
            $list .= "<td bgcolor='$bgcolor' style='{border: 1px solid $heading_color; padding-left: 5px; padding-right: 5px;}'><font color='blue'>$initials</font>";
            
            // Show project icon (if any), unless only one project is being viewed
            if (!$project) {
                if (($row['project_id'])  && ($row['project_id'] != "NULL")) {
                    $list .= "<a href='projects.php?action=view&project=" . 
                        $row['project_id'] . "'><img src=\"icons18/icon_" . 
                        $row['project_icon'] . ".png\" title='" . 
                        $row['project_title'] . "' border='0'></a>\n";
                }
            }
                
            // Show duty icon (if any), unless only one duty is being viewed
            if (!$duty) {
                if (($row['duty_id'])  && ($row['duty_id'] != "NULL")) {
                    $list .= "<a href='duties.php?action=view&duty=" . 
                        $row['duty_id'] . "'><img src=\"icons18/icon_" . 
                        $row['duty_icon'] . ".png\" title='" . 
                        $row['duty_title'] . "' border='0'></a>\n";
                }
            }
            
            // Comment text
            $comment_text = str_replace("\'", "'", $row['comment_text']);
            $comment_text = str_replace('\"', '"', $comment_text);
            $comment_text = str_replace("%", "%%", $comment_text);
            $list .= $comment_text . "</td>\n";
    
            // Don't show edit or delete icons if user does not have edit_priv
            if ($edit_priv == "Y") {
    
                // "Edit" icon
                $list .= "<td><img src='images/edit.png' height='16' style='{position:relative; padding-left:20px;}' " .
                    "onclick='show_item(\"editcomment_" . $row['comment_id'] . "\");'></td>";
        
                // "Delete" icon
                $list .= "<td style='background-color:" . $background_color . "'><img src='images/delete.png' height='16' onclick='show_item(\"confirmcomment_" . $row['comment_id'] . "\");'>" .
                "</td></tr></table>\n";
        
                // Div for edit box
                $list .= "<div style='display: none' id='editcomment_" . 
                    $row['comment_id'] . "'><center>Edit: <textarea id='txtEditEntry_" . 
                    $row['comment_id'] . 
                    "' rows='4' cols='120' class='description'>" .$comment_text . 
                    "</textarea>\n<br>";
                
                // Project/duty selection
                $list .= make_pdlist($staff, $row['comment_id'], $row['project_id'], 
                    $projects, $project_ids, $row['duty_id'], $duties, $duty_ids, 0);
                
                // Visibility selection
                $list .= "Visibility: ";
                $list .= "<select id='viscomment_" . $row['comment_id'] . 
                    "' name='viscomment_" . $row['comment_id'] . 
                    "'>\n";
                if ($row['visibility'] == 'Public') {
                    $list .= "<option value='Public' selected>Public</option>\n";
                } else {
                    $list .= "<option value='Public'>Public</option>\n";
                }
                if ($row['visibility'] == 'Private') {
                    $list .= "<option value='Private' selected>Private</option>\n";
                } else {
                    $list .= "<option value='Private'>Private</option>\n";
                }
                $list .= "</select><br>\n";
                
                // Update and cancel icons
                $list .= "<input type='button' value='Update' " .
                "onclick='{modify_comment(\"" . $row['comment_id'] . 
                "\", \"editcomment\", \"$staff\", \"$project\", \"$duty\", " .
                "\"$fromdate\", \"$todate\", \"$pageflag\", \"$page\", \"$maxresults\");}'>  &nbsp; &nbsp; " .
                "<input type='button' value='Cancel' onclick='hide_item(\"editcomment_" . 
                $row['comment_id'] . "\");'></center><br></div>\n";

                // Set variable for project/duty flag
                printf("<script type='text/javascript'>\n");
                if ($row['project_id']) {
                    printf("var pdflag_" . $row['comment_id'] . " = \"Project\";\n");
                } else if ($row['duty_id']) {
                    printf("var pdflag_" . $row['comment_id'] . " = \"Duty\";\n");
                } else {
                    printf("var pdflag_" . $row['comment_id'] . " = \"Project\";\n");
                }
                printf("</script>\n");

                // Div to confirm deletion
                $list .= "<div class='confirm' id='confirmcomment_" . $row['comment_id'] . 
                "' ><font color='red'>" .
                "<center>Are you sure you want to delete this comment?</font><br>" .
                "<input type='button' value='Confirm Deletion' " .
                "onclick='{modify_comment(\"" . $row['comment_id'] . 
                "\", \"deletecomment\", \"$staff\", \"$project\", \"$duty\", " .
                "\"$fromdate\", \"$todate\", \"$pageflag\", \"$page\", \"$maxresults\");" .
                "}'>  &nbsp; &nbsp; " .
                "<input type='button' value='Cancel' onclick='hide_item(\"confirmcomment_" . 
                $row['comment_id'] . "\");'></center><br></div>\n";
                
                $list .= "</div>\n";
            
            } else {
    
                // Don't show "Edit" icon
                $list .= "<td></td>";
        
                // Don't show "Delete" icon
                $list .= "<td></td></tr></table>\n";
            
                $list .= "</div>\n";
            }
    
        }

        // End comment block div
        $list .= "</div>\n";
    } else {
    
        if ($project) {
            $list .= "<b>No comments entered for this project.</b><br>\n";
        } else if ($duty) {
            $list .= "<b>No comments entered for this duty.</b><br>\n";
        } else {
            if ($displayed_user == "All") {
                $list .= "<b>No comments entered during this date range.</b><br>\n";
            } else {
                $list .= "<b>No comments entered by $displayed_user in this date range.</b><br>\n";
            }
        }
    }

    // Close the database connection
    $list_full->free_result();
    if ($pageflag) {
        $list_page->free_result();
    }
    $conn->close();

    return $list;
}

function show_project_comments($project, $edit_priv) {

    global $background_color;
    global $heading_color;

    $myList = "";

    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db(DB_DATABASE);
    $get_comments = mysql_query("select comment_id, comment_text, submit_date, " .
        "submit_time from comments where project_id = \"$project\" " .
        "order by submit_time desc ");

    if (mysql_num_rows($get_comments) == 0) {
        $myList .= "No comments entered for this project.<br>\n";
    }

    // Don't show edit or delete icons if user does not have edit_priv
    if ($edit_priv == "Y") {

        while ($row = mysql_fetch_array($get_comments, MYSQL_ASSOC))
        {

            $myList .= "<div id='comment_" . $row['comment_id'] . "'>";

            // Date submitted
            $myList .= "<table><tr valign='top'><td>" . short_date($row['submit_date
']) . "</td>\n";

            // Comment text
            $myList .= "<td bgcolor='white' style='{border: 1px solid $heading_color
; padding-left: 5px; padding-right: 5px;}'>"
                . $row['comment_text'] . "</td>\n";

            // "Edit" icon
            $myList .= "<td><img src='images/pencil.gif' style='{position:relative;
padding-left:20px;}' " .
                "onclick='show_editbox(\"editcomment_" . $row['comment_id'] . "\");'
></td>";

            // "Delete" icon
            $myList .= "<td style='background-color:" . $background_color . "'><img src='images/trash.gif' onclick='show_confirm(\"confirmcomment_" . $row['comment_id'] . "\");'>" .
            "</td></tr></table>\n";

            // Div for edit box
            $myList .= "<div class='editbox' id='editcomment_" . $row['comment_id'] . "' style='background-color: white;'>" .
            "<center><font color='red'>Edit:</font><br>\n<textarea id='txtEditComment_" .
            $row['comment_id'] .
            "' rows='3' cols='100'>\n" .
            //"onkeydown='handleKeyEditComment(event, $project, " . $row['comment_id'] . ")'>\n" .
            $row['comment_text'] . "</textarea><br>" .
            "<img src='images/update_icon.png' " .
            "onclick='{var editcomm = $(\"comment_" . $row['comment_id'] . "\"); Element.hide(editcomm); " .
            "process_comments(" . $row['comment_id'] . ", \"editComment\", \"" . $project . "\");}'>  &nbsp; &nbsp; " .
            //"process_comments(editcomm.id, \"editComment\", \"" . $project . "\");}'>  &nbsp; &nbsp; " .
            "<img src='images/cancel_icon.png' onclick='hide_editbox(\"editcomment_" . $row['comment_id'] . "\");'></center></div>\n";

            // Div to confirm deletion
            $myList .= "<div class='confirm' id='confirmcomment_" . $row['comment_id'] . "' style='background-color: white;'><font color='red'>" .
            "<center>Are you sure you want to delete this item?</font><br>" .
            "<img src='images/confirm_delete_icon.png' " .
            "onclick='{var remcomm = $(\"comment_" . $row['comment_id'] . "\"); Element.hide(remcomm); " .
            "process_comments(" . $row['comment_id'] . ", \"delComment\", \"" .
            $project . "\");}'>  &nbsp; &nbsp; " .
            "<img src='images/cancel_icon.png' onclick='hide_confirm(\"confirmcomment_" . $row['comment_id'] . "\");'></center></div>";


            $myList .= "</div>\n";
        }
    } else {

        while ($row = mysql_fetch_array($get_comments, MYSQL_ASSOC))
        {

            $myList .= "<div id='comment_" . $row['comment_id'] . "' style='background-color: white'>";

            // Date submitted
            $myList .= "<table><tr valign='top'><td>" . short_date($row['submit_date']) . "</td>\n";

            // Comment text
            $myList .= "<td bgcolor='white' style='border: 1px solid $heading_color;'>"
                . $row['comment_text'] . "</td>\n";

            // Don't show "Edit" icon
            $myList .= "<td></td>";

            // Don't show "Delete" icon
            $myList .= "<td></td></tr></table>\n";

            $myList .= "</div>\n";
        }

    }

    mysql_free_result($get_comments);
    mysql_close($conn);

    return $myList;
}



function ugly_date($formatted_date) {

    if ($formatted_date) {
        $parts = explode(" ", $formatted_date);
        $months = array("January", "February", "March", "April", "May", "June", "July", 
            "August", "September", "October", "November", "December");
        $monthnum = array_search($parts[0], $months) + 1;
        if (strlen($monthnum) < 2) {
            $monthnum = "0" . $monthnum;
        }
        $daynum = substr($parts[1], 0, strlen($parts[1]) - 1);
        if (strlen($daynum) < 2) {
            $daynum = "0" . $daynum;
        }
        $year = $parts[2];
        $newformat = $year . "-" . $monthnum . "-" . $daynum;
        return $newformat;
    } else {
        return $formatted_date;
    }
}

function pretty_date($formatted_date) {

    if ($formatted_date) {

        if ($formatted_date == "0000-00-00") {
            return "";
        }

        $parts = explode("-", $formatted_date);
        $months = array("January", "February", "March", "April", "May", "June", "July", 
            "August", "September", "October", "November", "December");
        $monthname = $months[$parts[1] - 1];
        $daynum = $parts[2];
        if (substr($daynum, 0, 1) == "0") {
            $daynum = substr($daynum, 1);
        }
        $year = $parts[0];
        $newformat = "$monthname $daynum, $year";
        return $newformat;
    } else {
        return $formatted_date;
    }
}

function short_date($formatted_date) {

    if ($formatted_date) {

        if ($formatted_date == "0000-00-00") {
            return "";
        }

        $parts = explode("-", $formatted_date);
        $monthnum = $parts[1];
        if (substr($monthnum, 0, 1) == "0") {
            $monthnum = substr($monthnum, 1);
        }
        $daynum = $parts[2];
        if (substr($daynum, 0, 1) == "0") {
            $daynum = substr($daynum, 1);
        }
        $year = $parts[0];
        $weekday = date("D", mktime(0, 0, 0, $monthnum, $daynum, $year));
        $shortyear = date("y", mktime(0, 0, 0, $monthnum, $daynum, $year));

        // If the date isn't in the current year, specify the year
        if ($year != date("Y")) {
            $newformat = "$weekday $monthnum/$daynum/$shortyear";
        } else {
            $newformat = "$weekday $monthnum/$daynum";
        }
        return $newformat;
    } else {
        return "";
    }
}

function shorter_date($formatted_date) {

    if ($formatted_date) {

        if ($formatted_date == "0000-00-00") {
            return "";
        }

        $parts = explode("-", $formatted_date);
        $monthnum = $parts[1];
        if (substr($monthnum, 0, 1) == "0") {
            $monthnum = substr($monthnum, 1);
        }
        $daynum = $parts[2];
        if (substr($daynum, 0, 1) == "0") {
            $daynum = substr($daynum, 1);
        }
        $year = $parts[0];
        $weekday = date("D", mktime(0, 0, 0, $monthnum, $daynum, $year));
        $shortyear = date("y", mktime(0, 0, 0, $monthnum, $daynum, $year));

        // If the date isn't in the current year, specify the year
        if ($year != date("Y")) {
            $newformat = "$monthnum/$daynum/$shortyear";
        } else {
            $newformat = "$monthnum/$daynum";
        }
        return $newformat;
    } else {
        return "";
    }
}

function mysql_date($standard_date) {

    if ($standard_date) {
        $parts = explode("/", $standard_date);
        $monthnum = $parts[0];
        if (strlen($monthnum) < 2) {
            $monthnum = "0" . $monthnum;
        }
        $daynum = $parts[1];
        if (strlen($daynum) < 2) {
            $daynum = "0" . $daynum;
        }
        $year = $parts[2];
        $newformat = $year . "-" . $monthnum . "-" . $daynum;
        return $newformat;
    } else {
        return $standard_date;
    }
}

function standard_date($mysql_date) {

    if ($mysql_date) {

        if ($mysql_date == "0000-00-00") {
            return "";
        }

        $parts = explode("-", $mysql_date);
        $monthnum = $parts[1];
        //if (substr($monthnum, 0, 1) == "0") {
        //    $monthnum = substr($monthnum, 1);
        //}
        $daynum = $parts[2];
        //if (substr($daynum, 0, 1) == "0") {
        //    $daynum = substr($daynum, 1);
        //}
        $longyear = $parts[0];

        // If the date isn't in the current year, specify the year
        $newformat = "$monthnum/$daynum/$longyear";
        return $newformat;
    } else {
        return "";
    }
}

function display_controls($fields, $values) {

    // Make sure at least one field was passed
    if (count($fields) < 1) {
        return "";
    }

    // Start table for controls
    printf("<table class='smalltext'><tr>\n");

    // Store passed arguments into local variables
    for ($i = 0; $i < count($fields); $i++) {
        if ($fields[$i] == "Staff") {
            
            // Staff Member
            $staff = $values[$i];
            //printf("<td>Staff Member: \n");
            printf("<td>Staff: \n");
            printf("<select name='staff' size='1' onChange='viewform.submit()'>\n");
            
            // Retrieve and display list of staff members
            $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
                or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
            mysql_select_db (DB_DATABASE);
            $get_staff = mysql_query("select user_id, first_name, last_name from users " .
                "where staff_flag = \"Y\" order by last_name ");
            if ($staff == 0) {
                printf("<option value='0' selected>All\n");
            } else {
                printf("<option value='0'>All\n");
            }
            $displayed_user = "All";
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
            
            printf("</select></td>\n");

            continue;
        }
        if ($fields[$i] == "Pstatus") {

            // Project status
            $status = $values[$i];
            //printf("<td>Project Status: \n");
            printf("<td>Projects: \n");
            printf("<select name='status' size='1' onChange='viewform.submit()'>\n");
            
            // Default status is Active
            if ($status == "") {
                $status = "Active";
            }
    
            // Show list of status values
            if ($status == "All") {
                printf("<option value='All' selected>All\n");
            } else {
                printf("<option value='All'>All\n");
            }
            if ($status == "Open") {
                printf("<option value='Open' selected>Open\n");
            } else {
                printf("<option value='Open'>Open\n");
            }
            if ($status == "Proposed") {
                printf("<option value='Proposed' selected>Proposed\n");
            } else {
                printf("<option value='Proposed'>Proposed\n");
            }
            if ($status == "Pending") {
                printf("<option value='Pending' selected>Pending\n");
            } else {
                printf("<option value='Pending'>Pending\n");
            }
            if ($status == "Active") {
                printf("<option value='Active' selected>Active\n");
            } else {
                printf("<option value='Active'>Active\n");
            }
            if ($status == "Suspended") {
                printf("<option value='Suspended' selected>Suspended\n");
            } else {
                printf("<option value='Suspended'>Suspended\n");
            }
            if ($status == "Aborted") {
                printf("<option value='Aborted' selected>Aborted\n");
            } else {
                printf("<option value='Aborted'>Aborted\n");
            }
            if ($status == "Completed") {
                printf("<option value='Completed' selected>Completed\n");
            } else {
                printf("<option value='Completed'>Completed\n");
            }
            
            printf("</select></td>\n");

            continue;
        }
        if ($fields[$i] == "Dstatus") {

            // Duty Status
            $dutystatus = $values[$i];
            //printf("<td>Duty Status: \n");
            printf("<td>Duties: \n");
            printf("<select name='dutystatus' size='1' onChange='viewform.submit()'>\n");
            
            // Default status is Active
            if ($dutystatus == "") {
                $dutystatus = "Active";
            }
    
            // Show list of status values
            if ($dutystatus == "All") {
                printf("<option value='All' selected>All\n");
            } else {
                printf("<option value='All'>All\n");
            }
            if ($dutystatus == "Active") {
                printf("<option value='Active' selected>Active\n");
            } else {
                printf("<option value='Active'>Active\n");
            }
            if ($dutystatus == "Inactive") {
                printf("<option value='Inactive' selected>Inactive\n");
            } else {
                printf("<option value='Inactive'>Inactive\n");
            }            
            printf("</select></td>\n");

            continue;
        }
        if ($fields[$i] == "Tstatus") {

            // ToDo Status
            $todostatus = $values[$i];
            //printf("<td>To Do Status: \n");
            printf("<td>To Do Status: \n");
            printf("<select name='todostatus' size='1' onChange='viewform.submit()'>\n");
            
            // Default todo status is Pending
            if ($todostatus == "") {
                $todostatus = "Pending";
            }
    
            // Show list of status values
            if ($todostatus == "Pending") {
                printf("<option value='Pending' selected>Pending\n");
            } else {
                printf("<option value='Pending'>Pending\n");
            }
            if ($todostatus == "Completed") {
                printf("<option value='Completed' selected>Completed\n");
            } else {
                printf("<option value='Completed'>Completed\n");
            }
            
            printf("</select></td>\n");

            continue;
        }
        if ($fields[$i] == "Priority") {

            // ToDo Status
            $priority = $values[$i];
            printf("<td>Priority: \n");
            printf("<select name='priority' size='1' onChange='viewform.submit()'>\n");
            
            // Default priority is High
            if ($priority == "") {
                $priority = "High";
            }
    
            // Show list of status values
            if ($priority == "High") {
                printf("<option value='High' selected>High\n");
            } else {
                printf("<option value='High'>High\n");
            }
            if ($priority == "Low") {
                printf("<option value='Low' selected>Low\n");
            } else {
                printf("<option value='Low'>Low\n");
            }
            
            printf("</select></td>\n");

            continue;
        }
        if ($fields[$i] == "FromDate") {

            // From Date
            $fromdate = $values[$i];
            printf("<td>From: \n");
            show_date_form_onchange('fromdate', $fromdate);
            printf("</td>\n");

            continue;
        }
        if ($fields[$i] == "ToDate") {

            // To Date
            $todate = $values[$i];
            printf("<td>To: \n");
            show_date_form_onchange('todate', $todate);
            printf("</td>\n");

            continue;
        }
        if ($fields[$i] == "Ftype") {

            // File Type
            $filetype = $values[$i];
            printf("<td>File Type: \n");
            printf("<select name='filetype' size='1' onChange='viewform.submit()'>\n");
            
            // Default todo status is Pending
            if ($todostatus == "") {
                $todostatus = "Pending";
            }
    
            // Show list of file type values
            if ($filetype == "") {
                printf("<option value='NULL' selected>\n");
            } else {
                printf("<option value='NULL'>\n");
            }
            if ($filetype == "Report") {
                printf("<option value='Report' selected>Report\n");
            } else {
                printf("<option value='Report'>Report\n");
            }
            if ($filetype == "Program") {
                printf("<option value='Program' selected>Program\n");
            } else {
                printf("<option value='Program'>Program\n");
            }
            if ($filetype == "Protocol") {
                printf("<option value='Protocol' selected>Protocol\n");
            } else {
                printf("<option value='Protocol'>Protocol\n");
            }
            
            printf("</select></td>\n");

            continue;
        }
        if ($fields[$i] == "Results") {

            // Results Per Page
            $maxresults = $values[$i];
            printf("<td>Results Per Page: \n");
            printf("<input type='text' name='maxresults' size='2' value='$maxresults' onChange='viewform.submit()'>\n");
            printf("</td>\n");
            continue;
        }
    }

    printf("</tr></table>\n");

    return $displayed_user;

}

function show_date_form($datevar, $defaultvalue) {

    // Show a date variable in a form
    printf("<input type='text' name='$datevar' id='$datevar' size='12' value='$defaultvalue'>\n");
    printf("<img src='images/calendar.gif' id='$datevar" . "_cal' ");
    printf("style='cursor: pointer; border: 1px solid red;' title='Date selector' ");
    printf("onmouseover=\"this.style.background='red';\" ");
    printf("onmouseout=\"this.style.background=''\">\n");
    printf("<script type='text/javascript'>\n");
    printf("    Calendar.setup({\n");
    printf("        inputField     :    \"$datevar\",     // id of the input field\n");
    printf("        ifFormat       :    \"%%m/%%d/%%Y\",      // format of the input field\n");
    printf("        button         :    \"$datevar" . "_cal\",  // trigger for the calendar (button ID)\n");
    printf("        align          :    \"Tl\",           // alignment (defaults to \"Bl\")\n");
    printf("        singleClick    :    true\n");
    printf("    });\n");
    printf("</script>\n");

}

function show_date_form_onchange($datevar, $defaultvalue) {

    // Show a date variable in a form
    printf("<input type='text' name='$datevar' id='$datevar' size='12' value='$defaultvalue' onChange='viewform.submit()'>\n");
    printf("<img src='images/calendar.gif' id='$datevar" . "_cal' ");
    printf("style='cursor: pointer; border: 1px solid red;' title='Date selector' ");
    printf("onmouseover=\"this.style.background='red';\" ");
    printf("onmouseout=\"this.style.background=''\">\n");
    printf("<script type='text/javascript'>\n");
    printf("    Calendar.setup({\n");
    printf("        inputField     :    \"$datevar\",     // id of the input field\n");
    printf("        ifFormat       :    \"%%m/%%d/%%Y\",      // format of the input field\n");
    printf("        button         :    \"$datevar" . "_cal\",  // trigger for the calendar (button ID)\n");
    printf("        align          :    \"Tl\",           // alignment (defaults to \"Bl\")\n");
    printf("        singleClick    :    true\n");
    printf("    });\n");
    printf("</script>\n");

}

function page_results($rowcount, $page, $maxresults, $url) {

    // Show total number of results
    $paging = "<table cellspacing='5' cellpadding='0' class='smalltext'><tr>";
    $paging .= "<td><b>Results: $rowcount</b></td>\n";

    // Show page number
    $maxpage = ceil($rowcount / $maxresults);
    $paging .= "<td>&nbsp; Page $page of $maxpage</td>\n";
    
    // Previous page button
    if ($page > 1) {
        $paging .= "<td>&nbsp; <a href='$url&page=" . ($page - 1) . 
            "&maxresults=$maxresults'>Prev</a></td>\n";
    }

    // Next page button
    if ($page < $maxpage) {
        $paging .= "<td>&nbsp; <a href='$url&page=" . ($page + 1) . 
            "&maxresults=$maxresults'>Next</a></td>\n";
    }
    $paging .= "</tr></table>\n";

    return $paging;

}

function fill_pdlist_arrays($staff, &$projects, &$project_ids, &$duties, &$duty_ids) {

    // Create JavaScript array of open projects
    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db (DB_DATABASE);
    $get_projects = mysql_query("select project_id, title from projects " .
        "where staff_assigned = \"$staff\" " .
        "and status in ('Pending', 'Active', 'Suspended') " .
        "order by date_entered desc ");
    $project_ids = array();
    $projects = array();
    $nprojects = 0;
    while ($row = mysql_fetch_array($get_projects, MYSQL_ASSOC)) {
        if (strlen($row['title']) > 40) {
            $project_title = substr($row['title'], 0, 37) . "...";
        } else {
            $project_title = $row['title'];
        }
        $project_ids[$nprojects] = $row['project_id'];
        $projects[$nprojects] = $project_title;
        $nprojects++;
    }
    mysql_free_result($get_projects);
    mysql_close($conn);
    printf("<script language='JavaScript'>\n");
    printf("var project_ids = new Array(%d);\n", $nprojects);
    printf("var projects = new Array(%d);\n", $nprojects);
    for ($i = 0; $i < $nprojects; $i++) {
        printf("project_ids[$i] = '$project_ids[$i]';\n");
        printf("projects[$i] = '$projects[$i]';\n");
    }
    printf("</script>\n");

    // Create JavaScript array of active duties
    $conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
        or die ("Cannot connect to database. " . mysql_error() . "\n<br>");
    mysql_select_db (DB_DATABASE);
    $get_duties = mysql_query("select duty_id, title from duties " .
        "where staff_assigned = \"$staff\" " .
        "and status = 'Active' " .
        "order by date_entered desc ");
    $duty_ids = array();
    $duties = array();
    $nduties = 0;
    while ($row = mysql_fetch_array($get_duties, MYSQL_ASSOC)) {
        if (strlen($row['title']) > 40) {
            $duty_title = substr($row['title'], 0, 37) . "...";
        } else {
            $duty_title = $row['title'];
        }
        $duty_ids[$nduties] = $row['duty_id'];
        $duties[$nduties] = $duty_title;
        $nduties++;
    }
    mysql_free_result($get_duties);
    mysql_close($conn);
    printf("<script language='JavaScript'>\n");
    printf("var duty_ids = new Array(%d);\n", $nduties);
    printf("var duties = new Array(%d);\n", $nduties);
    for ($i = 0; $i < $nduties; $i++) {
        printf("duty_ids[$i] = '$duty_ids[$i]';\n");
        printf("duties[$i] = '$duties[$i]';\n");
    }
    printf("</script>\n");

    return;
}

function make_pdlist($staff, $id, $project, $projects, $project_ids, 
    $duty, $duties, $duty_ids, $breakflag) {

    if ($id) {
        $suffix = "_" . $id;
    } else {
        $suffix = "";
    }
    
    $nprojects = count($projects);
    $nduties = count($duties);
    
    if ($row['project_id']) {
        $pstyle = "style='font-weight: bold' ";
        $dstyle = "style='font-weight: normal' ";
    } else if ($row['duty_id']) {
        $pstyle = "style='font-weight: normal' ";
        $dstyle = "style='font-weight: bold' ";
    } else {
        $pstyle = "style='font-weight: bold' ";
        $dstyle = "style='font-weight: normal' ";
    }
    
    // Project/Duty selection
    $list = "<font id='projectlabel$suffix' onclick='pdflag$suffix = \"Project\"; " .
        "populate_projects( $(\"pdlist$suffix\"), $(\"projectlabel$suffix\"), " .
        "$(\"dutylabel$suffix\"), project_ids, projects, \"$project\");' $pstyle>";
    $list .= "Project</font> / ";
    $list .= "<font id='dutylabel$suffix' onclick='pdflag$suffix = \"Duty\"; " .
        "populate_duties( $(\"pdlist$suffix\"), $(\"projectlabel$suffix\"), " .
        "$(\"dutylabel$suffix\"), duty_ids, duties, \"$duty\");' $dstyle>Duty</font>";
    if ($breakflag) {
        $list .= "<br>\n";
    } else {
        $list .= "\n";
    }
    $list .= "<select id='pdlist$suffix' size='1' " .
        "onChange=\"if (pdflag$suffix == 'Project') {duty = 0; " .
        "project = project_ids[$('pdlist$suffix').selectedIndex - 1];} " .
        "else {project = 0; duty = duty_ids[$('pdlist$suffix').selectedIndex - 1];}\" >\n";
        
    if ($project) {
        $list .= "<option value='P0'></option>\n";
        for ($i = 0; $i < $nprojects; $i++) {
            if ($project == $project_ids[$i]) {
                $list .= "<option value='P" . $project_ids[$i] . 
                    "' selected>" . $projects[$i] . "</option>\n";
            } else {
                $list .= "<option value='P" . $project_ids[$i] . 
                    "'>" . $projects[$i] . "</option>\n";
            }
        }
    } else if ($duty) {
        $list .= "<option value='D0'></option>\n";
        for ($i = 0; $i < $nduties; $i++) {
            if ($duty == $duty_ids[$i]) {
                $list .= "<option value='D" . $duty_ids[$i] . 
                    "' selected>" . $duties[$i] . "</option>\n";
            } else {
                $list .= "<option value='D" . $duty_ids[$i] . 
                    "'>" . $duties[$i] . "</option>\n";
            }
        }
    } else {
        $list .= "<option value='P0'></option>\n";
        for ($i = 0; $i < $nprojects; $i++) {
            if ($project == $project_ids[$i]) {
                $list .= "<option value='P" . $project_ids[$i] . 
                    "' selected>" . $projects[$i] . "</option>\n";
            } else {
                $list .= "<option value='P" . $project_ids[$i] . 
                    "'>" . $projects[$i] . "</option>\n";
            }
        }
    }
    
    $list .= "</select>\n";

    return $list;
}

?>
