# Sistema de Gestión Avícola para Avícola María Clara

## Descripción General del Proyecto

Este proyecto consiste en el desarrollo de un **Sistema de Gestión Avícola** diseñado para la organización **Avícola María Clara**. La necesidad principal que aborda es la **accesibilidad y recolección centralizada de los datos** que se producen en cada etapa del proceso productivo.

El sistema busca solucionar estos problemas centralizando la información generada en las operaciones diarias de las granjas. Esto permitirá detectar fenómenos en los datos recolectados, mejorar los procesos internos, mantener la trazabilidad de los lotes de aves y vacunas, y fundamentar mejor las decisiones. 

## Desarrolladores

El proyecto fue desarrollado por estudiantes de la Licenciatura en Sistemas de Información de la Facultad de Ciencia y Tecnología de la Universidad Autónoma de Entre Ríos.
*   Götte, Brian Nahuel: bngotte@gmail.com
*   Grimaldi, Gerónimo de La Cruz: gerogrimaldi2@gmail.com
*   Murguia, Enzo Daniel: murguiaenzo@gmail.com

## Tecnologías utilizadas
Se emplearon tecnologías Web:
* **HTML, CSS, JavaScript** para el desarrollo de la capa de presentación.
* **PHP** para capa de aplicación.
* **MariaDB** como base de datos.

Todas las tecnologías seleccionadas son de software libre.

## Requisitos de Implementación
### Requisitos de Hardware

**Mínimos**:
*   Procesador: 1GHz o más rápido.
*   Tarjeta gráfica discreta (para salida de vídeo).
*   Memoria RAM: 2GB.
*   Almacenamiento: Al menos 20GB de espacio libre en disco.
*   Monitor: Resolución de 1366 x 768.
*   Periféricos: Teclado y mouse.

### Requisitos de Software
*   Sistema Operativo: Windows 10 o Linux con soporte para las últimas versiones de navegadores.
*   Servidor Web (ej. Apache, Nginx) con soporte para PHP.
*   PHP y MariaDB instalados.
*   Navegadores compatibles: Google Chrome y Mozilla Firefox (versiones al menos del 2024).

## Características Principales del Sistema
El sistema ofrece las siguientes funcionalidades clave:
*   **Control de Acceso**: Identificación de usuarios y limitación de funciones según el rol (Propietario, Encargado, Administrador de Sistema).
*   **Gestión de Granjas**: Permite el alta, baja y modificación de granjas, incluyendo el registro y reporte de mantenimientos efectuados.
*   **Gestión de Galpones**: Facilita el alta, baja y modificación de galpones dentro de una granja, con registro y reporte de sus mantenimientos.
*   **Gestión de Lotes de Aves**: Control completo de los lotes de aves desde la compra hasta la venta (o baja por mortandad), permitiendo la modificación de datos, asignación de galpones y registro de movimientos.
*   **Registro de Pesajes**: Permite registrar los pesajes realizados a los lotes de aves para obtener información sobre su condición.
*   **Gestión de Vacunas**: Permite el alta, baja y modificación de vacunas, incluyendo la gestión de sus respectivos lotes (número, fecha de compra, vencimiento, cantidad).
*   **Aplicación de Vacunas**: Registro detallado de las vacunas aplicadas a cada lote de aves, indicando el lote de vacuna utilizado, y generación de reportes.
*   **Registro de Mortandad**: Permite registrar las aves que mueren de un lote, con detalles sobre la causa y la cantidad, facilitando análisis futuros.
*   **Gestión de Compuestos**: Permite el registro de los compuestos comprados para la fabricación de alimentos balanceados.
*   **Gestión de Copias de Seguridad**: Ofrece la funcionalidad de crear archivos de respaldo de los datos del sistema.
*   **Gestión de Usuarios**: (Para Administrador del Sistema) Permite listar, modificar, agregar y eliminar usuarios, así como asignar permisos.

## Estructura de la Base de Datos

La base de datos utiliza el SGBD MariaDB y cuenta con las siguientes tablas principales:

*   `usuarios`: Almacena información de los usuarios que interactúan con el sistema (ID, nombre, email, dirección, teléfono, contraseña cifrada, fecha de nacimiento, tipo de usuario - `Propietario` o `Encargado`, y tokens de sesión: solo permite sesiones únicas por usuario).
*   `granja`: Guarda los detalles de cada granja (ID, nombre, habilitación SENASA, metros cuadrados, ubicación).
*   `tipoAve`: Define los diferentes tipos de aves (ID, nombre).
*   `galpon`: Contiene la información de los galpones (ID, identificación, tipo de ave asignado, capacidad, granja a la que pertenece).
*   `tipoMantenimiento`: Catálogo de tipos de mantenimiento que se pueden realizar (ID, nombre).
*   `mantenimientoGranja`, `mantenimientoGalpon`: Registran los mantenimientos realizados, especificando fecha, granja/galpón y tipo de mantenimiento.
*   `loteAves`: Almacena los datos de los lotes de aves (ID, identificador, fecha de nacimiento, fecha de compra, cantidad inicial de aves, tipo de ave, precio de compra).
*   `viaAplicacion`: Define las vías de aplicación de las vacunas (ID, vía).
*   `vacuna`: Contiene los detalles de las vacunas (ID, nombre, vía de aplicación, marca, enfermedad que previene).
*   `loteVacuna`: Registra los lotes específicos de vacunas adquiridas (ID, número de lote, fecha de compra, cantidad, fecha de vencimiento, vacuna asociada).
*   `bajaLoteAves`: Guarda información sobre la baja de lotes de aves, ya sea por venta o por otros motivos (ID, fecha de baja, precio de venta, motivo, lote de aves).
*   `pesajeLoteAves`: Registra los pesajes realizados a los lotes de aves (ID, fecha, peso, lote de aves).
*   `mortandadAves`: Detalla los eventos de mortandad de aves (ID, fecha, causa, cantidad, lote de aves).
*   `compuesto`: Almacena información sobre los compuestos utilizados en la alimentación (ID, nombre, proveedor).
*   `compra`: Registra las compras de compuestos (ID, granja, compuesto, cantidad, precio de compra, fecha de compra).
*   `galpon_loteAves`: Tabla de unión para el historial de ubicación de los lotes de aves en los galpones, con fechas de inicio y fin.
*   `loteVacuna_loteAve`: Tabla de unión para registrar la aplicación de lotes de vacunas a lotes de aves, con fecha y cantidad aplicada.

