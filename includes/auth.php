<?php

require_once __DIR__ . '/admin-helpers.php';

admin_iniciar_sesion();

$autenticado = !empty($_SESSION['usuario_id']);
$rol = $_SESSION['rol'] ?? '';

if (!$autenticado || !in_array($rol, ['admin', 'autor'], true)) {
    admin_redirect(admin_url('login.php'));
}