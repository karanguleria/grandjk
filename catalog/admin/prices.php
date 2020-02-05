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
	
    if ($_POST['search_text']) {

	    $search_text = $_POST['search_text'];
        $_SESSION['search_text'] = $search_text;

        if ($search_text) {
            if ($search_text == '0.00') {
                $sql = "SELECT * FROM prices WHERE price = 0";
            } else {
	            $search_text = $admin->prepare_string($search_text);
	            $sql = "SELECT * FROM prices WHERE price = $search_text";
            }
        }

    } else if ($_POST['sub_category_id']) {

	    $sub_category_id = $_POST['sub_category_id'];
        $_SESSION['sub_category_id'] = $sub_category_id;
        $sql = "SELECT * FROM prices WHERE sub_category_id = $sub_category_id";
    
        if ($_POST['colour_id']) {
    	    $colour_id = $_POST['colour_id'];
            $sql .= " AND colour_id = $colour_id";	
        }

    } else if ($_POST['colour_id']) {

	    $colour_id = $_POST['colour_id'];
        $_SESSION['colour_id'] = $colour_id;
	    $sql = "SELECT * FROM prices WHERE colour_id = $colour_id";

        if ($_POST['sub_category_id']) {
	        $sub_category_id = $_POST['sub_category_id'];
            $sql .= " AND sub_category_id = $sub_category_id";
        }
    }

} else if (isset($_GET['sid'])) { //looking by sub-category

	$sub_category_id = $_GET['sid'];
    $_SESSION['sub_category_id'] = $sub_category_id;
	$sql = "SELECT * FROM prices WHERE sub_category_id = $sub_category_id";
	
	//get sub-category name
	$sub_category_sql = "SELECT sub_category FROM sub_categories WHERE sub_category_id = $sub_category_id";
	$sub_category_result = $admin->get_row($sub_category_sql);
	$this_sub_category = stripslashes($sub_category_result['sub_category']);

} else if (isset($_GET['cid'])) { //looking by colour

	$colour_id = $_GET['cid'];
    $_SESSION['colour_id'] = $colour_id;
	$sql = "SELECT * FROM prices WHERE colour_id = $colour_id";
	
	//get colour
	$colour_sql = "SELECT colour from colours WHERE colour_id = $colour_id";
	$colour_result = $admin->get_row($colour_sql);
	$colour = stripslashes($colour_result['colour']);

} else if (isset($_GET['c'])) { //clear search

    $sql = "SELECT * FROM prices ORDER BY colour_id, sub_category_id";

} else {

    if (isset($_SESSION['sql'])) {
        $sql = $_SESSION['sql'];
    } else {
        $sql = "SELECT * FROM prices ORDER BY colour_id, sub_category_id";        
    }

    if (isset($_SESSION['sub_category_id'])) $sub_category_id = $_SESSION['sub_category_id'];
    if (isset($_SESSION['colour_id'])) $colour_id = $_SESSION['colour_id'];
    if (isset($_SESSION['search_text'])) $search_text = $_SESSION['search_text'];
}

$_SESSION['sql'] = $sql;

if(!isset($_GET['page'])){ 
	$page = 1; 
} else if (isset($_SESSION['page'])) {
    $page = $_SESSION['page'];
} else { 
	$page = $_GET['page']; 
} 

$_SESSION['page'] = $page;

$max_results = 300; 
$from = (($page * $max_results) - $max_results); 

$no_prices = $admin->get_num_records($sql);
$total_pages = ceil($no_prices / $max_results); 

if ($page > 1){ 
	$prev = ($page - 1); 
	$prev_link = "<a href=\"".$_SERVER['PHP_SELF']."?page=$prev";
	if ($sub_category_id) $prev_link .= "&sid=$sub_category_id";
	if ($colour_id) $prev_link .= "&cid=$colour_id";
	$prev_link .= "\" class=\"btn\"><i class=\"icon-backward\"></i> Previous</a>"; 
} 

if($page < $total_pages){ 
	$next = ($page + 1); 
	$next_link .= "<a href=\"".$_SERVER['PHP_SELF']."?page=$next";
	if ($sub_category_id) $next_link .= "&sid=$sub_category_id";
	if ($colour_id) $next_link .= "&cid=$colour_id";
	$next_link .= "\" class=\"btn\">Next <i class=\"icon-forward\"></i></a>"; 
}

