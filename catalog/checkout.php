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

$siteurl = $grandclass->siteurl;

$cart_id = $_COOKIE['cart_id'];


/* echo "<pre>";
  print_r($_COOKIE);
  echo "</pre>";

  die("dddd"); */


if (!isset($_COOKIE['cart_id'])) { //have turned up somehow
    header("Location: store");
    exit();
} else if (isset($_POST['checkout'])) {

    $details = $_POST;

    $first_name = $details['first_name'];
    $last_name = $details['last_name'];
    $email = $details['email'];
    $tel = $details['tel'];
    $address = $details['address'];
    $city = $details['city'];
    $state = $details['state'];
    $zip = $details['zip'];
    $country = $details['country'];
    $job_no = $details['job_no'];
    $rep = $details['rep'];
    $shipping = $details['shipping'];
    $delivery = $details['delivery']; //15 May 2017
    $assembly = $details['assembly'];
    $touch_up_kit = $details['touch_up'];
    if ($details['notes'])
        $notes = $details['notes'];
    if ($details['company'])
        $company = $details['company'];
    if ($details['terms'])
        $terms = $details['terms'];

    if (!$first_name)
        $error = 'Please input First Name';
    if (!$last_name)
        $error .= '<br>Please input Last Name';
    if (!$email)
        $error .= '<br>Please input Email';
    if (!$tel)
        $error .= '<br>Please input Tel';
    if (!$address)
        $error .= '<br>Please input Address';
    if (!$city)
        $error .= '<br>Please input Town/City';
    if (!$state)
        $error .= '<br>Please input State';
    if (!$zip)
        $error .= '<br>Please input Zip Code';
    if (!$job_no)
        $error .= '<br>Please input your Job #';
    if (!$rep)
        $error .= '<br>Please input your JK Rep';
    if (!$assembly)
        $error .= '<br>Please select Assembly';
    if (!$touch_up_kit)
        $error .= '<br>Please select Touch Up Kit';
    if (!$shipping)
        $error .= '<br>Please input your Shipping Method';
    if (!$delivery)
        $error .= '<br>Please input your Expected Date';
    if (!$terms)
        $error .= '<br>You must accept our terms and conditions to place an order';

    if (!$error) {

        /* echo "<pre>";
          print_r($_COOKIE);
          echo "</pre>";
          die("dddddddd"); */

        $date = date('Y-m-d');

        $subtotal = $details['subtotal'];
        if ($details['multiplier'])
            $multiplier = $details['multiplier'];
        if ($details['discount'])
            $discount = $details['discount'];
        $total = $details['total'];

        $first_name = $grandclass->prepare_string($first_name);
        $last_name = $grandclass->prepare_string($last_name);
        $email = $grandclass->prepare_string($email);
        $tel = $grandclass->prepare_string($tel);
        $address = $grandclass->prepare_string($address);
        $city = $grandclass->prepare_string($city);
        $state = $grandclass->prepare_string($state);
        $zip = $grandclass->prepare_string($zip);
        $job_no = $grandclass->prepare_string($job_no);
        $rep = $grandclass->prepare_string($rep);
        $shipping = $grandclass->prepare_string($shipping);
        if ($notes)
            $notes = $grandclass->prepare_string($notes);
        if ($company)
            $company = $grandclass->prepare_string($company);

        //add customer to database
        $insert_customer_data = array('first_name' => $first_name,
            'last_name' => $last_name,
            'company' => $company,
            'email' => $email,
            'tel' => $tel,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'country' => $country,
            'rep' => $rep,
            'date' => $date);

        $insert_customer = $grandclass->insert_array('customers', $insert_customer_data);
        $customer_id = $grandclass->get_latest_id();

        //update cart
        $update_cart_data = array('subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'customer_id' => $customer_id,
            'job_no' => $job_no,
            'shipping' => $shipping,
            'delivery' => date('Y-m-d', strtotime($delivery)),
            'status' => 'emailed',
            'assembly' => $assembly,
            'touchup' => $touch_up_kit);

        if ($notes)
            $update_cart_data['notes'] = $notes;

        $update_cart = $grandclass->update_array('cart', 'cart_id', $cart_id, $update_cart_data);

        /* $query_quanity = "SELECT * FROM cartitems WHERE cart_id = $cart_id";
          $noitems = $grandclass->get_num_records($query_quanity);
          //if ($no_cart_items > 0) $cart_items = $grandclass->get_array($query);
          $cartitems = $grandclass->get_array($query_quanity);



          foreach($cartitems as $cart){

          $check_quanity = "SELECT * FROM quantity WHERE sub_category = '$cart[sub_category_id]' AND color_id = '14' ";
          $get_rows = $grandclass->get_num_records($check_quanity);
          if($get_rows > 0 ){

          $get_quanitity = $grandclass->get_array($check_quanity);
          foreach($get_quanitity as $get){

          $quanity = $get['quanity'] - $cart['quanity'];

          echo $quanity;

          if($quanity >= 0){
          $update_cart = $grandclass->update_array('quantity', 'sub_category',$get['sub_category'],array('quanity'=>$quanity));

          }
          }

          }


          }
         */
        //die("ddd");
        //send emails
        $admin_email = $grandclass->siteadminemail;
        $sitename = $grandclass->sitename;

        $message = "Dear $first_name, <br /><br />
		Thank you for your order from $sitename.  Your account manager ($rep) will contact you with a Sales Order/Quote for you to sign as confirmation.  Please call your account manager ($rep) if you need immediate help
        (866-853-9111).  Your order details are shown below for your reference: <br /><br /><table border='1' width='100%'><tr><td>";

        $admin_message = "There has been an order placed at $sitename.  The details are as follows:<br /><br /><table border='1' width='100%'><tr><td>";

        $email_message = "Name: $first_name $last_name<br />
		Email: $email <br />
                Company Name: $company<br />
		Tel: $tel <br />
		Address: <br /><br />
        $address <br />
		$city, $state $zip <br />
		$country <br /></td></tr><tr><td>
		Job #: $job_no <br />
		JK Rep: $rep <br />
		Assembly: $assembly <br />
		Touch Up Kit: $touch_up_kit <br />
		Shipping Method: $shipping <br />
		Expected Date: $delivery <br />";

        if ($notes)
            $email_message .= "Notes: $notes <br />";

        $email_message .= "</td></tr></table><br />";

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

        $email_message .= '<table width="100%" border="1">
            <tr><td>Order:</td></tr>
            <tr><td>
                <table width="100%">
				    <tr>
					    <td align="left" height="18" width="70%"><strong>Items</strong></td>
					    <td align="center" height="18" width="10%"><strong>Quantity</strong></td>
					    <td align="right" height="18" width="10%"><strong>Price</strong></td>
				    </tr>';

        foreach ($cart_items as $cart_item) {

            $email_message .= '<tr valign=top>
						<td width="70%">' . $sub_categories[$cart_item['sub_category_id']] . ' - ' . $colours[$cart_item['colour_id']] . '
						</td>
						<td width="10%" align="center">' . $cart_item['qty'] . '</td>
						<td width="10%" align="right">
							$' . number_format($cart_item["total"], 2) . '
						</td>
						</tr>';
        }

        if ($discount) {
            $email_message .= '<tr><td align=right colspan="3" valign="top">
							<strong>Subtotal: $' . number_format($subtotal, 2) . '</strong>
						</td></tr>
						<tr><td align=right colspan="3" valign="top">
							<strong>Discount: $' . number_format($discount, 2) . '</strong>
						</td></tr>';
        }

        $email_message .= '<tr><td align=right colspan="3" valign="top">
						<strong>Total: $' . number_format($total, 2) . '</strong>
					</td></tr></table></td></tr></table>';

        $email_message .= '<table width="100%"><tr><td align="center">Note: Total amounts may be slightly different to Sales Order/Quote due to rounding.</td></tr></table>';
        $email_message .= '<table width="100%"><tr><td>&nbsp;</td></tr><tr><td align="center">19204 68th Ave. S Kent, WA 98032 866-853-9111 www.grandjk.com </td></tr></table>';

        $message .= $email_message;
        $admin_message .= $email_message;

        //send email
        $subject = "Order From $sitename";
        $admin_subject = "Order For $sitename";


        foreach ($cart_items as $cart_item) {
            if ($cart_item['colour_id'] == "14") {
                $colour_id = $cart_item['colour_id'];
                $sub_category_id = $cart_item['sub_category_id'];
                $qty = $cart_item['qty'];
                $sql = "SELECT * FROM prices WHERE sub_category_id = $sub_category_id and colour_id= $colour_id";
                $sub_result = $grandclass->get_array($sql);
                $price_id = $sub_result[0]['price_id'];
                $quantity_left = $sub_result[0]['quantity'] - $qty;
                //update cart
                $update_cart_data = array('quantity' => $quantity_left);
                $update_cart = $grandclass->update_array('prices', 'price_id', $price_id, $update_cart_data);
            }
        }
        $grandclass->send_email($email, $subject, $message); //to customer
        $grandclass->send_email($admin_email, $admin_subject, $admin_message); //to admin
        unset($_COOKIE['cart_id']);
        $msg = TRUE;
    }
} else { //grab their details from database
    $user = wp_get_current_user();
    $user_id = $user->ID;

    //connect to wordpress
//    $wp_conn = mysqli_connect($grandclass->server, $grandclass->username, $grandclass->password, 'grandjkc_wordpress');
    $wp_conn = mysqli_connect($grandclass->server, "grandjkc_tbws", "CqALS@#*DJg~", 'grandjkc_wordpress');
    if (mysqli_connect_error())
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());

    //get their user details
    $user_sql = "SELECT * FROM wp_usermeta WHERE user_id = $user_id";
    $user_query = mysqli_query($wp_conn, $user_sql);

    while ($user_details = mysqli_fetch_assoc($user_query)) {

        if ($user_details['meta_key'] == 'phone1')
            $tel = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'billing_country')
            $country = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'zip')
            $zip = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'thestate')
            $state = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'city')
            $city = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'addr1')
            $address1 = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'addr2')
            $address2 .= ' ' . stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'first_name')
            $first_name = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'last_name')
            $last_name = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'billing_email')
            $email = stripslashes($user_details['meta_value']);
        if ($user_details['meta_key'] == 'billing_company')
            $company = stripslashes($user_details['meta_value']);
    }

    if ($address1)
        $address = $address1;
    if ($address2)
        $address .= ' ' . $address2;
}

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
?>
<!--calendar-->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $siteurl; ?>calendar-win2k-cold-1.css" title="win2k-cold-1" />
<script type="text/javascript" src="<?php echo $siteurl; ?>calendar.js"></script>
<script type="text/javascript" src="<?php echo $siteurl; ?>calendar-en.js"></script>
<script type="text/javascript" src="<?php echo $siteurl; ?>calendar-setup.js"></script>

