<?php $this -> Menu -> addAction('Regresar a listado', 'entypo-list', array('action' => $context, 'controller' => 'administradores')); ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Edición de permisos</h3>
	</div>

	<div class="panel-body">
		<p>
			Los siguientes permisos se pueden atribuir a todo un departamento en general y se pueden adaptar específicamente para cada usuario, si desea que un usuario tenga permisos distintos a los que tiene su respectivo departamento en algún contexto, haga clic en Administradores, edite los permisos del usuario y declare explítamente si alguno de ellos deberá ser distinto al del departamento.
		</p>
	</div>
	<form action="<?php echo $this -> url(array('controller'=>'administradores', 'action'=>'permisos', $id, $target_context))?>" id="permisos">

		<table class="table">

			<?php foreach($permisos as $p):?>
			<tr>
				<td><label><?php echo $p['titulo']?>:
				</label></td>
				<td>
				<input name="permisos[<?php echo $p['id']?>]" type="radio" <?php if($p['permiso'] and !$p['heredado']):?> checked="checked" <?php endif ?> class="icheck" value="1" >
				<label for="minimal-checkbox-1-15">Permitir</label></td>
				<td>
				<input  name="permisos[<?php echo $p['id']?>]"  type="radio" <?php if(!$p['permiso'] and !$p['heredado']):?> checked="checked" <?php endif ?> class="icheck" value="0" >
				<label for="minimal-checkbox-1-15">Bloquear</label></td>
				<?php if($context == 'administradores'):
				?>
				<td>
				<input  name="permisos[<?php echo $p['id']?>]"  type="radio" <?php if($p['heredado']): ?> checked="checked" <?php endif; ?> class="icheck" value="UNSET" >
				<label for="minimal-checkbox-1-15">Definido por el Rol
<!--				    (<?php if($p['SUP']):?> Permitir <?php else: ?> Bloquear <?php endif; ?>)-->
				</label></td>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</table>

		<div class="panel-footer">
			<button type="submit" class="btn btn-primary">
				Aplicar
			</button>
			<?php echo $this -> Html -> link ( 'Restaurar', $this -> request, array ( 'class' => 'btn btn-default' ) ); ?>
		</div>

	</form>

</div>

<?php echo $this -> Html -> css ( 'icheck/flat/_all' ); ?>

<?php echo $this -> Html -> script ( 'icheck/icheck.min' ); ?>

<script type="text/javascript">
	// Styles
	$(function() {
		$('input.icheck').iCheck({
			checkboxClass : 'icheckbox_flat-purple',
			radioClass : 'iradio_flat-purple'
		});
	});

	$('#permisos').submit(function(e) {
		e.preventDefault();
		$.ajax({
			data : $(this).serializeArray(),
			url : $(this).attr('action'),
			method : 'POST',
			success : function(data) {
				$('#action').html(data);
			},
			error : function(fun) {
				console.log(fun);
			}
		});
	});

</script>
