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

# This is the main configuration file for the website and database.

// Project information
$organization_name = "Example Group";
$background_color = '#F0F0FF';
$heading_color = '#C0C0FF';
$admin_name = "Joe Sysadmin";
$admin_email = "joe.sysadmin@company.com";

// Configuration settings
$main_page = "projects.php";
$upload_dir = "datafiles";
$number_icons = 35712;
$client_organization_label = "Department";
$display_client_contact = "N";

# LDAP connection
$use_ldap = "N";
define("LDAP_SERVER_ADDRESS","");
define("LDAP_PORT","389");
define("LDAP_BASE_DN","");
define("LDAP_USER_NAME","");
define("LDAP_PASSWORD","");

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'equilibrium');

?>
