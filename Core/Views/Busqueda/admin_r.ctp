<?php foreach($results as $modelResult): ?>
	<?php
	foreach ($modelResult['results'] as $kRow => $row) {
		foreach($row as $kCell => $cell){
			if($kCell != 'actions'){
				if($modelResult['modal']){
					$modelResult ['results'] [$kRow] [$kCell] = $this -> Js -> modal_link( $cell , $row['actions'], array('class' => 'btn btn-link'));
				} else {
					$modelResult ['results'] [$kRow] [$kCell] = $this -> Html -> link( $cell , $row['actions'], array('class' => 'btn btn-link'));
				}
			}
			unset($modelResult ['results'] [$kRow] ['id']);
			unset($modelResult ['results'] [$kRow] ['actions']);
		}
	}
	?>
	<div class="resultSet">
		<h4 class="resultSetTitle"><?php echo $modelResult['title']; ?></h4>
		<div class="searchResultSet">
			<?php echo $this -> Html -> dataTable($modelResult['results'])?>
		</div>
	</div>
<?php endforeach; ?>