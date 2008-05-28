<?php

// Start HTML header
printf("<html><head>\n");
printf("<title>%s Projects Database</title>\n", $organization_name);

// Load JavaScript files
printf("<script src='scripts/prototype.js' type='text/javascript'> </script>\n");
printf("<script src='scripts/scriptaculous.js' type='text/javascript'> </script>\n");
printf("<script src='scripts/calendarpopup.js' type='text/javascript'> </script>\n");
printf("<script src='scripts/equilibrium.js' type='text/javascript'> </script>\n");
printf("<!--[if lt IE 7.]>\n");
printf("<script defer type=\"text/javascript\" src=\"scripts/pngfix.js\"></script>\n");
printf("<![endif]-->\n");

// Load style sheets
printf("<link href='css/equilibrium.css' rel='stylesheet' type='text/css'>\n");
printf("<link href='css/calendar-blue.css' rel='stylesheet' type='text/css' media='all' title='blue'>\n");
printf("</head>\n");

// Start page body
printf("<body bgcolor='%s'>\n", $background_color);
printf("<table width=100%% halign='center'>\n");
printf("<tr><td><img src='images/equilibrium_logo.png'></td>");
printf("<td><b><font size='3' color='blue'>$organization_name</font></b></td>\n");
printf("<td align='right'><b><font size='2'>User: </font>" .
    "<font color=blue size='2'>%s</font></b> &nbsp; &nbsp; " .
    "<b><font color=blue size='2'><a href='index.php?cmd=logout'>Log out</a>" .
    "</font></b> &nbsp; &nbsp;</td></tr></table>\n", $_SESSION['SESSION_USER']);
printf("<table align='right' frame=border rules=top border=0 bgcolor='$background_color' cellpadding=5><tr>\n");

if (!isset($activepage)) {
    $activepage = "";
}

if ($activepage == "ToDo") {
    printf("<td bgcolor='$heading_color'><b><a href='todolist.php'>To Do</a></b></td>\n");
} else {
    printf("<td><b><a href='todolist.php'>ToDo</a></b></td>\n");
}
if ($activepage == "Projects") {
    printf("<td bgcolor='$heading_color'><b><a href='projects.php'>Projects</a></b></td>\n");
} else {
    printf("<td><b><a href='projects.php'>Projects</a></b></td>\n");
}
if ($activepage == "Duties") {
    printf("<td bgcolor='$heading_color'><b><a href='duties.php'>Duties</a></b></td>\n");
} else {
    printf("<td><b><a href='duties.php'>Duties</a></b></td>\n");
}
if ($activepage == "Log") {
    printf("<td bgcolor='$heading_color'><b><a href='logbook.php'>Log</a></b></td>\n");
} else {
    printf("<td><b><a href='logbook.php'>Log</a></b></td>\n");
}
//if ($activepage == "Calendar") {
//    printf("<td bgcolor='$heading_color'><b><a href='calendar.php'>Calendar</a></b></td>\n");
//} else {
//    printf("<td><b><a href='calendar.php'>Calendar</a></b></td>\n");
//}
//if ($activepage == "Reports") {
//    printf("<td bgcolor='$heading_color'><b><a href='reports.php'>Reports</a></b></td>\n");
//} else {
//    printf("<td><b><a href='reports.php'>Reports</a></b></td>\n");
//}
if ($activepage == "Files") {
    printf("<td bgcolor='$heading_color'><b><a href='files.php'>Files</a></b></td>\n");
} else {
    printf("<td><b><a href='files.php'>Files</a></b></td>\n");
}
if ($activepage == "Users") {
    printf("<td bgcolor='$heading_color'><b><a href='users.php'>Users</a></b></td>\n");
} else {
    printf("<td><b><a href='users.php'>Users</a></b></td>\n");
}
printf("</tr></table>\n");

?>




