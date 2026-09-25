<?php
/**
 * Configuración de correo SMTP (Gmail) — PLANTILLA.
 *
 * Cómo usar:
 * 1. Copia este archivo como config/mail.php:
 *      cp config/mail.example.php config/mail.php
 *    (o copia .env.example como .env y define las variables MAIL_*).
 * 2. En config/mail.php coloca tu correo Gmail en 'username' (línea 84)
 *    y 'from_email' (línea 88).
 * 3. Genera una CONTRASEÑA DE APLICACIÓN en tu cuenta de Google:
 *      https://myaccount.google.com/apppasswords
 *    (requiere verificación en 2 pasos activada) y pégala en 'password'
 *    (línea 86 de config/mail.php). NO uses tu contraseña normal de Gmail: Google la rechaza.
 *    Alternativa: define las variables de entorno MAIL_HOST, MAIL_PORT,
 *    MAIL_ENCRYPTION, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_EMAIL y
 *    MAIL_FROM_NAME (ver .env.example); si existen, tienen prioridad.
 * 4. No subas config/mail.php (ni .env) con secretos a repositorios públicos.
 *
 * Este archivo SÍ puede versionarse; config/mail.php contiene el secreto real.
 */

// Variables de entorno (opcional): aceptan la configuración y la contraseña
// de aplicación de Google sin escribir secretos en este archivo.
$env = function ($nombre) {
    $valor = getenv($nombre);

    return ($valor !== false && trim((string) $valor) !== '') ? trim((string) $valor) : null;
};

$envPassword = getenv('MAIL_PASSWORD');
$envPassword = ($envPassword !== false && trim((string) $envPassword) !== '') ? trim((string) $envPassword) : null;

return [
    // Servidor SMTP de Gmail.
    'host' => $env('MAIL_HOST') ?? 'smtp.gmail.com',
    // Puerto con STARTTLS.
    'port' => (int) ($env('MAIL_PORT') ?? 587),
    // Cifrado: 'tls' (puerto 587) o 'ssl' (puerto 465).
    'encryption' => $env('MAIL_ENCRYPTION') ?? 'tls',

    // Tu dirección de Gmail que envía los correos (línea 43).
    'username' => $env('MAIL_USERNAME') ?? '017200915e@uandina.edu.pe',
    // AQUÍ VA LA CONTRASEÑA DE APLICACIÓN de Gmail, línea 46
    // (16 letras, sin espacios). Se genera en https://myaccount.google.com/apppasswords
    'password' => $envPassword ?? 'xxxx xxxx xxxx xxxx',

    // Remitente visible en el correo (línea 49: tu Gmail).
    'from_email' => $env('MAIL_FROM_EMAIL') ?? '017200915e@uandina.edu.pe',
    'from_name' => $env('MAIL_FROM_NAME') ?? 'Revista Digital — Diálogo y Desarrollo Perú',

    // Minutos de validez del enlace de recuperación (el código usa 15 min fijos).
    'reset_expire_minutes' => 60,
];
