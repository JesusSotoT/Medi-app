<?php

// Carga configuraciones
include 'Lib/Commons.inc.php';
include 'configs.inc';

// Carga Básicos, APP, Routes, Model, Views
include 'Lib/Debugging.inc.php';
include 'Lib/APP.inc.php';
include 'Lib/Routes.inc.php';
include 'Lib/Controller/Controller.inc.php';
include 'Lib/Model/Model.inc.php';
include 'Lib/View/View.inc.php';

// Carga Semi-Básicos
include 'Lib/Controller/Component.inc.php';
include 'Lib/View/Helper.inc.php';

// Carga Rutas
include 'routes.inc';

$app = new APP();
$app -> exec();