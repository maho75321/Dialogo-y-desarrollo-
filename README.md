# Revista Digital — Diálogo y Desarrollo Perú (DDP Noticias)

Sitio **PHP 8 + MySQL/MariaDB (PDO, sin framework)** con portada pública que lee
datos reales y panel `public/admin/` con roles, borradores, biblioteca de
imágenes y recuperación de clave por Gmail SMTP (PHPMailer).

- URL pública: `http://localhost/revista-digital/public/index.php`
- Login: `http://localhost/revista-digital/public/admin/login.php`
- Admin: `017200915e@uandina.edu.pe` (clave inicial `Admin12345!`, cámbiala al entrar)

```
revista-digital/
├── index.php                  # Redirige a public/index.php
├── composer.json / .lock      # phpmailer/phpmailer
├── vendor/                    # PHPMailer (no versionar)
├── .env.example / .gitignore
├── config/
│   ├── database.php           # PDO: localhost/root/sin clave/revista_digital
│   ├── mail.php               # SMTP real (NO versionar, tiene secreto)
│   └── mail.example.php       # Plantilla sin secreto
├── includes/                  # helpers, upload+biblioteca, editor, auth, admin-*
├── public/
│   ├── index.php              # Portada (destacado, listados, boletín, podcast, video)
│   ├── reportajes.php / reportaje.php (ficha + galería + PDF)
│   ├── noticias.php / noticia.php (ficha con contenido + fuente externa)
│   ├── boletines.php, podcasts.php, videos.php (listados con paginación)
│   ├── alianzas.php, sobre.php, contacto.php (formulario SMTP real), mapa.php
│   └── admin/                 # login, dashboard, CRUD por módulo, recuperar clave
├── database/
│   ├── revista_digital_consolidado.sql  # ÚNICO SQL canónico (idempotente)
│   ├── datos_demo.sql                   # Contenido [Demo] idempotente (opcional)
│   ├── README_DATOS_DEMO.txt
│   └── legacy/                # Históricos NO ejecutables (ver su README)
├── assets/ (css, js, fonts, img, PDFs sueltos) / uploads/ (boletines, podcasts…)
```

## 1. Instalación en XAMPP

1. Instala XAMPP (PHP 8 + MySQL/MariaDB) e inicia Apache y MySQL
   (`sudo /opt/lampp/lampp start` en Linux).
2. Copia el proyecto a `/opt/lampp/htdocs/revista-digital`
   (Windows: `C:\xampp\htdocs\revista-digital`).
3. Crea la BD importando **en este orden** desde phpMyAdmin:
   1. `database/revista_digital_consolidado.sql` (crea las 9 tablas; repetible
      sin borrar nada; incluye admin inicial y los ALTER de `estado`,
      `contenido`, `archivo_audio` y columnas de recuperación).
   2. Opcional: `database/datos_demo.sql` (contenido `[Demo]` idempotente;
      no toca `password_hash` existentes).
