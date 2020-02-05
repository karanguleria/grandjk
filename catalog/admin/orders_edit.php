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

$cart_id = $_REQUEST['id'];

$action = $_GET['action'];	

if ($action == 'delete') {

    $admin->delete('cart', 'cart_id', $cart_id);		
    $admin->delete('cartitems', 'cart_id', $cart_id);		
			
	header("Location: orders.php?delete");
	exit();

} else {

    $sql = "SELECT * FROM cart WHERE cart_id = $cart_id";
    $cart = $admin->get_row($sql);

    $items_sql = "SELECT * FROM cartitems WHERE cart_id = $cart_id";
    $items = $admin->get_array($items_sql);

    $customer_sql = "SELECT * FROM customers WHERE customer_id = ".$cart['customer_id'];
    $customer = $admin->get_row($customer_sql);

	$sub_categories_sql = 'SELECT * FROM sub_categories';
	$sub_categories_result = $admin->get_array($sub_categories_sql);

	foreach ($sub_categories_result as $sub_category) {
		$sub_categories[$sub_category['sub_category_id']] = stripslashes($sub_category['id']) . ' - ' . stripslashes($sub_category['sub_category']);
	}

	$colours_sql = 'SELECT * FROM colours';
	$colours = $admin->get_assoc_array($colours_sql,'colour_id','colour');
    
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
<h1>View order</h1>

<table class="table table-striped table-hover">
<tr>
	<td align="left" width="70%"><strong>Items</strong></td>
	<td align="center" width="10%"><strong>Quantity</strong></td>
	<td align="right" width="10%"><strong>Price</strong></td>
</tr>
<?php 
foreach ($items as $cart_item) { ?>
	<tr>
		<td width="70%"><?php echo $sub_categories[$cart_item['sub_category_id']] . ' - ' . $colours[$cart_item['colour_id']];?></td>
		<td width="10%" align="center"><?php echo $cart_item['qty'];?></td>
		<td width="10%" align="right"><?php echo '$'.number_format($cart_item['total'],2);?></td>
	</tr>
<?php
} ?>
<tr>
    <td width="70%">&nbsp;</td>
    <td width="10%"><strong>Subtotal</strong></td>
    <td width="10%" align="right"><strong><?php echo '$'.number_format($cart['subtotal'],2);?></strong></td>
</tr>
<tr>
    <td width="70%">&nbsp;</td>
    <td width="10%"><strong>Discount</strong></td>
    <td width="10%" align="right"><strong><?php echo '$'.number_format($cart['discount'],2);?></strong></td>
</tr>
<tr>
    <td width="70%">&nbsp;</td>
    <td width="10%"><strong>Total</strong></td>
    <td width="10%" align="right"><strong><?php echo '$'.number_format($cart['total'],2);?></strong></td>
</tr>
<tr>
	<td colspan="3"><strong>Customer:</strong></td>
</tr>
<tr>
    <td colspan="3">
   		<?php echo 'Name: '.stripslashes(htmlentities($customer['first_name'])) . ' ' . stripslashes(htmlentities($customer['last_name']));?><br />
		<?php echo 'Email: '.stripslashes(htmlentities($customer['email']));?> <br />
		<?php echo 'Tel: '.stripslashes(htmlentities($customer['tel']));?> <br />
		<?php echo 'Address:'.stripslashes(htmlentities($customer['address'])).', '.stripslashes(htmlentities($customer['city'])).', '.stripslashes(htmlentities($customer['state'])).' '.stripslashes(htmlentities($customer['zip'])).' '.$customer['country'];?><br />
		<?php echo 'Job #: '.stripslashes(htmlentities($cart['job_no']));?> <br />
		<?php echo 'JK Rep: '.stripslashes(htmlentities($customer['rep']));?> <br />
		<?php echo 'Assembly: '.stripslashes(htmlentities($cart['assembly']));?> <br />
		<?php echo 'Touch Up Kit: '.stripslashes(htmlentities($cart['touchup']));?> <br />
		<?php echo 'Shipping Method: '.stripslashes(htmlentities($cart['shipping']));?> <br />
		<?php echo 'Expected Date: '.stripslashes(htmlentities($cart['delivery']));?> <br />		
		<?php if ($customer['notes']) echo 'Notes: ' . stripslashes(htmlentities($cart['notes']));?>
    </td>
</tr>
</table>
<a class="btn btn-inverse pull-right" href="orders.php">Cancel</a>

</div>
</body>
</html>
