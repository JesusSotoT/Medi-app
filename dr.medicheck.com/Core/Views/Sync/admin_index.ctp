<div class="well">
    <p>Para importar datos de una base de datos a otra hay que tener 
    ambas configuradas en el archivo config.inc. 
    También hay que declarar cada una de las tablas en el controlador SyncController.</p>
</div>
 <div 
  class="toUpdate row"
  data-sync-action="<?php echo $this -> url(array('controller' => 'sync', 'action' => 'sync', 'table'))?>"
   >
    <?php foreach($to_import as $table):?>
    <div class="col-md-6" id="<?php echo $table?>">
        <h3 class="title"><?php echo $table?></h3>
        <p class="result"></p>
    </div>
    <?php endforeach;?>
</div>
<hr>
<a onclick="process_update();" class="btn btn-primary">Sincronizar tablas</a>

<script>
function process_update () {
    
    // INIT
    var tables = $('.toUpdate DIV'),
        origin = $('.toUpdate').data('sync-action');

    // NAMES LOOP
    $.each(tables, function(i, t){

        // SETS DESC
        $(t).find('p.result').text('En espera de proceder la sincronización.');

    });

    // UPDATE LOOP
    $.each(tables, function(i, t){
        // INIT
        var table = $(t).attr('id'),
            resultHolder = $(t).find('p.result'),
            url = origin.replace('table', table);

        // URL
        resultHolder.text('Requesting: ' + url);

        // REQUEST
        $.ajax({
            url: url,
            async: true,
            success: function (data){
                if(data.status){
                    resultHolder.text('Update OK: ' + data.rows + ' rows.');
                } else {
                    resultHolder.text('ERROR:' + data.msg);
                }
            }
        });
    });
}
</script>