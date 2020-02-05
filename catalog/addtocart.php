<?php
@session_start();
/*
  J & K Cabinetry
  Created by Jill Atkins (admin@cybril.com)
  March 2017
 */

include('grandclass.php');
$grandclass = new grandclass;

$date = date("Y-m-d H:i:s");
//print_r($_COOKIE);
//echo $_COOKIE['cart_id'];
if (isset($_REQUEST['id'])) { //have added item to cart
    //echo $_REQUEST['id']; //die("dd");
    #print_r($_COOKIE);
    if (isset($_COOKIE['cart_id']) && isset($_COOKIE['cat_id'])) { //have added an item already
        $cart_id = $_COOKIE['cart_id'];
        $cat_id = $_COOKIE['cat_id'];
    } else { //first item
        //add to cart
        $insert_cart_values = array('date' => $date, 'cat_id' => $_REQUEST['cid'], 'status' => 'pending');
        $insert_cart = $grandclass->insert_array('cart', $insert_cart_values);
        $cart_id = $grandclass->get_latest_id();

        $_COOKIE['cat_id'] = $_REQUEST['cid'];

        $_COOKIE['cart_id'] = $cart_id;
        setcookie("cart_id", $cart_id, time() + (86400 * 30), "/"); 
        setcookie("cat_id", $_REQUEST['cid'], time() + (86400 * 30), "/");
    }

    // get the details for this item
    $sub_category_id = $_REQUEST['id'];

    $qty = $_REQUEST['qty'];
    $colour_id = $_REQUEST['cid'];


    $query = "SELECT * FROM prices WHERE sub_category_id = $sub_category_id AND colour_id = $colour_id";
    $row = $grandclass->get_row($query);

    $price = $row['price'];
    $applicable = $row['applicable'];

    //$applicable = $row['applicable'];
    $total = $price * $qty;

    //check if the item is already in the cart
    $pquery = "SELECT * FROM cartitems WHERE sub_category_id = $sub_category_id AND colour_id = $colour_id AND cart_id = $cart_id";
    $ptotalrows = $grandclass->get_num_records($pquery);

    #$ptotalrows;
    #die("ddd");

    if ($ptotalrows <= 0) { // if not in the cart, add
        $insert_item_values = array('cart_id' => $cart_id, 'sub_category_id' => $sub_category_id, 'colour_id' => $colour_id, 'price' => $price, 'qty' => $qty, 'total' => $total, 'applicable' => $applicable);
        $insert_item = $grandclass->insert_array('cartitems', $insert_item_values);
    }

    //get count of items in cart 
    $cart_sql = "SELECT sum(qty) as total FROM cartitems WHERE cart_id = $cart_id";
    $cart_result = $grandclass->get_row($cart_sql);
    $no_cart = $cart_result['total'];
    setcookie("cart_id", $cart_id, time() + (86400 * 30), "/"); 
    echo $no_cart;
}
?> 