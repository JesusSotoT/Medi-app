<div class="row" id="UserHeaderDashboard">

	<!-- Profile Info and Notifications -->
	<div class="col-md-6 col-sm-8 clearfix">

		<ul class="user-info pull-left pull-none-xsm">

			<!-- Profile Info -->
			<li class="profile-info dropdown">
				<!-- add class "pull-right" if you want to place this from right -->

				<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <?php

					if ( is_null ( $this -> Session -> user ( 'imagenes_id' ) ) ) {

						echo $this -> Html -> img ( 'default-user.png', array (
							'class' => 'img-circle',
							'width' => '44px'
						) );

					} else {

						echo $this -> Html -> img ( array (
							'controller' => 'img',
							'action' => 'render',
							$this -> Session -> user ( 'imagenes_id' )
						), array (
							'class' => 'img-circle',
							'width' => '44px'
						) );

					}

				?>
				<?php echo $this -> Session -> user ( 'nombres' ); ?>
				<span class="badge"><?php echo $this -> Session -> user (MultidepartmentComponent::$roleFiledInUserSession);?></span>
				</a>

				<ul class="dropdown-menu">

					<!-- Reverse Caret -->
					<li class="caret"></li>

					<!-- Profile sub-links -->
					<li>
						<?php echo $this -> Html -> link('<i class="entypo-user"></i>' . i('Editar Perfil'), 
							array('controller' => 'administradores', 'action' => 'miperfil')); ?>
					</li>
                    <?php if(count($this -> Session -> user(MultidepartmentComponent::$roleList)) > 1):?>
                        <?php foreach($this -> Session -> user(MultidepartmentComponent::$roleList) as $role => $dep):?>
                            <li>
                                <?php echo $this -> Html -> link(sprintf('<i class="entypo-cog"></i> %s: ', i('Entrar como')) . $dep, 
                                array('controller' => 'administradores', 'action' => 'roleas', $role)); ?>
                            </li>

                        <?php endforeach;?>
                    <?php endif;?>
            
				</ul>
			</li>
		</ul>

        <?php $this -> element('admin/lang_selector')?>
        
	</div>
    
</div>