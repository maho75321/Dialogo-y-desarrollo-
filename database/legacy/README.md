# LEGACY — NO EJECUTAR

Archivos históricos conservados solo como referencia. Ya están
consolidados en `../revista_digital_consolidado.sql` (idempotente).

- `basedatos.txt` y `revista_digital.sql`: esquema viejo + `DROP TABLE`
  e `INSERT` con id fijo. Reejecutarlos **BORRA o duplica datos**.
- `actualizar_*.sql` y `migracion_*.sql`: migraciones ya aplicadas
  en la base viva; no son idempotentes (fallan o hacen `UPDATE` ciego).
- `restablecer_admin.php`: resetea la clave del admin a `Admin12345!`.
  Se movió aquí para sacarlo de la URL pública documentada
  (`database/restablecer_admin.php`). Si necesitas recuperar el acceso,
  cópialo temporalmente, ejecútalo UNA vez y ELIMÍNALO.

Instalación nueva: importa `../revista_digital_consolidado.sql` y luego,
si quieres contenido de prueba, `../datos_demo.sql`.
