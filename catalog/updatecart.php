<?php
session_start();

/*
J & K Cabinetry
Created by Jill Atkins (admin@cybril.com)
March 2017
*/

include('grandclass.php');
$grandclass = new grandclass;

if (isset($_POST['submit'])) { //want to checkout
    if (isset($_POST['multiplier']) && $_POST['multiplier'] != '') {
        $multiplier = $_POST['multiplier'];
        $_COOKIE['multiplier'] = $multiplier;
        setcookie("multiplier", $multiplier, time() + (86400 * 30), "/");
    }
	header("Location:checkout");
	exit();
} else if (isset($_POST['submit1'])) { //want to continue shopping
	header("Location: store");
	exit();
} else if (isset($_POST['submit2'])) { //want to update quantity
	$update = true;
	$qty = $_POST['qty']; //array
} else if (isset($_GET['id'])) { //want to delete an item
	$cart_item_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);  
	$delete = true;
}

$cart_id = $_COOKIE['cart_id'];

if (!$cart_id) {
	header("Location:store");
	exit();    
}

if (isset($update)) {

	$query = "SELECT * FROM cartitems WHERE cart_id = $cart_id";
	$cart_items = $grandclass->get_array($query);
	
	foreach ($cart_items as $cart_item) {
		if ($qty[$cart_item['cartitem_id']]) {
			$new_qty = $qty[$cart_item['cartitem_id']];
			$price = $cart_item['price'];
			$new_total = $new_qty * $price;
			//update cart
			$update_details = array('qty' => $new_qty, 'total' => $new_total);
			$update = $grandclass->update_array('cartitems', 'cartitem_id', $cart_item['cartitem_id'], $update_details);
		}
	}

} else if ($delete) {

	$delete = $grandclass->delete('cartitems', 'cartitem_id', $cart_item_id);

}

header("location:basket/?update");
exit();

?>