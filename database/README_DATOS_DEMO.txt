DATOS DE DEMOSTRACION - REVISTA DIGITAL

Base de datos: revista_digital

1. Importar datos_demo.sql

Opcion desde phpMyAdmin:

- Abre http://localhost/phpmyadmin/
- Selecciona la base revista_digital.
- Entra a Importar.
- Selecciona database/datos_demo.sql.
- Ejecuta la importacion.

Opcion desde consola de Windows:

cmd /c "C:\xampp\mysql\bin\mysql.exe -u root revista_digital < C:\xampp\htdocs\revista-digital\database\datos_demo.sql"

El SQL es idempotente: usa titulos, nicknames, numeros de boletin y correo conocidos para evitar duplicados si se importa mas de una vez.

Usuario administrador esperado:

- Correo: 017200915e@uandina.edu.pe
- La clave se guarda como hash seguro en MySQL; no debe quedar escrita en texto plano dentro de password_hash.

2. Imagenes que se deben colocar en assets/img/

- assets/img/reportaje-01.jpg
- assets/img/reportaje-02.jpg
- assets/img/reportaje-03.jpg
- assets/img/reportaje-04.jpg
- assets/img/reportaje-05.jpg
- assets/img/noticia-01.jpg
- assets/img/noticia-02.jpg
- assets/img/noticia-03.jpg
- assets/img/noticia-04.jpg
- assets/img/noticia-05.jpg
- assets/img/boletin-01.jpg
- assets/img/podcast-01.jpg
- assets/img/podcast-02.jpg
- assets/img/video-01.jpg
- assets/img/video-02.jpg

Nota: el esquema actual de MySQL no tiene columnas de imagen para podcasts ni videos. Esas imagenes quedan listadas por si se amplia el esquema, pero datos_demo.sql no puede guardarlas sin inventar columnas.

3. PDFs que se deben colocar en uploads/boletines/

- uploads/boletines/ntep-01.pdf
- uploads/boletines/ntep-02.pdf
- uploads/boletines/ntep-03.pdf

4. Verificacion de portada

- Abre http://localhost/revista-digital/public/index.php
- Confirma que aparece el reportaje destacado de demostracion.
- Confirma que se muestran tres reportajes, noticias recientes, el ultimo boletin, podcasts y videos.
- El menu principal debe mostrar Administrador entre Sobre D&D y Contacto.
- El enlace Administrador debe abrir http://localhost/revista-digital/public/admin/login.php
