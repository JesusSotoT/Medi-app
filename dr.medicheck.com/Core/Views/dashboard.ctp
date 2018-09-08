<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="<?php echo $this -> url('favicon.ico', false, true) ?>" rel="icon" type="image/x-icon" />
	
		<title><?php echo strip_tags($this -> title); ?><?php echo $this -> name; ?></title>
	
		<?php echo $this -> Html -> css('jquery-ui/no-theme/jquery-ui-1.10.3.custom.min')?>
		<?php echo $this -> Html -> css('font-icons/entypo/css/entypo')?>
		<?php echo $this -> Html -> css('font-icons/font-awesome/css/font-awesome')?>
		<?php echo $this -> Html -> css('noto-sans')?>
		<?php echo $this -> Html -> css('bootstrap')?>
		<?php echo $this -> Html -> css('neon-core')?>
		<?php echo $this -> Html -> css('neon-theme')?>
		<?php echo $this -> Html -> css('neon-forms')?>
		<?php echo $this -> Html -> css('skins/black')?>
		<?php echo $this -> Html -> css('styleizr')?>
		<?php echo $this -> Html -> css('custom')?>
		
		<!-- JQUERY -->
		<?php echo $this -> Html -> script('jquery')?>
		<?php echo $this -> Html -> script('jquery.mvclite');?>
		<?php echo $this -> Html -> script('jquery.multi-select');?>
		<?php //echo $this -> Html -> script('jquery-1.11.0.min')?>
	
		<!--[if lt IE 9]><?php echo $this -> Html -> script('ie8-responsive-file-warning')?><![endif]-->
	
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	
	
	</head>
	<body class="page-body">

	<?php $this -> element('admin/actionMenu')?>

	<div class="page-container">
	
		<!-- add class "sidebar-collapsed" to close sidebar by default,
			"chat-visible" to make chat appear always -->
	
		<div class="sidebar-menu fixed">
	
			<?php $this -> element('admin/header')?>
	
			<?php $this -> element('admin/main')?>
	
		</div>
	
		<div class="main-content">
	
			<?php $this -> element('admin/userbar'); ?>
		
			<hr />
		
			<?php $this -> element('admin/breadcrums')?>
		
			<div id="actionData">
				<?php $this -> element('admin/flash')?>
				<?php if(!is_null($this -> title)):?>
				<h2><?php echo str_replace('|', null, $this -> title )?></h2>
				<hr />
				<?php endif ?>
				<div id="action">
					<?php echo $this -> contents ?>
				</div>
			</div>
			
            <?php if($this -> Html -> is_paginated()):?>

               <section id="paginatiorHolder">
                   <?php echo $this -> Html -> pagination_set(array(
                        'wrapper'       => 'UL.pagination',
                        'row'           => 'LI',
                        'active'        => '.active',
                        'expose'        => 3,
                        'back'          => false,
                        'forth'         => false,
                        'first_text'    => '<i class="entypo-left-open-big"></i> Primera',
                        'last_text'     => 'Última <i class="entypo-right-open-big"></i>',
                    ));?>
                    <?php echo $this -> Html -> paginate();?>
               </section>
               
            <?php endif; ?>
			
			<footer class="main">
				<?php $this -> element('admin/footer'); ?> | Core Version <?php echo $version; ?> 
			</footer>
			
		</div>
	
		</div>	
	
		<?php $this -> element('admin/global_scripts')?>
	
		<?php $this -> Js -> release_cached(); ?>
	
	</body>
	
</html>