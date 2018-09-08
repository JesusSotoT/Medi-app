var responsiveHelper;
var breakpointDefinition = {
    tablet: 1024,
    phone : 480
};
var tableContainer;
var this_image = '';
var dataToSend = {};

$(document).ready(function()
{
///////////////////////////////Grid - listado///////////////////////////////////////

   if($("._grid").length){

		tableContainer = $("._grid");
		
		tableContainer.dataTable({
			"sPaginationType": "bootstrap",
			//"sDom": "t<'row'<'col-xs-6 col-left'i><'col-xs-6 col-right'p>>",
			"aLengthMenu": [[30, 45, 50, 100,-1], [30, 45, 50, 100, "All"]],
			"bStateSave": false,
			"iDisplayLength": 30,
			"aoColumns": aoColumns,
			// Responsive Settings
		    bAutoWidth     : false,
		    fnPreDrawCallback: function () {
		        // Initialize the responsive datatables helper once.
		        if (!responsiveHelper) {
		            responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
		        }
		    },
		    fnRowCallback  : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		        responsiveHelper.createExpandIcon(nRow);
		    },
		    fnDrawCallback : function (oSettings) {
		        responsiveHelper.respond();
		    }
		});

  }

 if($(".dataTables_wrapper").length){
	$(".dataTables_wrapper select").select2({
		minimumResultsForSearch: -1
	});
  }


	 //$($('.modal_images')).appendTo('body');
     //$($('#blueimp-gallery')).appendTo('body');

///////////////////////////////End Grid - listado///////////////////////////////////////

});