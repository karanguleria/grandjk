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

$sql = "SELECT * FROM cart ORDER BY cart_id DESC";        

if(!isset($_GET['page'])){ 
	$page = 1; 
} else if (isset($_SESSION['orders_page'])) {
    $page = $_SESSION['orders_page'];
} else { 
	$page = $_GET['page']; 
} 

$_SESSION['orders_page'] = $page;

$max_results = 20; 
$from = (($page * $max_results) - $max_results); 

$no_orders = $admin->get_num_records($sql);
$total_pages = ceil($no_prices / $max_results); 

if ($page > 1){ 
	$prev = ($page - 1); 
	$prev_link = "<a href=\"".$_SERVER['PHP_SELF']."?page=$prev\" class=\"btn\"><i class=\"icon-backward\"></i> Previous</a>"; 
} 

if($page < $total_pages){ 
	$next = ($page + 1); 
	$next_link .= "<a href=\"".$_SERVER['PHP_SELF']."?page=$next\" class=\"btn\">Next <i class=\"icon-forward\"></i></a>"; 
}

$sql .= " LIMIT $from, $max_results";
if ($no_orders > 0) $orders = $admin->get_array($sql);

if (isset($_GET['delete'])) {
	$msg = '<div class="alert alert-success">Order deleted!</div>';	
}

//get customers
$customers_sql = 'SELECT * FROM customers';
$customers_result = $admin->get_array($customers_sql);

foreach ($customers_result as $customer) {
	$customers[$customer['customer_id']] = stripslashes($customer['first_name']) . ' ' . stripslashes($customer['last_name']);
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

<h1>Orders</h1>

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

<table class="table table-striped table-hover">
<thead>
	<tr>
        <th width="20%">Date</th>
        <th width="20%">Customer</th>
        <th width="20%">Total</th>
        <th width="20%">Status</th>
    	<th width="20%">&nbsp;</th>
    </tr>
</thead>

<?php
if (is_array($orders)) {
	foreach ($orders as $order) {
		echo '<tr>
		<td valign="top">'.date('m/d/y h:i', strtotime($order['date'])).'</td>
		<td valign="top">'.stripslashes($customers[$order['customer_id']]).'</td>
		<td valign="top">$'.number_format($order['total'],2).'</td>
		<td valign="top">'.$order['status'].'</td>
		<td class="tdtextalignright" valign="top" align="center">
		<a class="btn btn-success" href="orders_edit.php?action=edit&id='.$order['cart_id'].'"><i class="icon-edit icon-white"></i> View</a>  
		<a class="btn btn-danger" href="orders_edit.php?action=delete&id='.$order['cart_id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="icon-trash icon-white"></i> Delete</a>
		</td>
		</tr>';
	}
} else {
	echo '<tr class="error"><td colspan="5" align="center">No orders</td></tr>';
}
?>
</table>
</div>
</body>
</html>
