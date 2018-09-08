<script>
	$(document).ready(function(){
		$(".ajax-form").submit(function(event){
			event.preventDefault();
			form = $(this);
			$.ajax({
				'url': form.attr('action'),
				'data': form.serialize(),
				'type': form.attr('method'),
				'success' : function(data){
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