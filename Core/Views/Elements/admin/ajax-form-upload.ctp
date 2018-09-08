<script>
	$(document).ready(function(){
		$(".ajax-form-upload").submit(function(event){
			event.preventDefault();
			var data = new FormData();
			$('input[type=file]').each(function(){
				data.append($(this).attr('id'), this.files[0]);
			});
			
			var form = $(this);
			var dataform = form.serializeArray();
			
			$(dataform).each(function(){
				data.append(this.name, this.value);
			});
			
			
			$.ajax({
				url: form.attr('action'),
				data: data,
				type: form.attr('method'),
				cache: false,
			    contentType: false,
			    processData: false,
				success : function(data){
					console.log(data);
					set_flash(data.msg,data.class);
					if(data.close == undefined || data.close == '1'){
						$('#' + $('.modal.fade.in').attr('id')).modal('hide');
					}
				}
			});
		});
	});
</script>