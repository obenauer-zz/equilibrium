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


// Start HTML header
printf("<html><head>\n");
printf("<title>%s Projects Database</title>\n", $organization_name);

// Load JavaScript files
printf("<script src='external/prototype.js' type='text/javascript'> </script>\n");
printf("<script src='external/scriptaculous.js' type='text/javascript'> </script>\n");
printf("<script src='external/calendarpopup.js' type='text/javascript'> </script>\n");
printf("<script src='equilibrium.js' type='text/javascript'> </script>\n");
printf("<!--[if lt IE 7.]>\n");
printf("<script defer type=\"text/javascript\" src=\"external/pngfix.js\"></script>\n");
printf("<![endif]-->\n");

// Load style sheets
printf("<link href='equilibrium.css' rel='stylesheet' type='text/css'>\n");
printf("<link href='external/calendar-blue.css' rel='stylesheet' type='text/css' media='all' title='blue'>\n");
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
if ($activepage == "ToDo") {
    printf("<td bgcolor='$heading_color'><b><a href='todolist.php'>To Do</a></b></td>\n");
} else {
    printf("<td><b><a href='todolist.php'>ToDo</a></b></td>\n");
}
if ($activepage == "Log") {
    printf("<td bgcolor='$heading_color'><b><a href='logbook.php'>Log</a></b></td>\n");
} else {
    printf("<td><b><a href='logbook.php'>Log</a></b></td>\n");
}
if ($activepage == "Files") {
    printf("<td bgcolor='$heading_color'><b><a href='files.php'>Files</a></b></td>\n");
} else {
    printf("<td><b><a href='files.php'>Files</a></b></td>\n");
}
//if ($activepage == "Reports") {
//    printf("<td bgcolor='$heading_color'><b><a href='reports.php'>Reports</a></b></td>\n");
//} else {
//    printf("<td><b><a href='reports.php'>Reports</a></b></td>\n");
//}
if ($activepage == "Users") {
    printf("<td bgcolor='$heading_color'><b><a href='users.php'>Users</a></b></td>\n");
} else {
    printf("<td><b><a href='users.php'>Users</a></b></td>\n");
}
printf("</tr></table>\n");

?>




