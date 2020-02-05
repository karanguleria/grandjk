<?php 
session_start();

/*
J & K Cabinetry
Created by Jill Atkins (admin@cybril.com)
March 2017
*/

require_once("adminclass.php");
$admin = new admin;

$title = $admin->sitename;

$date = date('Y-m-d G:i:s');

if (isset($_SESSION['admin_redirect'])) {
    $redirect = $_SESSION['admin_redirect'];
} else {
    $redirect = 'index.php';
}

if (isset($_SESSION['admin'])) { //already logged in

	//go to dashboard
	header("Location: $redirect");
	exit();

} else if (isset($_POST['login'])) {
	
	$new_login = $_POST;

	$username = $new_login['username'];
	$password = $new_login['password'];

	$admin_username = $admin->admin_username;
	$admin_password = $admin->admin_password;
	
	if ($username == $admin_username && $password == $admin_password) {

		if ($_POST['remember_me'] == 1) {
			$remember_key = 'GrandJK';
			setcookie("GrandJK",$remember_key,time()+(3600*24*365));
		}
		
		$_SESSION['admin'] = true;
		
		//go to index
		header("Location: $redirect");
		exit();

	} else {
	
		$error = 'Details not correct!';

	}
	
} else if (isset($_COOKIE['GrandJK']) && !isset($_GET['logout'])) { //automatically log them in

		$_SESSION['admin'] = true;
		
		//go to index
		header("Location: $redirect");
		exit();
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
</head>
<body>

<div class="container well">

<?php if (isset($msg)) echo $msg; ?>

<h1>Login</h1>
<?php
if ($error) { ?>
	<div>&nbsp;</div><div class="alert alert-error"><?php echo $error;?> </div>
<?php
} else if (isset($_GET['logout'])) { ?>
	<div>&nbsp;</div><div class="alert alert-error">You are now logged out!</div>
<?php
}
?>
<div>Please login below.</div>
<br />
<form method="post" action="login.php">
Username:<br />
<span id="sprytextfield1"><input type="text" name="username" size="36" value="<?php if ($new_login['username']) echo htmlentities($new_login['username']);?>" /></span><br />
Password: <br />
<span id="sprytextfield2"><input type="password" name="password" size="38"  /></span><br />
<br />
Remember Me <input type="checkbox" tabindex="3" value="1" name="remember_me" style="width:40px;" /><br /><br />
<a href="forgotpassword.php">Forgotten Password</a><br /><br />
<button class="button" name="login">Login</button>
</form>
<br />
<script type="text/javascript">
<!--
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
	var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
//-->
</script>
</div>
</body>
</html>
