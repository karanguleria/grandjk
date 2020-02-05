<?php
session_start();

/*
J & K Cabinetry
Created by Jill Atkins (admin@cybril.com)
March 2017
*/

session_destroy();
header("Location: login.php?logout");
exit();

?>
