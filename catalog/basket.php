<?php
session_start();

/*
  J & K Cabinetry
  Created by Jill Atkins (admin@cybril.com)
  March 2017
 */

/*
  if(!is_user_logged_in() ) {
  header("Location: http://www.grandjk.com/welcome-grand-jk-dealer-page/");
  exit();
  }
 */
//print_r($_COOKIE);
//die("asdf");
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift($roles);

if ($role != 'customer' && $role != 'administrator') {
    header("Location: http://www.grandjk.com");
    exit();
}

include('grandclass.php');
$grandclass = new grandclass;

if (!isset($_COOKIE['cart_id'])) { //have turned up somehow
    header("Location: store");
    exit();
} else {

    $cart_id = $_COOKIE['cart_id'];

    $query = "SELECT * FROM cartitems WHERE cart_id = $cart_id";
    $no_cart_items = $grandclass->get_num_records($query);
    if ($no_cart_items > 0)
        $cart_items = $grandclass->get_array($query);

    $sub_categories_sql = 'SELECT * FROM sub_categories';
    $sub_categories_result = $grandclass->get_array($sub_categories_sql);

    foreach ($sub_categories_result as $sub_category) {
        $sub_categories[$sub_category['sub_category_id']] = stripslashes($sub_category['id']) . ' - ' . stripslashes($sub_category['sub_category']);
    }

    $colours_sql = 'SELECT * FROM colours';
    $colours = $grandclass->get_assoc_array($colours_sql, 'colour_id', 'colour');
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> 

<?php
if ($no_cart_items <= 0) {
    echo '<div class="alert alert-info" style="color: #f00">Your cart is empty!<br /></div><div>&nbsp;</div><form name="mycart" method="post" action="/catalog/updatecart.php"><input type="submit" name="submit1" value="Continue shopping"></form><div>&nbsp;</div>';
} else {
    if (isset($_GET['update']))
        echo '<div class="alert alert-success" style="color: #0a7c3a">Cart updated!</div><div>&nbsp;</div>';
    ?>
    <form name="mycart" method="post" action="/catalog/updatecart2.php" class="print-only" id="print_area">
        <table width="100%" border="0" align="center" cellpadding="10" cellspacing="0">
            <tr>
                <td align="left" height="18" width="70%" colspan="2"><strong>Items</strong></td>
                <td align="center" height="18" width="10%"><strong>Quantity</strong></td>
                <td align="right" height="18" width="10%"><strong>Price</strong></td>
                <td align="center" height="18" width="10%"></td>
            </tr>
            <?php
            $cart_total = 0;
            $applicable_total = 0;
            $non_applicable = 0;
//            print_r($cart_items);   
            foreach ($cart_items as $cart_item) {

                $quantity = 0;
                //if (@$_GET["dev"]) {
                $colour_id = $cart_item['colour_id'];
                if ($colour_id == "14") {
                    $sub_category_id = $cart_item['sub_category_id'];
                    $sql = "SELECT quantity FROM prices WHERE sub_category_id = $sub_category_id and colour_id= $colour_id";
                    $sub_result = $grandclass->get_array($sql);
                    // echo "<pre>";  print_r($sub_result);   echo "</pre>";
                    $quantity = $sub_result[0]['quantity'];
                }
                //}
                ?>
                <tr valign=top>
                    <td width="70%" colspan="2">
                        <?php
                        if (@$colours[$cart_item['colour_id']] == "Clearance") {
                            $sql = "SELECT category_id FROM sub_categories WHERE sub_category_id = $sub_category_id";
                            $sub_result = $grandclass->get_array($sql);
                            $category_id = $sub_result[0]['category_id'];
                            $sql = "SELECT category FROM categories WHERE category_id = $category_id";
                            $sub_result = $grandclass->get_array($sql);
                            $category = $sub_result[0]['category'];

                            echo $sub_categories[$cart_item['sub_category_id']] . ' - ' . $colours[$cart_item['colour_id']] . " (" . $category . ")";
                        } else {
                            echo $sub_categories[$cart_item['sub_category_id']] . ' - ' . $colours[$cart_item['colour_id']];
                        }
                        ?> 
                    </td>
                    <td width="10%" align="center">
                        <input <?php
                        if (@$colours[$cart_item['colour_id']] == "Clearance") {
                            echo 'class="qty_check"';
                            echo "attr-id='" . $quantity . "'";
                            ;
                        } else {
                            echo "";
                        }
                        ?>  type="text" name="qty[<?php echo $cart_item['cartitem_id']; ?>]" value="<?php echo $cart_item['qty']; ?>" size="1">
                    </td>
                    <td width="10%" align="right">
                        $<?php echo number_format($cart_item["total"], 2); ?>
                    </td>
                    <td width="10%" align="center">
                        <a href="/catalog/updatecart2.php?id=<?php echo $cart_item["cartitem_id"] ?>" onclick="return confirm('Are you sure you want to delete?')">Remove</a>
                    </td>
                </tr>
                <?php
                if ($cart_item['applicable'] == 'applicable') {
                    $applicable = 1;
                    $applicable_total += $cart_item["total"];
                } else {
                    $non_applicable += $cart_item["total"];
                }

                $cart_total += $cart_item['total'];
            }
            ?>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>
                <td align=right colspan="4" valign="top">
                    <strong>Subtotal: $<?php echo number_format($cart_total, 2); ?></strong>
                    <input type="hidden" name="subtotal" id="subtotal" value="<?php echo $cart_total; ?>" />
                    <input type="hidden" name="subtotal_applicable" id="subtotal_applicable" value="<?php echo $applicable_total; ?>" />
                    <input type="hidden" name="subtotal_non_applicable" id="subtotal_non_applicable" value ="<?= $non_applicable; ?>"/>
                </td>
                <td>&nbsp;</td>
            </tr>   

            <?php
            if (isset($applicable) && $applicable == '1') {
                ?>
                <tr>
                    <td align=right colspan="2">
                        <strong>Multiplier</strong>&nbsp;
                        <input type="text" name="multiplier" id="multiplier" />
                        <input type="button" id="calculate" value="Calculate" />
                    </td>
                    <td colspan="2" id="discount_html" align="right"><input type="hidden" name="discount" id="discount" />Discount: $0.00</td>
                    <td>&nbsp;</td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td>&nbsp;</td>
                <td><input type="hidden" name="total" id="total" value="<?php echo $cart_total; ?>" /></td>
                <td align="right" id="total_html" colspan="2">
                    <strong>Total: $<?php echo number_format($cart_total, 2); ?></strong>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td align="right">
                    <input type="submit" name="submit1" value="Continue shopping">
                </td>
                <td align="center">
                    <input type="submit" name="submit2" value="Update quantity">
                </td>
                <td>
                    <input type="submit" name="submit" value="Checkout">
                </td>
                <td></td>
            </tr>
        </table>
    </form>
    <br /><br />
    <script>
        $(document).ready(function () {

            $(document).on("change keyup", ".qty_check", function () {
                var max_qty = $(this).attr("attr-id");
                var val_qty = $(this).val();
                console.log(val_qty);
                console.log(max_qty);
                if (parseInt(val_qty) > parseInt(max_qty)) {
                    $(this).addClass("box_error");
                    $(this).val(max_qty);
                    console.log("Error");
                } else {
                    $(this).removeClass("box_error");
                    console.log("all good");
                }
            });
        });
        $("#calculate").click(function () {
            var multiplier = $("#multiplier").val();
            var subtotal = $("#subtotal_applicable").val();



            if (!multiplier) {
                alert('Please enter a value!');
            } else if (multiplier != 0.47 && multiplier != .47 && multiplier != 0.39 && multiplier != .39) {
                alert('Unknown multiplier!');
            } else {
                var total = subtotal * parseFloat(multiplier);

                var discount = subtotal - total;

                var sub_total = subtotal - total;


                var subtotal_non_applicable = $("#subtotal_non_applicable").val();
                subtotal_non_applicable

                var total_amount = parseFloat(subtotal_non_applicable) + parseFloat(total);



                $("#discount_html").html('Discount: $' + discount.toFixed(2));
                $("#discount").val(discount.toFixed(2));
                $("#total").val(total_amount);
                total_amount = total_amount.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                $("#total_html").html('<strong>Total: $' + total_amount + '</strong>');
            }
        });

    </script>

<?php }
?>