4. `composer require phpmailer/phpmailer` solo si falta `vendor/autoload.php`.
5. Gmail SMTP: copia `config/mail.example.php` a `config/mail.php` (o usa `.env`
   con `MAIL_PASSWORD`) y pega la **contraseña de aplicación** Google
   (https://myaccount.google.com/apppasswords, requiere 2 pasos).
   Sin ella el correo queda deshabilitado con mensaje administrativo
   (solo con `APP_ENV=local` se muestra el código en pantalla).
6. Verifica: portada, login, `php -l` (ver §10).

> `database/legacy/` contiene `basedatos.txt`, `revista_digital.sql` y las
> 6 migraciones sueltas: **no los ejecutes** (hacen `DROP`, `INSERT` con id
> fijo o `UPDATE` ciego). `restablecer_admin.php` también se archivó ahí para
> sacarlo de la URL pública; si pierdes el acceso, restáuralo temporalmente,
> úsalo una vez y elimínalo.

## 2. Base de datos (9 tablas)

| Tabla | Columnas clave | Notas |
|---|---|---|
| `usuarios` | `nombres, ap_paterno, ap_materno, email UQ, password_hash (bcrypt), rol admin/autor` | Login del panel. **No confundir con `autores`.** |
| `autores` | `nombres, ap_paterno, ap_materno, nickname, es_nickname` | Firma editorial de reportajes (`NULL` = Redacción). No son logins. |
| `reportajes` | `titulo, resumen_corto, desarrollo (LONGTEXT HTML), foto_principal, pdf_adjunto, fecha_publicacion, es_destacado (solo 1 en 1), autor_id, usuario_id, estado borrador/publicado` | Propiedad por `usuario_id`; `autor_id` es solo firma. |
| `reportajes_fotos` | `reportaje_id FK CASCADE, url_foto, orden, descripcion` | Galería; se borra con su reportaje. |
| `noticias` | `titulo, foto, link_externo, fecha_publicacion, usuario_id, estado, contenido (TEXT HTML)` | Si hay `link_externo`, la tarjeta enlaza fuera. |
| `boletines` | `numero_boletin UQ, resumen, foto_portada, archivo_pdf NN, fecha_publicacion, usuario_id` | **Sin `estado`**: siempre visibles. |
| `podcasts` | `titulo, url_embed NULL (YT/YTM/Spotify normalizado), archivo_audio NULL, fecha_publicacion, usuario_id, estado` | Publicar exige audio **o** enlace. |
| `videos` | `titulo, url_embed NN, fecha_publicacion, usuario_id, estado` | Solo YouTube (watch, youtu.be, music, shorts, embed, live). |
| `recuperacion_password` | `usuario_id FK CASCADE, token_hash UQ, codigo_hash, expira_en (10 min), intentos/intentos_max (5), usado` | Códigos de 6 dígitos (`random_int`, solo SHA-256 guardado). |

Consultas públicas filtran `estado='publicado'` (salvo boletines).
Los borradores son invisibles en portada, listados y fichas directas por id.

## 3. Arquitectura 

- **Portada y fichas** (`public/*.php`): solo leen con PDO preparado y pintan
  con `includes/header.php` + `footer.php` + `assets/css/style.css`.
  Para cambiar diseño, toca esos tres; la lógica de datos está arriba de cada
  archivo en 10–30 líneas.
- **Panel** (`public/admin/` + `includes/admin-*.php`): sesión, CSRF,
  permisos por rol y propiedad (`usuario_id`), subidas y biblioteca.
- **`includes/helpers.php`**: utilidades públicas (escape, fechas, media,
  embeds, sanitización HTML, tarjetas, paginado). Si algo se muestra mal en
  la web, se mira aquí.
- **`includes/upload.php`**: reglas de subida (5 MB imagen, 8 MB PDF,
  25 MB audio, lista blanca + MIME real + `.htaccess`) y **biblioteca de
  imágenes** (`assets/img/`, `assets/img/uploads/`, `uploads/*`).
- **`includes/admin-crud.php`**: CRUD genérico (noticias, boletines, podcasts,
  videos, autores) + formularios especiales de reportajes (principal, PDF,
  galería) y usuarios.
- **`includes/editor.php`**: barra `contenteditable` (negrita, títulos H2–H4,
  listas, enlaces, citas…) sin dependencias; `sanitizar_html()` deja solo
  `p, br, strong, b, em, i, u, h2–h4, ul, ol, li, blockquote, a, span`.
- **`config/`**: `database.php` (PDO) y `mail.php` (SMTP + cargador `.env`).

## 4. Módulos públicos 

- **`public/index.php`**: destacado único (`es_destacado=1`, si no hay muestra
  estado vacío, nunca promociona otro), 3 reportajes, 3 noticias, último
  boletín (enlace solo si el PDF existe en disco), 4 podcasts (audio local →
  iframe Spotify/YT/YTM → enlace, con modal Ampliar), 4 videos (iframe +
  miniatura YT + modal), bloque Nosotros. Cambiar orden/cantidades: los
  `LIMIT` de las 6 consultas.
- **`reportajes.php` / `reportaje.php`**: listado con paginación de 9
  (`?pagina=`) y ficha con foto principal, cuerpo enriquecido, galería y PDF
  adjunto (solo si existe). Borradores: 404 aunque se conozca el id.
- **`noticias.php` / `noticia.php`**: igual con paginación de 9; la tarjeta
  va a `link_externo` si existe o a la ficha (`contenido` + botón fuente).
- **`boletines.php`**: paginación de 9; cada tarjeta enlaza al PDF **solo si
  el archivo existe** (`boletinPdfDisponible()`), si no muestra aviso en vez
  de un `href=""` roto.
- **`podcasts.php`**: paginación de 12; reproductor integrado sin salir
  (audio `<audio>`, o `<iframe>` Spotify/YouTube/YouTube Music vía
  `embedUrl()`+`iframeSeguro()`), botón Ampliar (modal global del footer) y
  enlace a plataforma. URLs inválidas no generan enlaces rotos.
- **`videos.php`**: paginación de 12; mismo patrón (iframe + miniatura
  `miniaturaYoutube()` + Ampliar).
- **`contacto.php`**: formulario que **envía de verdad** por SMTP a
  `info@dialogoydesarrollo.com.pe` (PHPMailer, `Reply-To` del visitante,
  mensajes `.aviso-ok/.aviso-error`); sin `MAIL_PASSWORD` muestra aviso y
  conserva el enlace `mailto:`.
- **`alianzas.php`**: contenido institucional estático + CTA a contacto
  (no tiene tabla; cuando exista `aliados`, se lista aquí).
- **`sobre.php`**: página institucional estática. **`mapa.php`**: iframe
  ArcGIS con enlace alternativo si no carga; enlazada desde el footer.
- **`header.php`**: logo (`assets/img/logo.png|jpg|jpeg|webp`, si no hay muestra
  `DDP`), nav (Actualidad → `noticias.php`), botón Contacto.
  **`footer.php`**: redes, mapa de Contenido (incluye Mapa), contacto, botón
  subir (`#movetop`, oculto hasta scroll) y **modal global `#modal-media`**
  (audio/iframe, cierra con ×/fondo/Escape y destruye el player).
  **`assets/js/main.js`**: navbar, scroll, movetop y modal.

## 5. Panel admin 

- **Auth**: `login.php` (`password_verify`, sesión regenerada,
  `$_SESSION[usuario_id|rol]`), `logout.php`, `cambiar-password.php`.
  **Roles**: `admin` todo (dashboard, 7 módulos, destacar, usuarios);
  `autor` solo sus noticias/reportajes propios (`usuario_id`, validado en
  servidor, 403 si toca lo ajeno) + su clave. Todo pasa por
  `admin_requerir_modulo()` / `admin_requerir_propiedad()`.
- **Dashboard** (`admin/index.php`): contadores (`admin_contar`, el autor solo
  los suyos) y accesos crear/gestionar filtrados por permiso.
- **CRUD genérico** (noticias, boletines, podcasts, videos, autores):
  `public/admin/<mod>/index|crear|editar|eliminar.php` delegan a
  `admin_crud_*`. Cada campo de imagen ofrece: ver actual, **borrar** (checkbox,
  borra físico solo si vive en `assets/img/uploads/` o `uploads/`), **elegir
  de la galería** (`assets/img`, `logo.jpg` incluido, o `uploads/`, validada
  contra traversal) o **subir desde PC**. PDF/audio: ver + borrar + subir.
  Borradores: “Guardar borrador” (sin exigir campos) vs “Publicar” (exige
  obligatorios y lista qué falta).
- **Reportajes** (formulario propio): título/fecha/autor/destacado (solo admin,
  transacción que deja un único `es_destacado=1`), resumen, contenido
  enriquecido, imagen principal (ver/borrar/elegir/subir), PDF (ver/borrar/
  subir), galería actual (descripción, orden, eliminar con borrado físico) y
  agregar (5 subidas + checkboxes de la biblioteca). Eliminar reportaje borra
  su galería por FK en cascada.
- **Usuarios** (solo admin): crear/editar (email único, clave ≥ 8, no puedes
  quitarte tu propio admin ni eliminarte).
- **Recuperación**: `recuperar-password.php` (mensaje genérico, código de
  6 dígitos `random_int`, guarda solo SHA-256, expira 10 min, invalida previos)
  → `verificar-codigo.php` (`hash_equals`, 5 intentos, consume el código) →
  `nueva-password.php?token=` (token interno en sesión, `password_hash`).

## 6. Funciones que más vas a tocar

- `e()`, `formatearFecha()`, `formatearNumeroBoletin()` – pintado.
- `rutaMedia()/mediaDisponible()/boletinPdfDisponible()/htmlImagen()` –
  si una imagen o PDF no sale, el problema suele estar aquí o en la ruta BD.
- `embedUrl()/iframeSeguro()/podcastCardDatos()/videoCardDatos()` – qué
  enlaces de Spotify/YouTube se aceptan y cómo se incrustan.
- `obtenerPaginado()` – paginación de los 4 listados (9 o 12 por página).
- `nombreAutorBase()` – firma (compartida por `mostrarAutor()` y panel).
- `admin_biblioteca_imagenes()/admin_biblioteca_validar()` – qué fotos ofrece
  la galería del admin y qué rutas acepta.
- `admin_guardar_archivo_subido()` – límites y tipos de subida.

## 7. Seguridad 

Consultas 100 % preparadas; sesiones con CSRF en cada POST de admin;
contraseñas solo `password_hash/password_verify`; subidas con
`is_uploaded_file`, extensión + MIME real (`finfo`), bloqueo de ejecutables
(`php, js, html, svg…`), nombres aleatorios y `.htaccess`; HTML del editor
sanitizado al guardar y al mostrar; correos con PHPMailer/SMTP y secretos
fuera de Git (`.gitignore` cubre `.env`, `config/mail.php`, `vendor/`).

## 8. Optimización aplicada 
Botones muertos arreglados: contacto ahora envía por SMTP (antes `mailto:`
como `action`, inútil en móvil); PDFs de boletín solo enlazan si el archivo
existe; podcasts/videos nunca generan `href` con URLs crudas inválidas;
Alianzas tiene contenido + CTA en vez de mensaje de BD; Mapa tiene enlace
alternativo y entrada en el footer; Actualidad apunta a `noticias.php`;
modal movido al footer global (Ampliar funciona también en
podcasts/videos); tarjetas sin medio ya no fingen acción (sin “Ver todos”
falso); paginación en noticias/boletines/podcasts/videos como reportajes;
`#movetop` oculto hasta scroll. Código: `nombreAutorBase()` único,
`admin_url_valida()` delega en `urlExternaValida()`, alias de normalización
media, `admin_recortar()` multibyte, `podcastCardDatos()/videoCardDatos()/
obtenerPaginado()/boletinPdfDisponible()` (una sola fuente de verdad),
contacto con `Reply-To`. SQL: `revista_digital_consolidado.sql` canónico e
idempotente (probado: doble importación OK, 9 tablas); originales archivados
en `database/legacy/` (incluido `restablecer_admin.php`, fuera de URL pública).

## 9. Pendientes conocidos

- `uploads/boletines/` está vacío: los boletines muestran “PDF no disponible”
  hasta que subas los PDF desde el panel.
- `assets/img/` tiene pocas fotos semilla: la biblioteca del admin lista lo
  que haya en `assets/img/` y `uploads/*`.
- `style-starter.css` (plantilla, ~12 k líneas) se usa <10 %: candidato a
  purgar/minificar con herramienta (no a mano).
- Sin buscador ni filtros públicos; `esUrlEmbebible()` quedó en desuso
  (se usa `videoCardDatos()`).

## 10. Comandos útiles

```bash
/opt/lampp/bin/php -l public/index.php public/admin/login.php \
  public/admin/recuperar-password.php public/admin/verificar-codigo.php \
  public/admin/nueva-password.php includes/helpers.php \
  includes/admin-crud.php includes/upload.php
/opt/lampp/bin/mysql -u root revista_digital < database/revista_digital_consolidado.sql
/opt/lampp/bin/mysql -u root revista_digital < database/datos_demo.sql
/opt/lampp/bin/mysql -u root -e "SELECT id,nombres,email,rol FROM revista_digital.usuarios;"
```
