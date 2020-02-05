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

$sql = "SELECT * FROM categories ORDER BY category";        
$no_categories = $admin->get_num_records($sql);
if ($no_categories > 0) $categories = $admin->get_array($sql);

if (isset($_GET['update'])) {
	$msg = '<div class="alert alert-success">Category updated!</div>';
} else if (isset($_GET['add'])) {
	$msg = '<div class="alert alert-success">Category added!</div>';	
} else if (isset($_GET['delete'])) {
	$msg = '<div class="alert alert-success">Category deleted!</div>';	
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

<h1>Categories</h1>
<a class="btn btn-primary" href="categories_add.php"><i class="icon-plus-sign icon-white"></i> Add new category</a>

<br /><br />
<table class="table table-striped table-hover">
<thead>
	<tr>
        <th width="50%">Category</th>
        <th width="25%">Type</th>
    	<th width="25%">&nbsp;</th>
    </tr>
</thead>

<?php
if (is_array($categories)) {
	foreach ($categories as $category) {

                if($category['state'] == '1')
                {
                   $type = 'default';
                }
                elseif($category['state'] == '2')
                {
                   $type = 'Hardware';
                }
                elseif($category['state'] == '3')
                {
                   $type = 'Touch up sample part';
                }
                elseif($category['state'] == '4')
                {
                   $type = 'Clearance';
                }
		echo '<tr>
		<td valign="top">'.htmlentities(stripslashes($category['category'])).'</td>
                <td valign="top">'.$type.'</td>
		<td class="tdtextalignright" valign="top" align="center">
		<a class="btn btn-success" href="categories_edit.php?action=edit&id='.$category['category_id'].'"><i class="icon-edit icon-white"></i> Edit</a>  
		<a class="btn btn-danger" href="categories_edit.php?action=delete&id='.$category['category_id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="icon-trash icon-white"></i> Delete</a>
		<a class="btn btn-info" href="sub_categories.php?cid='.$category['category_id'].'"><i class="icon-list icon-white"></i> Sub-categories</a>  
		</td>
		</tr>';
	}
} else {
	echo '<tr class="error"><td colspan="2" align="center">No categories</td></tr>';
}
?>
</table>
</div>
</body>
</html>
