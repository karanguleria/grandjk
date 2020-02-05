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

if (isset($_POST['search'])) {
	
	$search_text = $_POST['search_text'];

    if ($search_text) {
	    $search_text = $admin->prepare_string($search_text);
	    $sql = "SELECT * FROM sub_categories WHERE id LIKE '%$search_text%' OR sub_category LIKE '%$search_text%'";
    }

} else if (isset($_GET['cid'])) { //looking by category

	$category_id = $_REQUEST['cid'];
	$sql = "SELECT * FROM sub_categories WHERE category_id = $category_id";
	$category_sql = "SELECT category FROM categories WHERE category_id = $category_id";
	$category_result = $admin->get_row($category_sql);
	$category = stripslashes($category_result['category']);
	
} else {

    $sql = "SELECT * FROM sub_categories";        

}

if(!isset($_GET['page'])){ 
	$page = 1; 
} else { 
	$page = $_GET['page']; 
} 

$max_results = 20; 
$from = (($page * $max_results) - $max_results); 

$no_sub_categories = $admin->get_num_records($sql);
$total_pages = ceil($no_sub_categories / $max_results); 

if ($page > 1){ 
	$prev = ($page - 1); 
	$prev_link = "<a href=\"".$_SERVER['PHP_SELF']."?page=$prev";
	if ($category_id) $prev_link .= "&cid=$category_id";
	$prev_link .= "\" class=\"btn\"><i class=\"icon-backward\"></i> Previous</a>"; 
} 

if($page < $total_pages){ 
	$next = ($page + 1); 
	$next_link .= "<a href=\"".$_SERVER['PHP_SELF']."?page=$next";
	if ($category_id) $next_link .= "&cid=$category_id";	
	$next_link .= "\" class=\"btn\">Next <i class=\"icon-forward\"></i></a>"; 
}

$sql .= " LIMIT $from, $max_results";
if ($no_sub_categories > 0) $sub_categories = $admin->get_array($sql);

if (isset($_GET['update'])) {
	$msg = '<div class="alert alert-success">Sub-Category updated!</div>';
} else if (isset($_GET['add'])) {
	$msg = '<div class="alert alert-success">Sub-Category added!</div>';	
} else if (isset($_GET['delete'])) {
	$msg = '<div class="alert alert-success">Sub-Category deleted!</div>';	
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

<h1>Sub-Categories<?php if ($category) echo " for $category Category";?></h1>
<form class="form-search pull-right" method="post" action="sub_categories.php">
<div class="input-append">
<input class="search-query input-xxlarge"  type="text" name="search_text"  value="<?php if (isset($search_text)) echo stripslashes($search_text);?>">
<button class="btn" type="submit" name="search" value="Search"><i class="icon-search"></i> Search</button>
</div>
</form>
<?php
if ($prev_link != '' && $next_link != '') {
	echo "<br /><br />$prev_link $next_link";
} else if ($prev_link != '') {
	echo "<br /><br />$prev_link";
} else if ($next_link != '') {
	echo "<br /><br />$next_link";
}
?>
<br /><br />

<a class="btn btn-primary" href="sub_categories_add.php<?php if ($category_id) echo '?cid='.$category_id;?>"><i class="icon-plus-sign icon-white"></i> Add new sub-category</a>

<br /><br />
<table class="table table-striped table-hover">
<thead>
	<tr>
        <th width="75%">Sub-Category</th>
    	<th width="25%">&nbsp;</th>
    </tr>
</thead>

<?php
if (is_array($sub_categories)) {
	foreach ($sub_categories as $sub_category) {
		echo '<tr>
		<td valign="top">'.stripslashes($sub_category['id']) . ' - ' . stripslashes($sub_category['sub_category']).'</td>
		<td class="tdtextalignright" valign="top" align="center">
		<a class="btn btn-success" href="sub_categories_edit.php?action=edit&id='.$sub_category['sub_category_id'].'"><i class="icon-edit icon-white"></i> Edit</a>  
		<a class="btn btn-danger" href="sub_categories_edit.php?action=delete&id='.$sub_category['sub_category_id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="icon-trash icon-white"></i> Delete</a>
		<a class="btn btn-info" href="prices.php?sid='.$sub_category['sub_category_id'].'"><i class="icon-list icon-white"></i> Prices</a>  
		</td>
		</tr>';
	}
} else {
	echo '<tr class="error"><td colspan="2" align="center">No sub-categories</td></tr>';
}
?>
</table>
</div>
</body>
</html>
