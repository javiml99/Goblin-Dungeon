# Publicar Goblin Dungeon en goblindungeon.lol

Esta guía prepara el despliegue; no compra el dominio ni publica el juego.

## Qué hace falta

- Dominio: la dirección que escribirán los jugadores.
- Alojamiento con **PHP 8.4, PDO SQLite y almacenamiento persistente**.
- HTTPS y acceso al panel o SSH/SFTP para subir archivos.
- Apache 2.4 con mod_rewrite y AllowOverride para el .htaccess incluido.
  En Nginx hay que configurar reglas equivalentes: .htaccess no funciona allí.

GitHub Pages sólo sirve archivos estáticos y no ejecuta PHP.
El servidor `php -S` del ZIP se usa exclusivamente en local; no es el servidor público.

## Estructura recomendada

```text
/home/tuusuario/goblin/
  GoblinDungeon/       <- raíz pública del dominio
  var/                 <- SQLite y avatares, fuera de la raíz pública
  tools/               <- administración desde consola
```

Sube el código de la versión revisada, incluidos los archivos ocultos como
`.htaccess`. No subas el runtime Windows, CSV antiguos ni datos personales.
La raíz pública debe apuntar a **GoblinDungeon**, nunca a la raíz del repositorio.

El usuario de PHP necesita escribir en `var`. El código y las imágenes no necesitan
ser escribibles. No uses permisos 777.
Si el alojamiento impone otra estructura, configura `GOBLIN_DATA_DIR` con una ruta
absoluta fuera del directorio público.
**GOBLIN_LOCAL debe estar ausente o valer 0**.

## Pasos

1. Contratar/configurar el alojamiento una vez comprobado que ofrece PDO SQLite
   y permite situar los datos fuera de la raíz pública.
2. Crear la web en el panel, seleccionar PHP 8.4 y activar PDO SQLite.
3. Subir los archivos y establecer la raíz pública.
4. Probar en una dirección temporal con registro, login, partida y perfil.
5. En el proveedor del dominio, poner el registro A o CNAME que indique el
   alojamiento. No inventar la IP; usar la asignada por el proveedor.
6. Añadir goblindungeon.lol al alojamiento, emitir su certificado HTTPS y activar
   redirección HTTP a HTTPS. Elegir si www redirige al dominio principal.
7. Registrar tu cuenta. Con SSH, ejecutar `php tools/admin.php TU_USUARIO`
   desde la raíz privada del proyecto para convertirla en administradora.
8. Comprobar que /Modelos/DAO.class.php, /bootstrap.php, /csv/autenticacion2.csv
   y cualquier ruta a la base de datos devuelven 403/404.
9. Probar registro, cierre de sesión, móvil, subida PNG, permisos de administración
   y una partida completa antes de compartir la URL.

Si hay proxy inverso, configurar HTTPS y cookies seguras con el proveedor:
no confiar indiscriminadamente en cabeceras enviadas por el cliente.

## Copias y límites

Copiar SQLite mediante su API de backup o con la aplicación detenida; una copia
del fichero durante una escritura puede ser inconsistente. Copiar también avatares.
No sobrescribir `var` al desplegar versiones nuevas.
Las victorias son persistentes; las partidas en curso dependen de la sesión PHP.
La web y cada instalación Windows tienen cuentas y datos separados.

Antes de abrir registros públicos, ajustar límites de peticiones en el servidor
y documentar privacidad/conservación de cuentas. El correo es opcional y no se
recogen fechas de nacimiento. Aún no hay recuperación de contraseña ni verificación
de correo. Los hashes/cuentas antiguos permanecen en el historial del repositorio:
no reutilizar sus contraseñas como credenciales de producción.

Fuentes oficiales:
- https://docs.github.com/en/pages/getting-started-with-github-pages/what-is-github-pages
- https://www.php.net/manual/en/features.commandline.webserver.php
- https://www.php.net/manual/en/ref.pdo-sqlite.php
