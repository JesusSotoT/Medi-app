<header>
	<div class="">
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container-fluid">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
			        <span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
		      	</button>
		      	<!--<a class="navbar-brand" href="#">Brand</a>-->
		    </div>

		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      	<!--<ul class="nav navbar-nav">
			        <li class=""><a href="#">Link <span class="sr-only">(current)</span></a></li>
			        <li><a href="#">Link</a></li>
		      	</ul>		   -->
		      	<div class="row menu-list">
		      		<ul class="nav navbar-nav">
			      		<li class="col-sm-3 col-xs-3 col-md-3 col-lg-2 col-lg-offset-1">
				      		<button class="section--inicio" onclick="location.href='<?php echo $this -> url(array('controller' => 'Home')); ?>'">
				      			<?php echo $this -> Html -> img('home/medicheckapp.png'); ?>
				      		</button>
			      		</li>
			      		<li class="col-md-2">
				      		<button class="section--medicos">
				      		Para Médicos
				      		</button>
			      		</li>
			      		<li class="col-md-2">
				      		<button class="section--pacientes">
				      		Pacientes
				      		</button>
			      		</li>
			      		<li class="col-md-2">
				      		<button onclick="location.href='<?php echo $this -> url(array('controller' => 'Empresa')); ?>'" class="section--empresa">
				      		Empresa
				      		</button>
			      		</li>
			      		<li class="col-md-2">
				      		<button class="section--contacto" >
				      		Contacto
				      		</button>
			      		</li>
		      		</ul>
		      	</div>
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>
	</div>

</header>