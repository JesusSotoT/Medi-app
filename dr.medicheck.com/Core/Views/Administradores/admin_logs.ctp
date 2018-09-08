<?php echo $this -> Menu -> addAction('Eliminar logs', 'fa fa-trash-o', array(
    'controller' => 'administradores', 
    'action' => 'cleanlogs'
));?>

<div class="tabs-vertical-env">
    <ul class="nav tabs-vertical">
        <!-- available classes "right-aligned" -->
        <?php foreach($logTypes as $t):?>
        <li>
            <a href="<?php echo $this -> url(array($t, 'controller' => 'administradores', 'action' => 'logsfilter'))?>">
                <?php echo i($t);?>
            </a>
        </li>
        <?php endforeach;?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="content_loader">

        </div>
    </div>
</div>

<script>
    $(function () {

        // CONTROLADOR PRINCIPAL
        $('.tabs-vertical LI A').on('click', function (e) {
            // INIT
            var target = $(this).attr('href'), parent = $(this).parent();
            e.preventDefault();
            $('.tabs-vertical LI').removeClass('active');
            $.ajax({
                url: target,
                success: function (data) {
                    parent.addClass('active');
                    $('.tab-content #content_loader').html(data);
                }
            })
        });

        // TRIGGER 
        $('.tabs-vertical LI:first-child A').click();

    });
</script>
