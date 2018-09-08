<!-- Bottom Scripts -->
<?php

	// Scripting
	$js = array (
		'gsap/main-gsap',
		'jquery-ui/js/jquery-ui-1.10.3.minimal.min',
		'bootstrap',
		'joinable',
		'resizeable',
		'neon-api',
		'toastr',
		'neon-chat',
		'neon-custom',
		'jquery.validate.min',
		'jquery.dataTables.min',
		'datatables/TableTools.min',
		'dataTables.bootstrap',
		'datatables/jquery.dataTables.columnFilter',
		'datatables/lodash.min',
		'datatables/responsive/js/datatables.responsive',
		'select2/select2.min'
	);

	foreach ( $js as $script ) {
		echo $this -> Html -> script ( $script );
	}

	// Specials
	// DaterangePicker
	echo $this -> Html -> css ( 'daterangepicker/daterangepicker-bs2' );
	echo $this -> Html -> script ( 'daterangepicker/moment.min' );
	echo $this -> Html -> script ( 'daterangepicker/daterangepicker' );

	// Icheck
	echo $this -> Html -> css ( 'icheck/flat/blue' );
	echo $this -> Html -> script ( 'icheck/icheck.min' );

    // MULTISELECT
    echo $this -> Html -> css ('bootstrap-select');
    echo $this -> Html -> script ('bootstrap-select.min');