# ClinAgenda

Un sistema pequeño para agendar las citas de los tres consultorios de la Dra. Paredes
(Clínica Centro, Norte y Sur) sin que se crucen, sin perder ninguna y sabiendo siempre
quién cambió qué.

Está hecho con PHP 8, MySQL y nada más: sin Composer, sin npm, sin nada que compilar.
Corre en XAMPP y funciona sin internet, porque cada clínica lo tiene instalado en su
propia computadora.

Para probarlo en cinco minutos, vaya a [docs/INSTRUCCIONES.md](docs/INSTRUCCIONES.md). Por qué
está hecho así, y qué quedó fuera, está en [docs/DECISIONES.md](docs/DECISIONES.md).

## Cómo arrancarlo

1. Copie la carpeta `clinagenda` dentro de `C:\xampp\htdocs\`.
2. En el panel de XAMPP, encienda **Apache** y **MySQL**.
3. Abra <http://localhost/clinagenda/>.

Eso es todo. La primera vez que se abre, el sistema crea la base `clinagenda` y carga los
datos de demostración solo (lo hace `lib/instalador.php` con los dos archivos de `sql/`).
No hace falta abrir phpMyAdmin ni importar nada.

Si su MySQL tiene contraseña, copie `config/local.example.php` como `config/local.php` y
póngala ahí. Ese archivo no se sube al repositorio.

Para volver a los datos de demostración, borre la base `clinagenda` en phpMyAdmin y
recargue la página: se vuelve a crear.

Use Apache y no el servidor de prueba de PHP (`php -S`): en Windows ese servidor atiende
un pedido a la vez y la prueba de dos recepcionistas guardando al mismo tiempo no sería real.

## Cómo está organizado

```
clinagenda/
  index.php            solo lleva a pantallas/
  README.md

  pantallas/           lo que se abre en el navegador, un archivo por pantalla
    index.php          entrar al sistema
    salir.php
    inicio.php         la pantalla de cuadros, según el rol
    agenda.php         ver el día, cancelar, deshacer, marcar atendida, imprimir la hoja
    agendar.php        agendar en tres pasos y cambiar la hora de una cita
    pacientes.php      buscar
    paciente.php       ficha, registrar y corregir datos
    auditoria.php      quién hizo qué y cuándo
    respaldo.php       descargar toda la base
    admin.php          cuentas, médicos y feriados
    manual.php         manual de uso para recepción
    sw.js              service worker (junto a las pantallas, para cubrirlas a todas)

  lib/
    auth.php         sesión, roles y permisos; carga el resto de lib/
    db.php           conexión PDO y manejo de errores
    instalador.php   crea la base la primera vez
    agenda.php       las reglas de las citas (el corazón)
    pacientes.php    registrar, buscar, posibles duplicados
    validacion.php   cédula, teléfono, correo, fechas, nombres
    auditoria.php    registrar(acción, entidad, antes, después)
    plantilla.php    cabecera, pie y piezas repetidas

  assets/            estilos.css, app.js, icono.svg, manifest.json
  config/            local.example.php
  sql/               01_esquema.sql y 02_datos_demo.sql
  docs/              DECISIONES.md e INSTRUCCIONES.md
  registro/          errores.log; se crea solo y no se sube al repositorio
```

Cada pantalla es un archivo corto y todas las reglas viven en `lib/`. Las pantallas no
escriben SQL: le piden las cosas a `lib/`. Así la raíz queda limpia: lo que se ve en el
navegador está en `pantallas/` y todo lo demás tiene su carpeta.

## Seguridad, en corto

- Contraseñas con `password_hash`. Después de cinco intentos fallidos hay que esperar un minuto.
- La sesión se cierra sola a los 20 minutos sin uso.
- Ninguna pantalla se abre escribiendo la dirección sin haber entrado, y cada acción revisa
  el permiso en el servidor. Los intentos de entrar donde no corresponde quedan en la auditoría.
- Todas las consultas son preparadas, y todo lo que se muestra pasa por `htmlspecialchars`.
- Todos los formularios llevan una ficha contra CSRF.
- Los errores técnicos van a `registro/errores.log`, nunca a la pantalla. El `.htaccess` impide
  abrir ese archivo, los `.sql` y los documentos desde el navegador.
- En el código no hay ni un `DELETE`: nada se borra.
