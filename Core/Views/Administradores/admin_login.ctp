<!-- This is needed when you send requests via Ajax -->
<script type="text/javascript">var loginurl =    '<?php echo $this -> url(array('controller' => 'administradores', 'action' => 'login')); ?>';</script>

<div class="login-header login-caret">

	<div class="login-content">

		<a href="index.html" class="logo"> <?php echo $this -> Html -> img('logo@2x.png', array('width' => 190)); ?></a>

		<p class="description">
			Estimado usuario, por favor inicie sesión
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

		<div class="form-login-error">
			<h3>Acceso incorrecto</h3>
			<p>
				Ingresa tu correo y contraseña correctamente.
			</p>
		</div>

		<form method="post" role="form" id="form_login">
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon">

						<i>@</i>

					</div>

					<input type="text" class="form-control" name="login[username]" id="username" placeholder="Email" autocomplete="off" />

				</div>

			</div>

			<div class="form-group">

				<div class="input-group">
					<div class="input-group-addon">
						<i class="entypo-key"></i>
					</div>
					<input type="password" class="form-control" name="login[password]" id="password" placeholder="Contraseña" autocomplete="off" />
				</div>

			</div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block btn-login">
					<i class="entypo-login"></i>
					Iniciar Sesión
				</button>
			</div>

		</form>

		<div class="login-bottom-links">
			<?php echo $this -> Html -> link('¿Ha olvidado su contraseña?', array('controller' => 'administradores', 'action' => 'forgottenpassword'), array('class' => 'link')); ?>
			<br />
			<?php echo $this -> Html -> link('SUSTAM ' . date('Y'), 'http://www.sustam.com'); ?>
		</div>

	</div>

</div>

<?php echo $this -> Html->script('neon-login')?>
