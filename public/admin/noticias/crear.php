<?php

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/admin-crud.php';

admin_requerir_modulo('noticias');
$config = admin_crud_config('noticias', $conexion);
admin_crud_formulario($conexion, $config);
