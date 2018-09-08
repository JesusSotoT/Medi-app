<div class="login-header login-caret">

	<div class="login-content">

		<a href="" class="logo"> <?php echo $this -> Html -> img('logo@2x.png', array('width' => 190)); ?></a>

		<p class="description">
			Para finalizar el restaurado de contraseña, por favor escribe una nueva en los campos siguientes:
		</p>

		<!-- progress bar indicator -->
		<div class="login-progressbar-indicator">
			<h3>43%</h3>
			<span>Restaurando password.</span>
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

		<form method="post" role="form" id="passwordReset" action='<?php echo $this -> url($this -> request); ?>'>
			
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon">
						<i><span class="entypo-lock"></span></i>
					</div>
					<input type="password" class="form-control" required="required" placeholder="Contraseña nueva" name="reset[pass1]" id="pass1" autocomplete="off" />
				</div>
			</div>
			
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon">
						<i><span class="entypo-lock"></span></i>
					</div>
					<input type="password" class="form-control" required="required" placeholder="Confirma la contraseña" name="reset[pass2]" id="pass2" autocomplete="off" />
				</div>
			</div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block btn-login" >
					<i class="entypo-unlock"></i>
					Restaurar Contraseña
				</button>
			</div>
			
		</form>

		<div class="login-bottom-links">			
			<?php echo $this -> Html -> link('SUSTAM ' . date('Y'), 'http://www.sustam.com'); ?>
		</div>

	</div>

</div>

<?php echo $this -> Html->script('password-reset')?>