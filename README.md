# Sistema de solicitudes académicas (PHP + MySQL)

Aplicación web en PHP con persistencia en **MySQL** (XAMPP). Incluye paneles para **estudiantes**, **docentes** y **gestión administrativa** (solicitudes, reportes, datos maestros). Interfaz con **Tailwind CSS** vía CDN.

---

## Requisitos

| Requisito | Detalle |
|-----------|---------|
| PHP | **8.0 o superior** (`json`, `mbstring`, `fileinfo`, **`pdo_mysql`**) |
| Servidor web | Apache con PHP (p. ej. **XAMPP** en Windows) |
| MySQL | **MariaDB/MySQL** en XAMPP (base `solicitudes_academicas`) |
| Navegador | Cualquier navegador moderno |

---

## Instalación y ejecución (paso a paso)

### 1. Obtener el proyecto

- Clone el repositorio o copie la carpeta del proyecto en el directorio público del servidor.
- Con **XAMPP**, la ruta habitual es:
  - `C:\xampp\htdocs\Parcial2DeBa`

### 2. Ubicación y URL

- El **document root** de Apache debe poder servir esa carpeta.
- La URL base será del tipo:
  - `http://localhost/Parcial2DeBa/`

### 3. Iniciar Apache

1. Abra el **Panel de control de XAMPP**.
2. Inicie **Apache** y **MySQL**.

### 3b. Base de datos (phpMyAdmin)

1. Importe `database/solicitudes_academicas.sql` en phpMyAdmin (crea la base `solicitudes_academicas`, tablas y datos demo).
   - Opcional: ejecute antes `database/00_crear_base.sql` si la base aún no existe.
2. Conexión en `config/database.php`: host `127.0.0.1`, base `solicitudes_academicas`, usuario `root`, contraseña vacía (XAMPP por defecto). **`DB_ENABLED` debe estar en `true`.**
3. **Vaciar solo solicitudes** (conserva usuarios; para cargar trámites manualmente desde la web):
   ```bat
   C:\xampp\php\php.exe database\limpiar_solicitudes.php
   ```
4. **Ver solicitudes con toda la información** en phpMyAdmin: ejecute `database/crear_vista_solicitudes.sql` y abra la vista **`v_solicitudes_completa`** (une cabecera + detalle estudiante/docente + resolución formal).

### 4. Abrir la aplicación

1. En el navegador vaya a `http://localhost/Parcial2DeBa/` o `http://localhost/Parcial2DeBa/index.php`.
2. Si no hay sesión, se redirige a **Iniciar sesión** (`login.php`).

### 5. Credenciales

- Los usuarios demo están en MySQL (tablas `administradores`, `docentes`, `estudiantes`), definidos al importar `solicitudes_academicas.sql`.
- Ejemplo de administrador por defecto:
  - **Usuario (correo):** `admin@academico.edu`
  - **Contraseña:** `admin123`
- Docentes y estudiantes pueden iniciar con **documento** o **correo** y su **clave** (p. ej. `demo123` en los registros demo).

> **Nota:** No suba contraseñas reales a repositorios públicos. En producción use contraseñas hasheadas.

---

## Arquitectura (resumen)

| Capa | Ubicación |
|------|-----------|
| Configuración y arranque | `config/config.php` |
| Controladores | `app/Controllers/` |
| Vistas | `views/`, `partials/` |
| Modelos / almacenamiento | `app/Models/` (`storage.php`, `MysqlStorage.php`, `repository.php`, `data_dictionary.php`) |
| Servicios | `app/Services/` (`SolicitudesService`, `GestionAcademicaService`, etc.) |
| Datos | MySQL (`solicitudes_academicas`) |
| Adjuntos | `uploads/solicitudes/` |
| Entradas públicas | `index.php`, `login.php`, `logout.php`, `estudiante/*.php`, `docente/*.php`, `gestion/*.php` |

La clase `App\Controllers\Controller` expone `render()` para incluir cabecera, vista y pie.

---

## Autenticación

- Tras un login válido, la sesión guarda rol y redirige al **panel correspondiente** (estudiante, docente o gestión).
- Orden de búsqueda de credenciales: administradores → docentes → estudiantes.

---

## Front-end

- Estilos base: `assets/css/main.css` (complemento; el grueso del diseño es Tailwind por CDN).

## Datos de demostración

Al importar `database/solicitudes_academicas.sql` se cargan usuarios demo (estudiantes y docentes por sede Cúcuta y Ocaña). Las solicitudes puede crearlas desde la aplicación o vaciar la bandeja con `limpiar_solicitudes.php` y cargarlas manualmente.

---

## Pie de página

El texto del pie se define con `SITE_FOOTER_LINE` en `config/config.php` y se muestra en `partials/footer.php`.
