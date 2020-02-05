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
	
	$new_sub_category = $_POST;
	
	$category_id = $new_sub_category['category_id'];
	$id = $admin->prepare_string($new_sub_category['id']);
	$sub_category = $admin->prepare_string($new_sub_category['sub_category']);
	
	//check not already in
	$check_sql = "SELECT * FROM sub_categories WHERE sub_category = '$sub_category' AND id = '$id' AND category_id = $category_id";
	$check = $admin->get_num_records($check_sql);
	
	if ($check <= 0) { //not in, add
	
		$insert = array('category_id' => $category_id, 'id' => $id, 'sub_category' => $sub_category);

		//add to database
		$admin->insert_array('sub_categories', $insert);

		header("Location: sub_categories.php?add");
		exit();
	
	} else {

		$error = '<div class="alert alert-danger">That sub-category is already in the database!</div>';

	}

} else if (isset($_GET['cid'])) { //adding a sub-category to a particular category

	$category_id = $_GET['cid'];

}

//get categories
$categories_sql = 'SELECT * FROM categories';
$categories = $admin->get_select($categories_sql,'category_id','category',$category_id);
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
<h1>Add new sub-category</h1>

<?php if ($error) echo $error;?>

<form method="post" action="sub_categories_add.php">

<table class="table table-striped table-hover">
<tr>
	<td><strong>Category:</strong></td>
    <td align="left">
        <span id="spryselect1">
        <select name="category_id">
        	<option value="">Please select</option>
        	<?php echo $categories;?>
        </select>
        </span>
    </td>
</tr>
<tr>
	<td><strong>ID:</strong></td>
    <td align="left">
        <span id="sprytextfield1">
        <input type="text" name="id" value="<?php if ($new_sub_category['id']) echo stripslashes(htmlentities($new_sub_category['id']));?>" /> 
        </span>
    </td>
</tr>
<tr>
	<td><strong>Sub-Category:</strong></td>
    <td align="left">
        <span id="sprytextfield2">
        <input type="text" name="sub_category" value="<?php if ($new_sub_category['sub_category']) echo stripslashes(htmlentities($new_sub_category['category']));?>" /> 
        </span>
    </td>
</tr>

</table>
<a class="btn btn-inverse pull-right" href="sub_categories.php">Cancel</a><input class="btn btn-primary" type="submit" name="add" value="Save new sub-category"> 
</form>
<script type="text/javascript">
<!--
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
	var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
	var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
//-->
</script>
</div>
</body>
</html>