## Configuración del Entorno (Guía Rápida)
Para poner en marcha el proyecto, se requieren los siguientes pasos:

### Prerrequisitos
*   Un servidor web local (como XAMPP o WAMP para Windows, o LAMP para Linux) que incluya **PHP** y **MariaDB** (o MySQL compatible).
*   En caso de que se desee usar el inicio de sesión verificado por captcha, se debe realizar el procedimiento para adquirir una llave Captcha.

### Configuración de la Base de Datos
1.  **Editar `includes/config.php`**:
    *   `define('DB_HOST', '127.0.0.1');` // O la dirección de tu servidor de BD
    *   `define('DB_USER', 'root');` // Tu usuario de BD
    *   `define('DB_PASS', '');` // Tu contraseña de BD
    *   `define('DB_NAME', 'granjas');` // Nombre de la base de datos (se crea desde un apartado en caso de no existir)

2.  **Crear y Cargar la Base de Datos**:
    El proyecto incluye funcionalidades de prueba para la gestión de la base de datos, accesibles a través del `testController.php`. Puedes acceder a ellas en la interfaz del sistema bajo la opción `Test` si `TEST` está definido como `true` en `config.php`.
    En caso de estar definido, el acceso se permite desde "/index.php?opt=test" donde hay una serie de botones para ejecutar las funciones descriptas debajo:
    *   Para **crear la base de datos**: Ejecuta la función `crearBD()`.
    *   Para **crear las tablas**: Ejecuta la función `crearTablas()`, que utiliza el script `db/Tablas_granjas.sql`.
    *   Para **cargar datos de prueba**: Ejecuta la función `cargarDatos()`, que utiliza el script `db/Datos_granjas.sql`. (Las contraseñas de los usuarios de prueba son "12345678").
    *   Puedes realizar **Backups** con `backupDB()` y **Restaurar** con `restaurarBackupBD()`.

### Despliegue del Proyecto
1.  Clona o descarga el repositorio del proyecto.
2.  Coloca los archivos del proyecto en el directorio de tu servidor web (ej. `htdocs` para XAMPP).
3.  Asegúrate de que los archivos de la base de datos (`db/Tablas_granjas.sql` y `db/Datos_granjas.sql`) estén en el directorio `db/` dentro del proyecto.

## Uso del Sistema
### Roles de Usuario
El sistema distingue entre diferentes roles de usuario, cada uno con permisos específicos:
*   **Dueño (Propietario)**: Posee la mayor responsabilidad, gestiona aspectos económicos y toma decisiones generales. Tiene acceso completo a la gestión de granjas, galpones, usuarios y todas las operaciones.
*   **Encargado**: Controla y supervisa las operaciones diarias, registra datos y ejecuta actividades. Accede a la gestión de vacunas, lotes de aves (mortandad, pesaje, movimientos, aplicación de vacunas, bajas), compuestos y mantenimientos de granjas/galpones.

### Funcionalidades por Pantalla

El sistema cuenta con las siguientes vistas principales, accesibles a través de la navegación una vez iniciada la sesión:

*   **Inicio (`home`)**: Panel principal con accesos directos a las funciones administrativas y operativas.
*   **Login (`login`)**: Pantalla de inicio de sesión.
*   **Granjas (`granjas`)**: Interfaz para el alta, baja y modificación de granjas.
*   **Mantenimientos Granjas (`mantenimientos`)**: Registro y visualización de mantenimientos a nivel de granja.
*   **Galpones (`galpones`)**: Interfaz para el alta, baja y modificación de galpones.
*   **Mantenimientos Galpones (`mantenimientosGalpones`)**: Registro y visualización de mantenimientos a nivel de galpón.
*   **Vacunas (`vacunas`)**: Gestión de vacunas disponibles (alta, baja, modificación).
*   **Lotes de Vacunas (`lotesVacunas`)**: Gestión de los lotes específicos de vacunas adquiridas.
*   **Compuestos (`compuestos`)**: Gestión de compuestos para alimentos y registro de sus compras.
*   **Lotes de Aves (`lotesAves`)**: Gestión completa de los lotes de aves, incluyendo filtrado y reportes.
*   **Cargar Mortandad (`cargarMortandad`)**: Registro de aves muertas en un lote.
*   **Cargar Pesaje (`cargarPesaje`)**: Registro de los pesos de los lotes de aves.
*   **Cambiar Ubicación (`moverGalpon`)**: Funcionalidad para mover lotes de aves entre galpones o granjas.
*   **Aplicar Vacunas (`aplicarVacunas`)**: Registro de la aplicación de vacunas a los lotes de aves.
*   **Bajas (`bajaLote`)**: Gestión de la baja de lotes de aves (venta o fin de ciclo).
*   **Base de Datos (`database`)**: Opciones para realizar y restaurar copias de seguridad de la base de datos.
*   **Usuarios (`usuarios`)**: Gestión de los usuarios del sistema (alta, baja, modificación de datos y permisos).
*   **Test (`test`)**: Funcionalidades para pruebas de conexión, creación/borrado de BD y carga de datos/tablas.
