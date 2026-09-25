<?php

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/admin-crud.php';

admin_requerir_modulo('autores');
$config = admin_crud_config('autores', $conexion);
admin_crud_eliminar($conexion, $config);
