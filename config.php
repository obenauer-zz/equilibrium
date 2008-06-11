<?php
# This is the main configuration file for the website and database.

// Project information
$organization_name = "Example Group";
$background_color = '#F0F0FF';
$heading_color = '#C0C0FF';
$admin_name = "Joe Sysadmin";
$admin_email = "joe.sysadmin@company.com";

// Configuration settings
$main_page = "todolist.php";
$upload_dir = "datafiles";
$number_icons = 35712;

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
