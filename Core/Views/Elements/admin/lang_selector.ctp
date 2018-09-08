<?php if($this -> is_translatable()):?>
<ul id="langholder" class="list-inline ">
			
    <!-- Language Selector -->			
    <li class="dropdown language-selector">

        <a class="dropdown-toggle" href="#" data-toggle="dropdown" data-close-others="true">
            <?php echo $this -> Html -> img('flag-'  .  $this -> get_lang() . '.png');?>
        </a>

        <ul class="dropdown-menu language-list">
            <li>
                <?php echo $this -> Html -> link( $this -> Html -> img('flag-es.png') . "<span>Español</span>", array('controller' => 'administradores', 'action' => 'miperfil', 'lang' => "es"), array('class' => 'btn btn-xs btn-default'))?>
            </li>
            <li>
                <?php echo $this -> Html -> link( $this -> Html -> img('flag-en.png') . "<span>English</span>", array('controller' => 'administradores', 'action' => 'miperfil', 'lang' => "en"), array('class' => 'btn btn-xs btn-default'))?>
            </li>
        </ul>

    </li>

</ul>
<?php endif; ?>