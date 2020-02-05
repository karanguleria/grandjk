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
$accepted_types = $admin->accepted_types;
$image_path = $admin->image_path;
$image_display_path = $admin->image_display;
$image_maxsize = 250;

$colour_id = $_REQUEST['id'];

if (isset($_POST['edit'])) { 
	
	$new_colour = $_POST;
	
	$colour = $admin->prepare_string($new_colour['colour']);
	
	//check not already in
	$check_sql = "SELECT * FROM colours WHERE colour = '$colour' AND colour_id != $colour_id";
	$check = $admin->get_num_records($check_sql);
	
	if ($check <= 0) { //not in, add

		$update = array('colour' => $colour);

        //do image if there is one
	    $main_image = $_FILES['image']; 

	    if ($main_image) { 

            $image = $main_image['tmp_name'];
 			
		    if (in_array($main_image['type'],$accepted_types)) {
					
			    $image_name = $main_image['name'];	               

			    list($width, $height, $type, $attr) = getimagesize($image);									
		
			    //standard image
			    if ($width>$image_maxsize || $height>$image_maxsize) {				
				    if ($admin->resizeImage($image,$image_path . $image_name, $image_maxsize, $image_maxsize)) {			
					    $image = $image_name;													  				
				    } else {																										
					    $image = "";																			
					    $error = "Error copying image.";						
				    }
			    } else {																								
				    if (copy($image, $image_path . $image_name)) {					  					
					    $image = $image_name;													  				
				    } else {																										
					    $image = "";																			
					    $error = "Error copying image.";
				    }
			    }

			    if (!$error) {
				    $update['image'] = $image;
			    }
	
		    } else { //wrong type
			    $error = "Incorrect image type - ". $main_image['type'];
		    } //end if in array			
	    }    	

	    if (!$error) {
		    //add to database
		    $admin->update_array('colours', 'colour_id', $colour_id, $update);

		    header("Location: colours.php?update");
		    exit();
        }
	
	} else {

		$error = '<div class="alert alert-danger">That color is already in the database!</div>';

	}

} else {

	$action = $_GET['action'];	

    $sql = "SELECT * FROM colours WHERE colour_id = $colour_id";
    $new_colour = $admin->get_row($sql);

	if ($action == 'delete') {

        //delete image if there is one
		$old_image = $new_colour['image'];
		if ($old_image) unlink($image_path . $old_image);
        				
        $admin->delete('colours', 'colour_id', $colour_id);		
			
		header("Location: colours.php?delete");
		exit();

	} else if ($action == 'deleteimage') {
		
		//delete image
		$old_image = $new_colour['image'];		
		if ($old_image) unlink($image_path . $old_image);

		$update = array('image' => ''); 
		
		//update db 
	    $admin->update_array('colours', 'colour_id', $colour_id, $update);

		$new_colour['image'] = '';

        $msg = '<div class="alert alert-success">Image deleted!</div>';		
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
<?php include("menu.php"); ?>

<div class="container well">
<h1>Update color</h1>

<?php if ($error) echo $error;?>
<?php if ($msg) echo $msg;?>

<form method="post" action="colours_edit.php" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $colour_id;?>" />

<table class="table table-striped table-hover">
<tr>
	<td><strong>Color:</strong></td>
    <td align="left">
        <span id="sprytextfield1">
        <input type="text" name="colour" value="<?php if ($new_colour['colour']) echo stripslashes(htmlentities($new_colour['colour']));?>" /> 
        </span>
    </td>
</tr>
<?php
if ($new_colour['image']) { ?>
    <tr>
	    <td valign="top"><strong>Image:</strong> </td>
	    <td>
		    <img class="img-polaroid" src="<?php echo $image_display_path . $new_colour['image'];?>" border="0" /><a href="colours_edit.php?action=deleteimage&id=<?php echo $colour_id;?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete?')"><i class="icon-trash icon-white"></i> Delete</a>  
	    </td>
    </tr>
<?php } else { ?>
    <tr>
	    <td><strong>Image:</strong> </td>
        <td align="left">
            <input class="input-xlarge" type="file" name="image" /> 
        </td>
    </tr>
<?php } ?>
</table>
<a class="btn btn-inverse pull-right" href="colours.php">Cancel</a><input class="btn btn-primary" type="submit" name="edit" value="Save changes to color"> 
</form>
<script type="text/javascript">
<!--
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
//-->
</script>
</div>
</body>
</html>
