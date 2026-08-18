<?php
// Cuando PHP encuentra una clase que no conoce,
// llama a esta función con el nombre de la clase y buscamos su archivo.
spl_autoload_register(function (string $clase) {
    $ruta = __DIR__ . "/models/$clase.php";
    if (file_exists($ruta)) {
        require $ruta;
    }
});