<!--SpryAssets-->
<script src="<?php echo $siteurl; ?>SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="<?php echo $siteurl; ?>SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<style type="text/css"> strong.billing  {font-weight:500}


    .added-to-list1 .billing_info{display:block}</style>
<style rel="stylesheet" type="text/css" media="print" >
    @media print   {     .billing_info{display:block !important;}   }  
    @media screen, print   {     .billing_info{display:block !important;}   }
</style>

<?php
if ($msg) {

    echo '<div>&nbsp;</div><div>&nbsp;</div><div class="alert alert-success" style="color: #0a7c3a">Thank you, your order has been received.</div><div>&nbsp;</div>
         <div align="center"><form name="mycart" method="post" action="store"><input type="submit" name="submit" value="Continue shopping"></form></div><div>&nbsp;</div>';
} else {

    if ($error)
        echo '<div class="alert alert-error" style="color: #FF0000">' . $error . '</div>';
    ?>

    <form method="post" action="/checkout" id="print_area">
        <div class="print-only">
            <table cellspacing="10" cellpadding="10" width="100%" border="0">
                <tr>
                    <td colspan="2"><h3>Billing Details</h3></td>
                </tr>
                <tr>
                    <td><strong class="billing"><span class="green">*</span> First Name:</strong> </td>
                    <td><strong class="billing"><span class="green">*</span> Last Name</strong></td>
                </tr>
                <tr>
                    <td><span id="sprytextfield1">
                            <input type="text" name="first_name" size="40" value="<?php echo htmlentities($first_name); ?>"></span></td>
                    <td><span id="sprytextfield2"><input type="text" name="last_name" size="40" value="<?php echo htmlentities($last_name); ?>"></span></td>
                </tr>
                <tr>
                    <td colspan="2"><strong class="billing"> Company Name<strong><td>
                                    </tr>
                                <tr>
                                    <td colspan="2"><input type="text" name="company" size="100%" value="<?php echo htmlentities($company); ?>"></td>
                                </tr>
                                <tr>
                                    <td><strong class="billing"><span class="green">*</span> Email: </label></td>                    
                                    <td><strong class="billing"><span class="green">*</span> Tel: </label></td>
                                </tr>
                                <tr>
                                    <td><span id="sprytextfield3"><input type="text" name="email" size="40" value="<?php echo htmlentities($email); ?>"></span></td>
                                    <td><span id="sprytextfield4"><input type="text" name="tel" size="40" value="<?php echo htmlentities($tel); ?>"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong class="billing"><span class="green">*</span> Address: </strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span id="sprytextfield5"><input type="text" name="address" id="address" size="100%" value="<?php echo htmlentities($address); ?>"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong class="billing"><span class="green">*</span>Town/City: </strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span id="sprytextfield6"><input type="text" name="city" id="city" size="100%" value="<?php echo htmlentities($city); ?>"></span></td>
                                </tr>
                                <tr>
                                    <td><strong class="billing"><span class="green">*</span> State: </strong></td>
                                    <td><strong class="billing"><span class="green">*</span> Zip Code: </strong></td>
                                </tr>
                                <tr>
                                    <td><span id="sprytextfield7"><input type="text" name="state" id="state" size="40" value="<?php echo htmlentities($state); ?>"></span></td>
                                    <td><span id="sprytextfield8"><input type="text" name="zip" id="zip" size="40" value="<?php echo htmlentities($zip); ?>"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong class="billing"><span class="green">*</span> Country: </strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <select name="country" id="country">
                                            <option value="US"<?php if ($country == "US") echo " selected"; ?>>United States</option>
                                            <option value="AI"<?php if ($country == "AI") echo " selected"; ?>>Anguilla</option>
                                            <option value="AR"<?php if ($country == "AR") echo " selected"; ?>>Argentina</option>
                                            <option value="AU"<?php if ($country == "AU") echo " selected"; ?>>Australia</option>
                                            <option value="AT"<?php if ($country == "AT") echo " selected"; ?>>Austria</option>
                                            <option value="BE"<?php if ($country == "BE") echo " selected"; ?>>Belgium</option>
                                            <option value="BR"<?php if ($country == "BR") echo " selected"; ?>>Brazil</option>
                                            <option value="CA"<?php if ($country == "CA") echo " selected"; ?>>Canada</option>
                                            <option value="CL"<?php if ($country == "CL") echo " selected"; ?>>Chile</option>
                                            <option value="C2"<?php if ($country == "C2") echo " selected"; ?>>China</option>
                                            <option value="CR"<?php if ($country == "CR") echo " selected"; ?>>Costa Rica</option>
                                            <option value="CY"<?php if ($country == "CY") echo " selected"; ?>>Cyprus</option>
                                            <option value="CZ"<?php if ($country == "CZ") echo " selected"; ?>>Czech Republic</option>
                                            <option value="DK"<?php if ($country == "DK") echo " selected"; ?>>Denmark</option>
                                            <option value="DO"<?php if ($country == "DO") echo " selected"; ?>>Dominican Republic</option>
                                            <option value="EC"<?php if ($country == "EC") echo " selected"; ?>>Ecuador</option>
                                            <option value="EE"<?php if ($country == "EE") echo " selected"; ?>>Estonia</option>
                                            <option value="FI"<?php if ($country == "FI") echo " selected"; ?>>Finland</option>
                                            <option value="FR"<?php if ($country == "FR") echo " selected"; ?>>France</option>
                                            <option value="DE"<?php if ($country == "DE") echo " selected"; ?>>Germany</option>
                                            <option value="GR"<?php if ($country == "GR") echo " selected"; ?>>Greece</option>
                                            <option value="HK"<?php if ($country == "HK") echo " selected"; ?>>Hong Kong</option>
                                            <option value="HU"<?php if ($country == "HU") echo " selected"; ?>>Hungary</option>
                                            <option value="IS"<?php if ($country == "IS") echo " selected"; ?>>Iceland</option>
                                            <option value="IN"<?php if ($country == "IN") echo " selected"; ?>>India</option>
                                            <option value="IE"<?php if ($country == "IE") echo " selected"; ?>>Ireland</option>
                                            <option value="IL"<?php if ($country == "IL") echo " selected"; ?>>Israel</option>
                                            <option value="IT"<?php if ($country == "IT") echo " selected"; ?>>Italy</option>
                                            <option value="JM"<?php if ($country == "JM") echo " selected"; ?>>Jamaica</option>
                                            <option value="JP"<?php if ($country == "JP") echo " selected"; ?>>Japan</option>
                                            <option value="LV"<?php if ($country == "LV") echo " selected"; ?>>Latvia</option>
                                            <option value="LT"<?php if ($country == "LT") echo " selected"; ?>>Lithuania</option>
                                            <option value="LU"<?php if ($country == "LU") echo " selected"; ?>>Luxembourg</option>
                                            <option value="MY"<?php if ($country == "MY") echo " selected"; ?>>Malaysia</option>
                                            <option value="MT"<?php if ($country == "MT") echo " selected"; ?>>Malta</option>
                                            <option value="MX"<?php if ($country == "MX") echo " selected"; ?>>Mexico</option>
                                            <option value="NL"<?php if ($country == "NL") echo " selected"; ?>>Netherlands</option>
                                            <option value="NZ"<?php if ($country == "NZ") echo " selected"; ?>>New Zealand</option>
                                            <option value="NO"<?php if ($country == "NO") echo " selected"; ?>>Norway</option>
                                            <option value="PL"<?php if ($country == "PL") echo " selected"; ?>>Poland</option>
                                            <option value="PT"<?php if ($country == "PT") echo " selected"; ?>>Portugal</option>
                                            <option value="SG"<?php if ($country == "SG") echo " selected"; ?>>Singapore</option>
                                            <option value="SK"<?php if ($country == "SK") echo " selected"; ?>>Slovakia</option>
                                            <option value="SI"<?php if ($country == "SI") echo " selected"; ?>>Slovenia</option>
                                            <option value="ZA"<?php if ($country == "ZA") echo " selected"; ?>>South Africa</option>
                                            <option value="KR"<?php if ($country == "KR") echo " selected"; ?>>South Korea</option>
                                            <option value="ES"<?php if ($country == "ES") echo " selected"; ?>>Spain</option>
                                            <option value="SE"<?php if ($country == "SE") echo " selected"; ?>>Sweden</option>
                                            <option value="CH"<?php if ($country == "CH") echo " selected"; ?>>Switzerland</option>
                                            <option value="TW"<?php if ($country == "TW") echo " selected"; ?>>Taiwan</option>
                                            <option value="TH"<?php if ($country == "TH") echo " selected"; ?>>Thailand</option>
                                            <option value="TR"<?php if ($country == "TR") echo " selected"; ?>>Turkey</option>
                                            <option value="GB"<?php if ($country == "GB") echo " selected"; ?>>United Kingdom</option>
                                            <option value="UY"<?php if ($country == "UY") echo " selected"; ?>>Uruguay</option>
                                            <option value="VE"<?php if ($country == "VE") echo " selected"; ?>>Venezuela</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><h3>Further Details</h3></td>
                                </tr>
                                <tr>
                                    <td><strong class="billing"><span class="green">*</span> Your Job #: </strong></td>
                                    <td><strong class="billing"><span class="green">*</span> Your JK Rep: </strong></td>
                                </tr>
                                <tr>
                                    <td><span id="sprytextfield9"><input type="text" name="job_no" size="40" placeholder="PO/Job Name" value="<?php echo htmlentities($job_no); ?>"></span></td>
                                    <td><span id="sprytextfield10"><input type="text" name="rep" size="40" placeholder="Your JK Rep" value="<?php echo htmlentities($rep); ?>"></span></td>
                                </tr>
                                <tr>
                                    <td><strong class="billing"><span class="green">*</span> Assembly? ($18-36/ea): </strong></td>
                                    <td><strong class="billing"><span class="green">*</span> Touch Up Kit ($5-10/ea): </strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select name="assembly">
                                            <option value="">Please select</option>
                                            <option value="No"<?php if ($assembly == 'No') echo ' selected'; ?>>No</option>
                                            <option value="Yes"<?php if ($assembly == 'Yes') echo ' selected'; ?>>Yes</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="touch_up">
                                            <option value="">Please select</option>
                                            <option value="No"<?php if ($touch_up_kit == 'No') echo ' selected'; ?>>No</option>
                                            <option value="Yes"<?php if ($touch_up_kit == 'Yes') echo ' selected'; ?>>Yes</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong class="billing"><span class="green">*</span> Shipping Method: </strong></td>
                                    <td><strong class="billing"><span class="green">*</span> Expected Date: </strong></td>
                                </tr>
                                <tr>
                                    <td><span id="sprytextfield11"><input type="text" name="shipping" size="40" placeholder="Pickup/Delivery/FedEx/Freight" value="<?php echo htmlentities($shipping); ?>"></span></td>
                                    <td>
                                        <span id="sprytextfield12">
                                            <input type="text" name="delivery" id="delivery" readonly="1" value="<?php echo $delivery; ?>" />
                                        </span>
                                        <img src="<?php echo $siteurl; ?>cal.gif" id="trigger_delivery" style="cursor: pointer; border: 1px solid red;" title="Date selector"
                                             onmouseover="this.style.background = 'red';" onmouseout="this.style.background = ''" />
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField: "delivery", // id of the input field
                                                ifFormat: "%e %B %Y", // format of the input field
                                                button: "trigger_delivery", // trigger for the calendar (button ID)
                                                align: "Tl", // alignment (defaults to "Bl")
                                                singleClick: true
                                            });
                                        </script>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong class="billing">Order Notes: </label></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><textarea name="notes" cols="40" rows="5" class="print-no"><?php echo htmlentities($notes); ?></textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><h3>Your Order</h3></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <table width="100%" border="0" align="center">
                                            <tr>
                                                <td align="left" height="18" width="70%" colspan="2"><strong>Items</strong></td>
                                                <td align="center" height="18" width="15%"><strong>Quantity</strong></td>
                                                <td align="right" height="18" width="15%"><strong>Price</strong></td>
                                            </tr>
                                            <?php
                                            $applicable_set = "";
                                            $applicable_total = 0;
                                            $non_applicable = 0;
                                            foreach ($cart_items as $cart_item) {
                                                ?>
                                                <tr valign=top>
                                                    <td width="70%" colspan="2">
                                                       
   <?php
        if (@$colours[$cart_item['colour_id']] == "Clearance") {
             $sub_category_id = $cart_item['sub_category_id'];
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
        ?>  <?php // echo $sub_categories[$cart_item['sub_category_id']] . ' - ' . $colours[$cart_item['colour_id']]; ?>
                                                    </td>
                                                    <td width="15%" align="center">
                                                        <?php echo $cart_item['qty']; ?>
                                                    </td>
                                                    <td width="15%" align="right">
                                                        $<?php echo number_format($cart_item["total"], 2); ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                if ($cart_item['applicable'] == 'applicable') {
                                                    $applicable = 1;
                                                    $applicable_total += $cart_item["total"];
                                                    $applicable_set = "applicable";
                                                } else {
                                                    $non_applicable += $cart_item["total"];
                                                }



                                                $cart_total += $cart_item['total'];
                                            }
                                            $sub_total = $cart_total;
                                            if (isset($_COOKIE['multiplier']) && $_COOKIE['multiplier'] != '') {
                                                $multiplier = $_COOKIE['multiplier'];
                                                //$cart_total = $cart_total * $multiplier;
                                                //echo $applicable_total;
                                                $cart_total = $applicable_total * $multiplier;
                                                $discount = $applicable_total - $cart_total;

                                                $cart_total = $cart_total + $non_applicable;
                                            }
                                            ?>
                                            <tr><td colspan="4">&nbsp;</td></tr>
                                            <tr>
                                                <td width="70%" colspan="2">&nbsp;</td>
                                                <td width="15%"><input type="hidden" name="subtotal" id="subtotal" value="<?php echo $sub_total; ?>" />
                                                    <input type="hidden" name="subtotal_applicable" id="subtotal_applicable" value="<?php echo $applicable_total; ?>" />
                                                    <input type="hidden" name="subtotal_non_applicable" id="subtotal_non_applicable" value ="<?= $non_applicable; ?>"/>
                                                </td>
                                                <td align="right">
                                                    <strong>Subtotal: $<?php echo number_format($sub_total, 2); ?></strong>
                                                </td>
                                            </tr>
                                            <?php
                                            if (isset($_COOKIE['multiplier']) && $_COOKIE['multiplier'] != '' && $applicable_set == 'applicable') {
                                                ?>
                                                <tr>
                                                    <td valign="top" width="70%" colspan="2">
                                                        <strong>Multiplier</strong>&nbsp;
                                                        <input type="text" name="multiplier" id="multiplier" value="<?php if ($multiplier) echo $multiplier; ?>" />
                                                        <input type="button" id="calculate" value="Calculate" />
                                                    </td>
                                                    <td><input type="hidden" name="discount" id="discount" value="<?php if ($discount) echo $discount; ?>" /></td>
                                                    <td id="discount_html" align="right">Discount: $<?php if ($discount)
                                            echo number_format($discount, 2);
                                        else
                                            echo '0.00';
                                                ?></td>
                                                </tr>
        <?php
    }
    ?>

                                            <tr>
                                                <td width="70%" colspan="2">&nbsp;</td>
                                                <td width="15%"><input type="hidden" name="total" id="total" value="<?php echo $cart_total; ?>" />

                                                </td>
                                                <td align="right" id="total_html">
                                                    <strong>Total: $<?php echo "<span id='total_get'>" . $cart_total . "</span>"; ?></strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><input type="checkbox" name="terms" /> I've read and accept the <a href="http://www.grandjk.com/return-policy/" target="_blank">terms and conditions</a></td></tr>
                                <tr><td>Payment due upon receipt</td><td align="right"><a href="store">Continue Shopping</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button name="checkout">Place Order</button></td></tr>
                                </table>
                                </div>
                                </form> 
                                <script type="text/javascript">

                                    var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
                                    var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
                                    var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "email");
                                    var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
                                    var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
                                    var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");
                                    var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7");
                                    var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8");
                                    var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9");
                                    var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10");
                                    var sprytextfield11 = new Spry.Widget.ValidationTextField("sprytextfield11");
                                    var sprytextfield12 = new Spry.Widget.ValidationTextField("sprytextfield12");

                                </script>
                                <script>
                                    $(document).ready(function ()
                                    {
                                        var total_amoutn1 = $("#total").val();
                                        console.log(total_amoutn1);
                                        var total_amount2 = parseFloat(total_amoutn1);
 
                                        $("#total_get").html('');

                                        $("#total_get").html(total_amount2.toFixed(2));

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
                                    });
                                </script>	
                                <script>
                                    $(".noslimstat").click(function () {
                                        alert("hiii");
                                        $(".billing_info").show();
                                    });</script>

<?php } ?>