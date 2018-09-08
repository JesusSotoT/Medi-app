<?php
$prevSearch = null;
if(class_exists('BusquedaComponent'))
	$prevSearch = BusquedaComponent::get_keyword();
?>
<li id="search">
	<form method="get" action="<?php echo $this -> url(array('controller' => 'busqueda', 'action' => 'r'))?>" id="globalSearch">
		<input type="text" name="q" class="search-input" placeholder="<?php echo i('Buscar')?>" value="<?php echo $prevSearch?>"/>
		<button type="submit">
			<i class="entypo-search"></i>
		</button>
	</form>
</li>
<script>
	var globalSearch = function (){
		var form = $('#globalSearch');
		form.on('submit', function(e){
			e.preventDefault();
			window.document.location = form.attr('action') + '/' + form.find('INPUT[name=q]').val();
		});
	};
	globalSearch();
</script>

