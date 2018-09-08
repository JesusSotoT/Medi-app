<?php $this -> element('copy')?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="es"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $this -> title  . $this -> name?></title>
        <meta name="keywords" content="medicheck">
        <meta name="description" content="medicheck">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="author" content="">
        <meta name="copyright" content="">
        <meta name="creator" content="">
        <meta name="distribution" content="global">
        <meta name="rating" content="general">
        <meta name="robots" content="all">
        <meta name="revisit-after" content="14 days">


        <link rel="shortcut icon" href="<?php echo $this -> root?>images/favicon/favicon.png">

        <!-- Dublin Core -->
        <meta name="DC.title" content="">
        <meta name="DC.subject" content="">
        <meta name="DC.creator" content="">

        <!-- G+ -->
        <meta itemprop="name" content="medicheck">
        <meta itemprop="description" content="medicheck">
        <meta itemprop="image" content="medicheck">

        <!-- Facebook -->
        <meta property="og:image" content="" />
        <meta property="og:description" content="" />
        <meta property="og:site_name" content="medicheck"/>
        <meta property="og:title" content="" />
        <meta property="og:type" content="article">
        <meta property="og:url" content="" />

        <!-- CSS -->
        <?php echo $this -> Html -> css('bootstrap.min')?>
        <?php echo $this -> Html -> css('font-awesome')?>
        <?php echo $this -> Html -> css('styles')?>

        <!-- JS -->
        <?php // echo $this -> Html -> script('modernizr-2.6.2.min')?>
        <?php echo $this -> Html -> script('jquery');?>
        <?php echo $this -> Html -> script('angular.min');?>

    </head>
    <body>
        <?php $this -> element('analytics')?>

        <!--[if lt IE 7]>
            <p class="chromeframe">Tu navegador es <strong>Obsoleto</strong>. Por favor <a href="http://browsehappy.com/">actualizalo</a> o <a href="https://www.google.com/chrome/browser/">instala Google Chrome</a> para mejorar tu experiencia.</p>
        <![endif]-->

        <!--[if lt IE 8]>
            <p class="chromeframe">Tu navegador es <strong>Obsoleto</strong>. Por favor <a href="http://browsehappy.com/">actualizalo</a> o <a href="https://www.google.com/chrome/browser/">instala Google Chrome</a> para mejorar tu experiencia.</p>
        <![endif]-->

        <?php $this -> element('modals');?>
        <?php echo $this -> contents?>
        <?php $this -> element('footer');?>

        <!-- CSS Asy -->
            <?php echo $this -> Html -> css('owl.carousel2.4')?>
            <?php echo $this -> Html -> css('animate.min')?>
            <?php // echo $this -> Html -> css('toastr')?>
            <?php // echo $this -> Html -> css('jquery-ui.min')?>

        <!-- JS Asy -->
            <?php // echo $this -> Html -> script('jquery-ui.min');?>
            <!-- Anclas Suaves -->
            <?php echo $this -> Html -> script('jquery.easing.1.3.min')?>
            <!-- Owl Carousel -->
            <?php echo $this -> Html -> script('owl.carousel2.4.min')?>
            <!-- Bootstrap -->
            <?php echo $this -> Html -> script('bootstrap.min');?>
            <!-- Toastr -->
            <?php // echo $this -> Html -> script('toastr');?>

            <?php echo $this -> Html -> script('main.min')?>
        
    </body>
</html>
