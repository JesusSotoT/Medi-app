<div class="login-header login-caret">

	<div class="login-content">

		<a href="" class="logo"> <?php echo $this -> Html -> img('logo@2x.png', array('width' => 190)); ?></a>

		<p class="description">
			Escribe tu correo electrónico registrado, te enviarémos accesos para reestablecer tu contraseña.
		</p>

		<!-- progress bar indicator -->
		<div class="login-progressbar-indicator">
			<h3>43%</h3>
			<span>Obteniendo credenciales...</span>
		</div>
	</div>

</div>

<div class="login-progressbar">
	<div></div>
</div>

<div class="login-form">

	<div class="login-content">

		<div id="notify" class="form-login-error">
			<h3>Acceso incorrecto</h3>
			<p>
				Ingresa tu correo y contraseña correctamente.
			</p>
		</div>

		<form method="post" role="form" id="passwordBack" action='<?php echo $this -> url(array('controller' => 'administradores', 'action' => 'forgottenpassword')); ?>'>
			
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon">
						<i>@</i>
					</div>
					<input type="text" class="form-control" name="recovery[email]" id="email" placeholder="Email" autocomplete="off" />
				</div>
			</div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block btn-login">
					<i class="entypo-lock"></i>
					Recuperar Contraseña
				</button>
			</div>
			
		</form>

		<div class="login-bottom-links">			
			<?php
			echo $this -> Html -> link('SUSTAM ' . date('Y'), 'http://www.sustam.com');
			?>
		</div>

	</div>

</div>

<?php echo $this -> Html->script('password-back')?>