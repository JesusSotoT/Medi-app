<div class="login-header login-caret">
	<div class="login-content">
		<a href="" class="logo"> <?php echo $this -> Html -> img('logo@2x.png', array('width' => 190)); ?></a>
	</div>
</div>

<div class="login-form">
	<div class="login-content">
		<h1 class="iconify"><i class="entypo-mail"></i></h1>
		<p class="description">
			Hemos enviado un correo electrónico con la liga para el restaurado de tu contraseña, por favor revisa tu bandeja de entrada.
		</p>
	</div>
</div>

<script>
	var Interface = function($) {
		var showContent = function() {
			if ($('body').hasClass('login-form-fall')) {
				setTimeout(function() {
					$('body').addClass('login-form-fall-init');

					setTimeout(function() {
						if (!focus_set) {
							$('body FORM').find('input:first').focus();
							focus_set = true;
						}
					}, 8000);
				}, 0);
			}
		};
		showContent();
	};
	Interface($);
</script>