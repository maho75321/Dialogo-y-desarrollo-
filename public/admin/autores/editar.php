<?php

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/admin-crud.php';

admin_requerir_modulo('autores');
$config = admin_crud_config('autores', $conexion);
$id = admin_id_parametro();

if (!$id) {
    admin_flash('error', 'Identificador no válido.');
    admin_redirect(admin_url('autores/index.php'));
}

admin_crud_formulario($conexion, $config, $id);
