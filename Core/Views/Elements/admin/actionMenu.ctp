	<div id="actionMenu">
		<div id="actionBar">


			<?php echo $this -> Menu -> actionMenu(); ?>

			<div id="actionBtn">
				<div class="actionBarH">
					<div class="siteLive">
						<?php if(isset(self::$live_site) and !is_null(self::$live_site))
							echo $this -> Html -> link ( '<i class="entypo-monitor"></i>' . i('Ver Sitio'), self::$live_site, array ( 'target' => 'new' ) ); ?>
					</div>
					<div class="logOut">
						<?php echo $this -> Html -> link ( i('Salir') . ' <i class="entypo-logout right"></i>', array (
								'controller' => 'administradores',
								'action' => 'logout'
							) , array(), i('¿Estás seguro que deseas finalizar tu sesión y salir?')
						);
						?>
					</div>
					<div class="toolIcon">
						<?php if($this -> Menu -> check_actions()):?>
							<i class="text-info entypo-cog"></i>
						<?php endif;?>
					</div>
				</div>
			</div>

		</div>
	</div>