$sql .= " LIMIT $from, $max_results";
if ($no_prices > 0) $prices = $admin->get_array($sql);

if (isset($_GET['update'])) {
	$msg = '<div class="alert alert-success">Price updated!</div>';
} else if (isset($_GET['add'])) {
	$msg = '<div class="alert alert-success">Price added!</div>';	
} else if (isset($_GET['delete'])) {
	$msg = '<div class="alert alert-success">Price deleted!</div>';	
}

//get colours
$colours_sql = 'SELECT * FROM colours';
$colours = $admin->get_assoc_array($colours_sql,'colour_id','colour');
$colours_select = $admin->get_select($colours_sql,'colour_id','colour',$colour_id);

//get sub-categories
$sub_categories_sql = 'SELECT * FROM sub_categories';
$sub_categories = $admin->get_array($sub_categories_sql);

$sub_categories_select = '';
foreach ($sub_categories as $sub_category) {
	$sub_categories[$sub_category['sub_category_id']] = stripslashes($sub_category['id']) . ' - ' . stripslashes($sub_category['sub_category']);
	$sub_categories_select .= '<option value="'.$sub_category['sub_category_id'].'"';
    if ($sub_category_id && $sub_category_id == $sub_category['sub_category_id']) $sub_categories_select .= ' selected';
    $sub_categories_select .= '>' . stripslashes($sub_category['id']) . ' - ' . stripslashes($sub_category['sub_category']) . '</option>';
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

<h1>Prices<?php if ($this_sub_category) echo " for $this_sub_category Sub-Category";?><?php if ($colour) echo " for colour $colour";?></h1>
<form class="form-search pull-right" method="post" action="prices.php">
<div class="input-append">
<select name="colour_id">
    <option value="">Color</option>
    <?php echo $colours_select;?>
</select>
<select name="sub_category_id">
    <option value="">Sub-Category</option>
    <?php echo $sub_categories_select;?>
</select>
<button class="btn" type="submit" name="search" value="Search"><i class="icon-search"></i> Search</button>
</div>
</form>
<form class="form-search pull-right" method="post" action="prices.php">
<div class="input-append">
<input class="search-query input-xxlarge"  type="text" name="search_text" placeholder="0.00 (no dollar sign)" value="<?php if (isset($search_text)) echo stripslashes($search_text);?>">
<button class="btn" type="submit" name="search" value="Search"><i class="icon-search"></i> Search Price</button>
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

<a class="btn btn-primary" href="prices_add.php<?php if ($sub_category_id) echo '?sid='.$sub_category_id;?><?php if ($colour_id) echo '?cid='.$colour_id;?>"><i class="icon-plus-sign icon-white"></i> Add new price</a>

<br /><br />
<table class="table table-striped table-hover">
<thead>
	<tr>
        <th width="25%">SR.NO.</th>
        <th width="25%">Color</th>
        <th width="25%">Sub-Category</th>
        <th width="13%">Price</th>
        <th width="12%">Quantity</th>
		<th width="25%">Muliplier Applicable</th>
    	<th width="25%">&nbsp;</th>
    </tr>
</thead>

<?php
if (is_array($prices)) {
	foreach ($prices as $K=>$price) {
		$srno=$K+1;
		echo '<tr>
                <td valign="top">'.$srno.'</td>
		<td valign="top">'.stripslashes($colours[$price['colour_id']]).'</td>
		<td valign="top">'.stripslashes($sub_categories[$price['sub_category_id']]).'</td>
		<td valign="top">$'.$price['price'].'</td>
                    <td valign="top">'.$price['quantity'].'</td>
		<td valign="top">'.$price['applicable'].'</td>
		
		<td class="tdtextalignright" valign="top" align="center">
		<a class="btn btn-success" href="prices_edit.php?action=edit&id='.$price['price_id'].'"><i class="icon-edit icon-white"></i> Edit</a>  
		<a class="btn btn-danger" href="prices_edit.php?action=delete&id='.$price['price_id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="icon-trash icon-white"></i> Delete</a>
		</td>
		</tr>';
	}
} else {
	echo '<tr class="error"><td colspan="4" align="center">No prices</td></tr>';
}
?>
</table>
</div>
</body>
</html>
