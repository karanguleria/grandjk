<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
<link type="text/css" rel="stylesheet" href="css/bootstrap-responsive.min.css" >
<link type="text/css" rel="stylesheet" href="css/lightbox.css"  media="screen" />
<link type="text/css" rel="stylesheet" href="css/admin.css"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
                <script src="js/respond.min.js"></script>
	<![endif]-->
<script src="js/bootstrap.js"></script>
<script src="js/bootstrap-modal.js"></script> 
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.lightbox.js"></script>
<script>
$(document).ready(function(){		    
	$(".lightbox").lightbox({
		fitToScreen: true,
		imageClickClose: false
	});	
});

</script>
<title><?php echo $title;?> Administration Area</title>