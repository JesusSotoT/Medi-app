<header class="logo-env">

	<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
	<div class="sidebar-mobile-menu visible-xs">
		<a href="#" class="with-animation"><!-- add class "with-animation" to support animation --> <i class="entypo-menu"></i> </a>
	</div>

	<!-- logo -->
	<div class="logo">
		<?php echo $this -> Html -> link($this -> Html -> img ( 'logo@2x-white.png', array ( 'width' => 120 ) ), AppController::$homeAction); ?>
	</div>

	<!-- logo collapse icon -->

	<div class="sidebar-collapse">
		<a href="#" class="sidebar-collapse-icon with-animation"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition --> <i class="entypo-menu"></i> </a>
	</div>


</header>