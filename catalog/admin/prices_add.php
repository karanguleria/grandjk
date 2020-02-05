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

if (isset($_POST['add'])) { 
	
	$new_price = $_POST;
	
	$colour_id = $new_price['colour_id'];
	$sub_category_id = $new_price['sub_category_id'];
	$price = $admin->prepare_string($new_price['price']);
        
	$applicable = $new_price['applicable'];
        $quantity = $new_price['quantity'];
	
	//check not already in
	$check_sql = "SELECT * FROM prices WHERE sub_category_id = $sub_category_id AND colour_id = $colour_id";
	$check = $admin->get_num_records($check_sql);
	
	if ($check <= 0) { //not in, add
	
		$insert = array('sub_category_id' => $sub_category_id, 'colour_id' => $colour_id, 'price' => $price, 'applicable'=>$applicable, 'quantity' => $quantity);

		//add to database
		$admin->insert_array('prices', $insert);

		header("Location: prices.php?add");
		exit();
	
	} else {

		$error = '<div class="alert alert-danger">There is already a price for that combination in the database!</div>';

	}

} else if (isset($_GET['cid'])) { //adding a price to a particular colour

	$colour_id = $_GET['cid'];

} else if (isset($_GET['sid'])) { //adding a price to a particular colour

    $sub_category_id = $_GET['sid'];
    
}

//get colours
$colours_sql = 'SELECT * FROM colours';
$colours = $admin->get_select($colours_sql,'colour_id','colour', $colour_id);

//get sub-categories
$sub_categories_sql = 'SELECT * FROM sub_categories';
$sub_categories_result = $admin->get_array($sub_categories_sql);

$sub_categories = '';
foreach ($sub_categories_result as $sub_category) {
	$sub_categories .= '<option value="'.$sub_category['sub_category_id'].'"';
    if ($sub_category_id && $sub_category_id == $sub_category['sub_category_id']) $sub_categories .= ' selected';
    $sub_categories .= '>' . stripslashes($sub_category['id']) . ' - ' . stripslashes($sub_category['sub_category']) . '</option>';
}?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="robots" content="noindex,nofollow">

<?php include("metatags.php");?>

<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
</head>
<body>
<?php include("menu.php"); ?>

<div class="container well">
<h1>Add new price</h1>

<?php if ($error) echo $error;?>

<form method="post" action="prices_add.php">

<table class="table table-striped table-hover">
<tr>
	<td><strong>Color:</strong></td>
    <td align="left">
        <span id="spryselect1">
        <select name="colour_id" id= "color">
        	<option value="">Please select</option>
        	<?php echo $colours;?>
        </select>
        </span>
    </td>
</tr>
<tr>
	<td><strong>Sub-Category:</strong></td>
    <td align="left">
        <span id="spryselect2">
        <select name="sub_category_id">
        	<option value="">Please select</option>
        	<?php echo $sub_categories;?>
        </select>
        </span>
    </td>
</tr>
<tr>
	<td><strong>Price:</strong></td>
    <td align="left">
        <span id="sprytextfield1">
        <input type="text" name="price" value="<?php if ($new_price['price']) echo number_format($new_price['price'],2);?>" /> 
        </span>
    </td>
</tr>

<tr class = "quantity" style = "display:none">
	<td><strong>Quantity:</strong></td>
    <td align="left">
        <span id="sprytextfield1">
        <input type="text" name="quantity" value="<?php if ($new_price['quantity']) echo $new_price['quantity'];?>" /> 
        </span>
    </td>
</tr>

<tr>
	<td><strong>Multiplier Applicable:</strong></td>
    <td align="left">
        <span id="spryselect3">
		<select name="applicable">
		<option value="">Please select</option>
		<option value="non-applicable">Non-applicable</option>
		<option value="applicable">applicable</option>
		</select>
        
        </span>
    </td>
</tr>


</table>
<a class="btn btn-inverse pull-right" href="prices.php">Cancel</a><input class="btn btn-primary" type="submit" name="add" value="Save new price"> 
</form>
<script>
$(document).ready(function(){
   
   $("#color").change(function(){
      var selectedCountry = $("#color option:selected").val();
      //alert(selectedCountry)
        if (selectedCountry == 14) {
         $(".quantity").show();   
        }
        else{
         $(".quantity").hide();      
        }
   });
});
</script>
</div>
</body>
</html>
