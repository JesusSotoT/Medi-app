<?php if(!isset($panelClass) or !is_string($panelClass)) $panelClass = "primary"; ?>
<?php if(!isset($origin) or is_null($origin)) die("ELEMENT[admin/panel] Requiere un array de origen para operar"); ?>

<div class="panel panel-<?php echo $panelClass?>" id="<?php echo $id ?>" >
   <div class="panel-heading">
        <div class="panel-heading">
            <div class="panel-title"><?php echo $title ?></div>

            <div class="panel-options">
                   
                <?php if(isset($allowRefreshIcon) and $allowRefreshIcon):?>
                        <a href="#" data-rel="reload"><i class="entypo-arrows-ccw"></i></a>
                <?php endif;?>
            </div>
        </div>
   </div>
   <div class="refreshable" data-origin ="<?php echo $this -> url($origin)?>"
       class="panel-body"></div>
</div>