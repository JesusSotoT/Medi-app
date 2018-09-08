<div id ="galleryGrid" data-ref="<?php echo $url;?>">
	
</div>

<hr/>

<form id="uploadable" class="dropzone" method = "POST" action="<?php echo $url;?>" >
	  <div class="fallback">
	    	<input name="dz[img1]" type="file" multiple />
	  </div>
</form>

<?php 
	echo $this -> Html -> css('dropzone/dropzone'); 
	echo $this -> Html -> script('dropzone/dropzone'); 
?>

<script>
	function update_gallery(){
	
		$.ajax({
			url: $('#galleryGrid').data('ref'),
			success: function(html){
				$('#galleryGrid').html(html);
			}
		});
		
	}
	
	update_gallery();
	
	// Elimina el error de dropzone loeaded
	Dropzone.autoDiscover = false;
	
	var Uploadable = new Dropzone("#uploadable");
	
	Uploadable.on('complete', function(file){
		this.removeAllFiles();
		update_gallery();
	});
	
</script>