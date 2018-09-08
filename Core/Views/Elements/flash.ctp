<?php echo $this -> Html -> css('toastr')?>
<?php echo $this -> Html -> script('toastr') ?>
<?php if(!$this -> is_ajax()):?>
<script>
	var opts = {
		 "closeButton": true,
          "debug": false,
          "newestOnTop": false,
          "progressBar": false,
          "positionClass": "toast-top-right",
          "preventDuplicates": false,
          "onclick": null,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": 0,
          "extendedTimeOut": 0,
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut",
          "tapToDismiss": false
	};

	/**
	* Toaster Set Flash
	*
	* Implementación rápida.
	*
	* @Author Daniel Lepe
	* @Version 1.0
	*/

	function set_flash(msg, clase){
		switch (clase){
				case 'danger' :
					toastr.error(msg, '¡Error!', opts);
					break;
				case 'error' :
					toastr.error(msg, '¡Error!', opts);
					break;
				case 'success' :
					toastr.success(msg, '¡Perfecto!', opts);
					break;
				case 'warning' :
					toastr.warning(msg, 'Atención', opts);
					break;
				default :
					toastr.info(msg, 'Mensaje', opts);
					break;
		}
	}

	function setFlash(msg, clase){
		set_flash(msg, clase);
	}

</script>
<?php endif;?>

<?php if($this -> Session -> has_flash()):?>

<?php $flash_msgs = $this -> Session -> flash ( ); ?>

<script type="text/javascript">

	$(function() {
		<?php foreach ( $flash_msgs as $msg ) { echo sprintf ( 'set_flash("%s", "%s");', $msg [ 'msg' ],  $msg [ 'class' ] ); }?>
	});

</script>

<?php endif ?>