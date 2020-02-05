<?php 
/*
Sitting For A Cause
Created by Jill Atkins (admin@cybril.com)
March 2015
*/

require_once('adminclass.php');
$admin = new admin;

$title = $admin->sitename;

$sitename = $admin->sitename;
$siteadminemail = $admin->siteadminemail;
$username = $admin->admin_username;
$password = $admin->admin_password;

if (isset($_POST['send'])) {

	//send email
	$email_subject = "Details";
	$email_message = "<p>As per your recent request, please find below your login details:<br /><br />Username: $username<br /><br />Password: $password<br /><br /></p>";

    $admin->send_email($siteadminemail,$email_subject,$email_message);

	$msg = 'Login details have been sent to your registered email address.';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
<meta name="robots" content="noindex,nofollow">
<?php include("metatags.php");?>
</head>
<body>

<div class="container well">

<?php if (isset($msg)) echo $msg; ?>

<h1>Forgotten password</h1>
	  <?php
	  if ($msg) { ?>
			<div>&nbsp;</div><div class="alert alert-success"><?php echo $msg;?> </div>
	  <?php
	  } else { ?>
		<p>
		<br />Please press Send Details below and your login details will be sent to you.<br />
		<br />
		<form action="forgotpassword.php" method="post">
			<button class="button" name="send">Send Details</button>
		</form><br />
		</p>
	<?php
	} ?>

</div>
</body>
</html>
