<?php 
session_start();

/*
J & K Cabinetry
Created by Jill Atkins (admin@cybril.com)
March 2017
*/

if (!isset($_SESSION['admin'])) { //not logged in
	//go to login
	header("Location: login.php");
	exit();
}

require_once("adminclass.php");
$admin = new admin;

$title = $admin->sitename;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<link rel="stylesheet" href="css/analytics.css">
<?php include("metatags.php");?>
<script type="text/javascript" src="js/protovis.min.js"></script>
<script type="text/javascript" src="js/graphsupport.js"></script>
</head>
<body>
<?php include("menu.php"); ?>

<div class="container well">
  <div>&nbsp;</div>
  <h1>Welcome to the <?php echo $title;?> Dashboard.</h1>
  <p>&nbsp;</p>
  <p>Please make your selection from the menu above.</p>
</div>
</body>
</html>