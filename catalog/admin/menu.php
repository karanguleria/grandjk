<?php
/*
J & K Cabinetry
Created by Jill Atkins (admin@cybril.com)
March 2017
*/
?>

<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">

      <div class="container"> <button class="btn btn-navbar" data-toggle="collapse" type="button" data-target=".nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> 
   
      <a class="brand" data-toggle="collapse" data-target=".nav-collapse" ><span class="pull-left"><?php echo $title;?> Admin Navigation &nbsp;</span> <i class="visible-desktop pull-left  icon-arrow-down "></i> <i class="hidden-desktop pull-left icon-arrow-right "></i> </a>
      
        <div class="nav-collapse collapse"> 
         <p class="navbar-text pull-right visible-desktop">
            </p>
			<?php
			if (isset($_SESSION['admin'])) { ?>
			  <ul class="nav">
				<li><a href="categories.php">Categories</a></li>
				<li><a href="sub_categories.php">Sub-Categories</a></li>
				<li><a href="colours.php">Colors</a></li>
				<li><a href="orders.php">Orders</a></li>
				<li><a href="prices.php?c">Prices</a></li>
				<li><a href="logout.php">Logout</a></li>
			  </ul>
			<?php
			} ?>
        </div>
        <!--/.nav-collapse --> 
      </div>

  </div>
</div>







