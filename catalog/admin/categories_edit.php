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

$category_id = $_REQUEST['id'];

if (isset($_POST['edit'])) { 
	
	$new_category = $_POST;

	$update = array('category' => $admin->prepare_string($new_category['category']),'state'=>$new_category['category_type']);
	  
	//update 
	$admin->update_array('categories', 'category_id', $category_id, $update);
		
	header("Location: categories.php?update");
	exit();
	
} else {

	$action = $_GET['action'];	

	if ($action == 'delete') {
				
        $admin->delete('categories', 'category_id', $category_id);		
			
		header("Location: categories.php?delete");
		exit();
		
	} else {

	    $sql = "SELECT * FROM categories WHERE category_id = $category_id";
	    $new_category = $admin->get_row($sql);
	    
	}
}



?>

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

</head>
<body>
<?php include("menu.php"); 


?>

<div class="container well">
<h1>Edit category</h1>

<form method="post" action="categories_edit.php">
<input type="hidden" name="id" value="<?php echo $category_id;?>" />
<table  class="table table-striped table-hover">
<tr>
	<td><strong>Category:</strong></td>
    <td align="left">
        <span id="sprytextfield1">
        <input type="text" name="category" value="<?php if ($new_category['category']) echo stripslashes(htmlentities($new_category['category']));?>" /> 
        </span>
    </td>
</tr>

<tr>
	<td><strong>Type:</strong></td>
    <td align="left">
        <span id="spryselect3">
		<select name="category_type">
                   <option value="">Select Category Type</option>
                   <option value="1" <?php if($new_category['state'] == '1') echo "selected='selected'";?>>Default</option>
		    
		   <option value="2" <?php if($new_category['state'] == '2') echo "selected='selected'";?>>Hardware</option>
		   
		   <option value="3" <?php if($new_category['state'] == '3') echo "selected='selected'";?>>Touch up sample part</option>
<option value="4" <?php if($new_category['state'] == '4') echo "selected='selected'";?>>Clearance</option>
		   
		   
         </select>
        </span>
    </td>
</tr>

</table>
<a class="btn btn-inverse pull-right" href="categories.php">Cancel</a><input class="btn btn-primary" type="submit" name="edit" value="Save changes to category">
</form>
<script type="text/javascript">
<!--
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
        var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3");
//-->
</script>
</div>
</body>
</html>
