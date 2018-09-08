<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href="<?php echo $this -> url('favicon.ico')?>" rel="icon" type="image/x-icon" />

	<title><?php echo $this -> title; ?><?php echo $this -> name; ?></title>

	<?php echo $this -> Html -> css('jquery-ui/no-theme/jquery-ui-1.10.3.custom.min')?>
	<?php echo $this -> Html -> css('font-icons/entypo/css/entypo')?>
	<?php echo $this -> Html -> css('noto-sans')?>
	<?php echo $this -> Html -> css('bootstrap')?>
	<?php echo $this -> Html -> css('neon-core')?>
	<?php echo $this -> Html -> css('neon-theme')?>
	<?php echo $this -> Html -> css('neon-forms')?>
	<?php echo $this -> Html -> css('custom')?>

	<?php echo $this -> Html -> script('jquery-1.11.0.min')?>

	<!--[if lt IE 9]><?php echo $this -> Html -> script('ie8-responsive-file-warning')?><![endif]-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->


</head>
<body class="page-body login-page login-form-fall" >

<div class="login-container">

	<?php echo $this -> contents?>

</div>
<?php $this -> element('admin/global_scripts')?>
<?php echo $this -> Html->script('neon-demo')?>
</body>
</html>
