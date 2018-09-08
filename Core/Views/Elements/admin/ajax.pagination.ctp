<?php if($this -> Html -> is_paginated()):?>
    <div class="pagination-ajax-holder">
        <?php
        $this -> Html -> pagination_set('wrapper', 'UL.pagination.pagination-sm');
        $this -> Html -> pagination_set('active', '.page.active');
        echo $this -> Html -> paginate();
        ?>
    </div>
<?php endif;?>