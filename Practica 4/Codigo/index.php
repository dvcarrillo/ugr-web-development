<!--
Sistemas de Información Basados en Web
Curso 2017 - 2018
Práctica 3

Autores:
- David Vargas Carrillo (github.com/dvcarrillo)
- Arturo Cortés Sánchez (github.com/arturocs)

Archivo principal de la pagina que enlaza con el resto
-->

<?php
    require_once('db/db.php');

    // GET petition check
    if (isset($_GET['option'])) {
        $option = $_GET['option'];

        if (isset($_GET['item']))
            $item = $_GET['item'];
        else
            $item = 0;
    }
    else {
        $option = "index";
        $item = 0;
    }

    require_once("views/layout.php");
?>