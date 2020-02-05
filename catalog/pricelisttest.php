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

$colour_id = filter_input(INPUT_GET, 'cid', FILTER_VALIDATE_INT);

if ($colour_id) {

    //get colour name
    $colour_sql = "SELECT colour FROM colours WHERE colour_id = $colour_id";
    $colour_result = $grandclass->get_row($colour_sql);
    $colour = stripslashes($colour_result['colour']);

    //get prices
    $sql = "SELECT * FROM prices WHERE colour_id = $colour_id";
    $prices = $grandclass->get_assoc_array($sql, 'sub_category_id', 'price');

    //get categories

    if ($colour_id == '14')
    {

    $categories_sql = 'SELECT * FROM categories where `state`= 4';
    }
    else 
    {
       $categories_sql = 'SELECT * FROM categories where `state`= 1';
    }
    $categories = $grandclass->get_array($categories_sql);
    //print_r($categories);
} else {
    header("Location: store");
    exit();
}

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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> 
<strong><a href="/store" id="home">Store</a></strong> / 
<strong><?php echo $colour; ?></strong>
<?php
if ($categories) {
    foreach ($categories as $category) {
        echo ' / <a href="#' . $category['category'] . '">' . stripslashes($category['category']), '</a>';
    }
}

?>
</a>
<?php
/*if ($colour_id == '14') {
    echo "<h1 style='text-align:center;'>Coming Soon</h1>";
}*/  if ($colour_id == '12') {
    echo "<h1 style='text-align:center;'>Work Under Progress on this page</h1>";
} else {
    ?>
    <table width="100%" border="0">
        <tr><td align="center" width="85%"><img src="http://www.grandjk.com/images/Literature.jpg" width="100" height="80" /></td><td align="right" width="15%"><div class="page" id="cart_div"<?php if (!isset($_COOKIE['cart_id'])) echo 'style="display:none"'; ?>><a href="/basket" class="link link-shop link-primary"><span class="label-inline" id="no_cart_items"><?php if ($no_cart) echo $no_cart; ?></span><i class="material-icons cart-icon">shopping_cart</i></a></div></td></tr>
        <tr><td align="center" width="85%"><a href="http://www.grandjk.com/wa/JKCatAndSpecForWeb.pdf" target="_blank">Please click here for Product Catalog</a></td><td align="right" width="15%">&nbsp;</td></tr>
    </table>
    <table border="1" width="100%">
        <tr>		
            <td colspan="2">&nbsp;</td><td><strong>MSRP</strong></td><td><strong>QTY</strong></td><td>&nbsp;</td>
        </tr>
        <?php
        if ($categories) {
            $link_counter = 0;
            foreach ($categories as $category) {
                $link_counter++;
                //get sub-categories
                $sub_category_sql = 'SELECT * FROM sub_categories WHERE category_id = ' . $category['category_id'];
                $sub_categories = $grandclass->get_array($sub_category_sql);
                ?>	
                <tr>														
                    <td colspan="5" id="<?php echo $category['category'] ?>">
                        <strong><a name="<?php echo $category['category_id'] ?>"></a><?php echo stripslashes(strtoupper($category['category'])); ?></strong>
                        <?php if ($link_counter > 1) echo '<span style="float:right"><a href="#home">Back To Top</a></span>'; ?>
                    </td>													
                </tr>	
                <?php
                if ($sub_categories) {
                    foreach ($sub_categories as $sub_category) {
                        ?>
                        <tr>		
                            <td><?php echo $sub_category['id']; ?></td>
                            <td><?php echo stripslashes($sub_category['sub_category']) ?></td>
                            <td>
                                <?php if ($prices[$sub_category['sub_category_id']] != 0) { ?>
                                    $<?php
                                    echo number_format($prices[$sub_category['sub_category_id']], 2);
                                } else {
                                    echo 'CALL';
                                }
                                ?>
                            </td>
                            <td><input type="text" size="1" id="qty_<?php echo $sub_category['sub_category_id'] ?>" /></td>
                            <td><input type="submit" name="add" value="Add to cart" id="<?php echo $sub_category['sub_category_id'] ?>" onclick="add_to_cart(this);" /></td>
                        </tr>	
                        <?php
                    }
                }
            }
            ?>
            <tr>														
                <td colspan="5">
                    <span style="float:right"><a href="#home">Back To Top</a></span>
                </td>													
            </tr>
        <?php }
        ?>
    </table><br /><br />
    <?php
}
?>

<script>

    function add_to_cart(button) {
        var id = button.id;

        if ($("#qty_" + id).val() != '') {
            var qty = $("#qty_" + id).val();
        }

        if (!qty) {
            alert('You did not enter a quantity!');
        } else if (qty == 0) {
            alert('Please enter a number greater than 0!')
        } else if (typeof qty !== 'number' && (qty % 1) !== 0) {
            alert('You did not enter a number!');
        } else {
            var cid = getParameterByName('cid'); //colour id
            var string = 'id=' + id + '&qty=' + qty + '&cid=' + cid;
            $.ajax({
                type: "POST",
                url: "/catalog/addtocart.php",
                data: string,
                success: function (data) {
//					alert(data);
                   $('#no_cart_items').html(data);
                    $('#cart_div').show();
                    alert('Added to cart!');
                }
            });
        }
    }

    function getParameterByName(name) {
        var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
        return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
    }

</script>
