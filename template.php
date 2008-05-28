<?php
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
$activepage = "";
require("header.php");

// Main functions of page
switch($action) {
    case "";
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

