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

$sql = "SELECT * FROM colours";        
$no_colours = $admin->get_num_records($sql);
if ($no_colours > 0) $colours = $admin->get_array($sql);

if (isset($_GET['update'])) {
	$msg = '<div class="alert alert-success">Color updated!</div>';
} else if (isset($_GET['add'])) {
	$msg = '<div class="alert alert-success">Color added!</div>';	
} else if (isset($_GET['delete'])) {
	$msg = '<div class="alert alert-success">Color deleted!</div>';	
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
<meta name="robots" content="noindex,nofollow">
<?php include("metatags.php");?>

</head>
<body>
<?php include("menu.php"); ?>

<div class="container well">

<?php if (isset($msg)) echo $msg;?>

<h1>Colors</h1>

<br /><br />

<a class="btn btn-primary" href="colours_add.php"><i class="icon-plus-sign icon-white"></i> Add new color</a>

<br /><br />
<table class="table table-striped table-hover">
<thead>
	<tr>
        <th width="50%">Color</th>
    	<th width="50%">&nbsp;</th>
    </tr>
</thead>

<?php
if (is_array($colours)) {
	foreach ($colours as $colour) {
		echo '<tr>
		<td valign="top">'.stripslashes($colour['colour']).'</td>
		<td class="tdtextalignright" valign="top" align="center">
		<a class="btn btn-success" href="colours_edit.php?action=edit&id='.$colour['colour_id'].'"><i class="icon-edit icon-white"></i> Edit</a>  
		<a class="btn btn-danger" href="colours_edit.php?action=delete&id='.$colour['colour_id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="icon-trash icon-white"></i> Delete</a>
		<a class="btn btn-info" href="prices.php?cid='.$colour['colour_id'].'"><i class="icon-list icon-white"></i> Prices</a>  
		</td>
		</tr>';
	}
} else {
	echo '<tr class="error"><td colspan="3" align="center">No colours</td></tr>';
}
?>
</table>
</div>
</body>
</html>
