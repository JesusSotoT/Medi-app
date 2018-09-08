<div class="pag_home">
	<?php echo $this -> element("header"); ?>
	<section class="bq_carousel" id="inicio">
		<!--<div class="logo">
			<p>
				<a href="<?php echo $this -> url(array('controller' => 'home')); ?>">
				<?php echo $this -> Html -> img('all/medicheck.png'); ?>
				</a>
			</p>
		</div>-->
		<div class="owl-carousel owl-theme">
		    <div class="item">
				<div class="col-xs-12 col-sm-6 cols col1">
					<?php echo $this -> Html -> img('home/medicine.png'); ?>
				</div>
				<div class="col-xs-12 col-sm-6 cols texto">
					<h1>Con Medicheck</h1>
					<p>
						Consulta tus medicamentos recetados.
					</p>
				</div>
			</div>
		    <div class="item">
		    	<div class="col-xs-12 col-sm-6 cols col1">
					<?php echo $this -> Html -> img('home/listdoctores.png'); ?>
				</div>
				<div class="col-xs-12 col-sm-6 cols texto">
					<h1>Accede</h1>
					<p>
						A un listado de médicos certificados.
					</p>
				</div>
		    </div>
		    <div class="item">
		    	<div class="col-xs-12 col-sm-6 cols col1">
					<?php echo $this -> Html -> img('home/calendario.png'); ?>
				</div>
				<div class="col-xs-12 col-sm-6 cols texto">
					<h1>Agenda</h1>
					<p>
						Tus citas con el médico de tu preferencia.
					</p>
				</div>
		    </div>
		    <div class="item">
		    	<div class="col-xs-12 col-sm-6 cols col1">
					<?php echo $this -> Html -> img('home/medicprof.png'); ?>
				</div>
				<div class="col-xs-12 col-sm-6 cols texto">
					<h1>Consulta</h1>
					<p>
						Tu registro médico en todo momento.
					</p>
				</div>
		    </div>
		</div>
	</section>
	<section class="bq_medicos" id="medicos">
		<div class="container">
			<div class="col-xs-12 col-md-12 col-lg-offset-1 col-lg-10">
			<div class="tbl">
				<div class="tcell">
					<h3>MÉDICOS</h3>
					<br>
					<p>
						Una  forma  fácil  y  práctica de  tener  la  información de  tus  pacientes   en   instantes.
					</p>
				</div>
				<div class="tcell">
					<?php echo $this -> Html -> img('home/circle.png'); ?>
				</div>
			</div>
			</div>
		</div>
	</section>
	<section class="bq_pasos bq_pas_medicos">
		<div class="container">
			<div class="col-xs-12 col-sm-4 cols">
				<?php echo $this -> Html -> img('home/m1.png'); ?>
				<p>
					Consulta el historial<br>
					de todos tus pacientes.
				</p>
			</div>
			<div class="col-xs-12 col-sm-4 cols">
				<?php echo $this -> Html -> img('home/m2.png'); ?>
				<p>
					Registra completo<br>
					de todos tus pacientes.
				</p>
			</div>
			<div class="col-xs-12 col-sm-4 cols">
				<?php echo $this -> Html -> img('home/m3.png'); ?>
				<p>
					Organiza a todos tus pacientes<br>
					de una forma práctica y segura.
				</p>
			</div>
		</div>
	</section>
	<section class="bq_comunidad" ">
		<div class="container">
			<h2>¿Te  gustaría  ser  parte  de  esta  comunidad?</h2>
			<br>
			<br>
			<p>
				Registrate <br> Medicheck será gratuito durante los primeros meses del 2017
			</p>
			<br>
			<br>
			<a href="#Modal" class="btn_circ" data-toggle="modal" data-target="#afiliate" role="button">REGISTRARME</a> 
		</div>
	</section>
	<section class="bq_listapac">
		<div class="bg"></div>
		<div class="container">
			<div class="col-xs-12 col-sm-12 col-md-offset-4 col-md-7 col_texto">
				<div class="tbl">
					<div class="tcell">
						<h3>Listado de Pacientes</h3>
						<br>
						<p>
							Tu  como  médico  registrado  en  MEDICHECK<br>
							tienes  la  facilidad  de  tener  un  listado<br>
							completo  de  tus  pacientes.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="bq_pacientes" id="pacientes">
		<div class="container">
			<div class="col-xs-12 col-md-12 col-lg-offset-1 col-lg-10">
			<div class="tbl">
				<div class="tcell">
					<?php echo $this -> Html -> img('home/circle2.png'); ?>
				</div>
				<div class="tcell">
					<h3>PACIENTES</h3>
					<br>
					<p>
						Tu  como  paciente se  parte  de  MEDICHECK descargando nuestra app.
					</p>
				</div>
			</div>
			</div>
		</div>
	</section>
	<section class="bq_pasos bq_pas_pacientes">
		<div class="container">
			<div class="col-xs-12 col-sm-4 cols">
				<?php echo $this -> Html -> img('home/p1.png'); ?>
				<p>
					Tu historial clínico<br>
					directo en tu smartphone.
				</p>
			</div>
			<div class="col-xs-12 col-sm-4 cols">
				<?php echo $this -> Html -> img('home/p2.png'); ?>
				<p>
					Registra tus<br>
					últimos analisis.
				</p>
			</div>
			<div class="col-xs-12 col-sm-4 cols">
				<?php echo $this -> Html -> img('home/p3.png'); ?>
				<p>
					Alarma para<br>
					los medicamentos.
				</p>
			</div>
		</div>
	</section>
	<section class="bq_directorio">
		<div class="container">
			<div class="col-xs-12 col-sm-12 col-md-offset-1 col-md-10 col_texto">
				<div class="tbl">
					<div class="tcell">
						<h3>Directorio</h3>
						<br>
						<p>
							Como paciente registrado en MEDICHECK tendrás la facilidad
							de tener un directorio con todos nuestros medicos activos
							en nuestra app.
						</p>
					</div>
					<div class="tcell">
						<?php echo $this -> Html -> img('home/circle3.png'); ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="bq_contacto" ng-app="" id="contacto">
		<div class="container">
			<div class="col">
				<form class="varios-form" id="form_all" name="formularioContacto" method="post" action="<?php echo $this -> url(array('controller' => 'home','action' => 'contact_send')); ?>" ng-submit="formularioContacto.$valid" novalidate>
					<h3>Contactanos</h3>
					<br>
					<br>
					<label class="label_rows">
						<span>Nombre:</span><br>
						<input class="inputs" type="text" name="contacto[nombre]" ng-model="mNombre" required >
					</label>
					<label class="label_rows">
						<span>Correo:</span><br>
						<input class="inputs" type="email" name="contacto[email]" ng-model="mEmail" ng-pattern="/^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/" required>
					</label>
					<label class="label_rows">
						<span>Mensaje:</span><br>
						<textarea class="inputs textareas" name="contacto[mensaje]" ng-model="mMensaje" required></textarea>
					</label>
					<br>
					<br>
					<br>
					<button type="submit" class="btn_circ" >ENVIAR</button>
			<!--		<button type="submit" class="btn_circ" ng-class="{'disabled_btn': formularioContacto.$invalid }" ng-disabled="formularioContacto.$invalid">ENVIAR</button>-->
				</form>
			</div>
		</div>
	</section>

	<div class="modal fade" id="afiliate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Registrarme</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
        	<div class="form-group">
			    <label for="inputName" class="col-sm-2 control-label">Nombre</label>
			    <div class="col-sm-10">
			      <input type="text" class="form-control" id="inputName" placeholder="Nombre">
			    </div>
		  	</div>
		  <div class="form-group">
		    <label for="especialidad" class="col-sm-2 control-label">Especialidad</label>
		    <div class="col-sm-10">
		      <input type="text" class="form-control" id="especialidad" placeholder="Especialidad">
		    </div>
		  </div>
		  <div class="form-group">
		    <label for="especialidad" class="col-sm-2 control-label">Correo Electrónico</label>
		    <div class="col-sm-10">
		      <input type="text" class="form-control" id="especialidad" placeholder="Especialidad">
		    </div>
		  </div>
		  <div class="form-group">
		    <label for="especialidad" class="col-sm-2 control-label">Teléfono</label>
		    <div class="col-sm-10">
		      <input type="text" class="form-control" id="especialidad" placeholder="Especialidad">
		    </div>
		  </div>
		  <div class="form-group">
		    <div class="col-sm-offset-2 col-sm-10">
		      <button type="submit" class="btn btn-default">Registrarme</button>
		    </div>
		  </div>
		</form>
      </div>
      <!--<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>-->
    </div>
  </div>
</div>
</div>