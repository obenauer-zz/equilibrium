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

// Check that authentication has been passed already
session_start();
if (!session_is_registered("SESSION")) {

    // No session established -- exit
    header("Location: index.php?error=2");
    exit();
}

// Register session variables
session_register("SESSION_USERID");
session_register("SESSION_USER");
session_register("SESSION_LOGIN");
session_register("SESSION_STAFF");
session_register("SESSION_ADMIN");

?>
