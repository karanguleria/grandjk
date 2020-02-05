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

if (isset($_POST['add'])) { 
	
	$new_category = $_POST;
	
	$category = $admin->prepare_string($new_category['category']);
	
	//check not already in
	$check_sql = "SELECT * FROM categories WHERE category = '$category'";
	$check = $admin->get_num_records($check_sql);
	
	if ($check <= 0) { //not in, add
		$insert = array('category' => $admin->prepare_string($new_category['category']),'state'=>$new_category['category_type']);

		//add to database
		$admin->insert_array('categories', $insert);

		header("Location: categories.php?add");
		exit();
	
	} else {

		$error = '<div class="alert alert-danger">That category is already in the database!</div>';

	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="robots" content="noindex,nofollow">

<?php include("metatags.php");?>

<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />

</head>
<body>
<?php include("menu.php"); ?>

<div class="container well">
<h1>Add new category</h1>

<?php if ($error) echo $error;?>

<form method="post" action="categories_add.php">

<table class="table table-striped table-hover">
<tr>
	<td><strong>Category:</strong></td>
    <td align="left">
        <span id="sprytextfield1">
        <input type="text" name="category" value="<?php if ($new_category['category']) echo stripslashes(htmlentities($new_category['category']));?>" /> 
        </span>
    </td>
</tr>

<tr>
	<td><strong>Type:</strong></td>
    <td align="left">
        <span id="spryselect3">
		<select name="category_type">
                   <option value="">Select Category Type</option>
                   <option value="1">Default</option>
		    
		   <option value="2">Hardware</option>
		   <option value="3">Touch up sample part</option>
		   <option value="4">Clearance</option>
		   
		   
         </select>
        </span>
    </td>
</tr>

</table>
<a class="btn btn-inverse pull-right" href="categories.php">Cancel</a><input class="btn btn-primary" type="submit" name="add" value="Save new category"> 
</form>
<script type="text/javascript">
<!--
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
	var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3");
//-->
</script>
</div>
</body>
</html>
