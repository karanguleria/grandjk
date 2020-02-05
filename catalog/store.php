<?php
@session_start();

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

$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift($roles);

if ($role != 'customer' && $role != 'administrator') {
    header("Location: http://www.grandjk.com");
    exit();
}

include('grandclass.php');

$grandclass = new grandclass;

$image_display = $grandclass->image_display;
$siteurl = $grandclass->siteurl;

$sql = 'SELECT * FROM colours order by orderid';
$colours = $grandclass->get_array($sql);
//print_r($colours);
if (isset($_COOKIE['cart_id'])) {

    //get count of items in cart
    $cart_sql = 'SELECT sum(qty) as total FROM cartitems WHERE cart_id = ' . $_COOKIE['cart_id'];
    $cart_result = $grandclass->get_row($cart_sql);
    $no_cart = $cart_result['total'];
}
?>

<!-- https://material.io/icons/ -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style type="text/css">
    .link-shop {
        width: 50px;
        height: 50px;
        line-height: 48px;
        border: 2px solid;
        border-radius: 100px;
        position: relative;
        text-align: center;
        -webkit-transition: none;
        -o-transition: none;
        transition: none;
        display: inline-block;
        color: #f36561;
        background: transparent;
        text-decoration: none;
    }
    .link-shop:hover {
        color: #848484;
    }
    .label-inline {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 19px;
        height: 19px;
        font-size: 10px;
        line-height: 18px;
        font-weight: 700;
        border-radius: 100px;
        transition: .33s all ease;
        color: #fff;
        background: #f36561;
    }
    .link-shop:hover .label-inline {
        background-color: #848484
    }
    .cart-icon {
        width: 22px;
        height: 22px;
        font-size: 22px;
        line-height: inherit;
    }

</style>

<?php if (isset($_COOKIE['cart_id'])) { ?>
    <table width="100%" border="0">
        <tr><td align="right"><div class="page"><a href="/basket" class="link link-shop link-primary"><span class="label-inline" id="no_cart_items"><?php echo $no_cart; ?></span><i class="material-icons cart-icon">shopping_cart</i></a></div></td></tr>
    </table>
<?php } ?>
<table width="100%">
    <tr>
<?php
if ($colours) {
    $counter = 0;
    foreach ($colours as $colour) {
        if ($colour['image']) {
            if ($colour['colour_id'] != 14) { 
            ?>
                    <td width="25%" valign="top">
                        <?php if ($colour['colour_id'] == 12) {
                            ?> <a href="/newprice/?cid=<?php echo $colour['colour_id']; ?>"> <?php
                        } elseif ($colour['colour_id'] == 13) {
                            ?>
                                <a href="/tuchup/?cid=<?php echo $colour['colour_id']; ?>"> <?php
                                    } elseif ($colour['colour_id'] == 14) {
                            ?>
                                <!--<a href="/clearance/?cid=-->
                                    <?php // echo $colour['colour_id']; ?>
                                   <!--">--> 
                            <?php } else { ?>
                                <a href="/pricelist/?cid=<?php echo $colour['colour_id']; ?>">	 
                            <?php } ?>
                                    
                                <img width="183" height="302" alt="<?php echo stripslashes($colour['colour']) ?>" src="<?php echo $image_display . $colour['image']; ?>">
                                <h4><?php echo stripslashes($colour['colour']) ?> </h4>
                            </a>
                                  
                    </td>
                     <?php } ?>
            <?php
            $counter++;
            if ($counter == 4) {
                $counter = 0;
                echo '</tr><tr>';
            }
        }
    }
}
?>
    </tr>
</table>