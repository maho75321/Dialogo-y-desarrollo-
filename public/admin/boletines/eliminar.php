<?php

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/admin-crud.php';

admin_requerir_modulo('boletines');
$config = admin_crud_config('boletines', $conexion);
admin_crud_eliminar($conexion, $config);
