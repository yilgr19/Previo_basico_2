# Sistema de solicitudes académicas (PHP + JSON)

Aplicación web en PHP con persistencia en **MySQL** (XAMPP) o, si se desactiva, en archivos **JSON** en `data/`. Incluye paneles para **estudiantes**, **docentes** y **gestión administrativa** (solicitudes, reportes, datos maestros). Interfaz con **Tailwind CSS** vía CDN.

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
   - Si los nombres de catálogos muestran `??`, ejecute: `php database/fix_catalog_utf8.php`
2. Si prefiere migrar desde JSON en lugar del volcado SQL:
   ```bat
   C:\xampp\php\php.exe database\seed_from_json.php
   ```
3. Conexión en `config/database.php`: host `127.0.0.1`, base `solicitudes_academicas`, usuario `root`, contraseña vacía (XAMPP por defecto). `DB_ENABLED` en `true` usa MySQL; en `false` vuelve a JSON.
4. **Prueba exhaustiva de persistencia** (crea y borra datos de prueba):
   ```bat
   C:\xampp\php\php.exe database\test_integracion_bd.php
   ```
   Para conservar los registros creados: añada `--keep`.
5. **Ver estado actual** de respuestas y conteos:
   ```bat
   C:\xampp\php\php.exe database\verificar_registros.php
   ```
6. **Resetear solo solicitudes** (conserva usuarios; crea 4 trámites Cúcuta + 4 Ocaña):
   ```bat
   C:\xampp\php\php.exe database\limpiar_y_sembrar_solicitudes.php
   ```

### 4. Abrir la aplicación

1. En el navegador vaya a `http://localhost/Parcial2DeBa/` o `http://localhost/Parcial2DeBa/index.php`.
2. Si no hay sesión, se redirige a **Iniciar sesión** (`login.php`).

### 5. Credenciales

- Los usuarios demo están en MySQL (tablas `administradores`, `docentes`, `estudiantes`) o en `data/*.json` si `DB_ENABLED` es `false`.
- Ejemplo de administrador por defecto:
  - **Usuario (correo):** el campo `correo` del administrador.
  - **Contraseña:** el campo `clave` correspondiente.
- Docentes y estudiantes pueden iniciar con **documento** o **correo** y su **clave** en los JSON.

> **Nota:** Revise los archivos en `data/` para ver o crear cuentas de prueba. No suba contraseñas reales a repositorios públicos.

---

## Arquitectura (resumen)

| Capa | Ubicación |
|------|-----------|
| Configuración y arranque | `config/config.php` |
| Controladores | `app/Controllers/` |
| Vistas | `views/`, `partials/` |
| Modelos / almacenamiento | `app/Models/` (`storage.php`, `MysqlStorage.php`, `repository.php`, `data_dictionary.php`) |
| Servicios | `app/Services/` (`SolicitudesService`, `GestionAcademicaService`, etc.) |
| Datos | MySQL (`solicitudes_academicas`) o `data/*.json` |
| Entradas públicas | `index.php`, `login.php`, `logout.php`, `estudiante/*.php`, `docente/*.php`, `gestion/*.php` |

La clase `App\Controllers\Controller` expone `render()` para incluir cabecera, vista y pie.

---

## Autenticación

- Tras un login válido, la sesión guarda rol y redirige al **panel correspondiente** (estudiante, docente o gestión).
- Orden de búsqueda de credenciales: administradores → docentes → estudiantes.

---

## Front-end

- Estilos base: `assets/css/main.css` (complemento; el grueso del diseño es Tailwind por CDN).

## Datos de demostración (`data/`)

El repositorio incluye un juego mínimo de prueba: **2 estudiantes y 2 docentes por sede** (Cúcuta y Ocaña), **2 solicitudes** de ejemplo (una radicada por estudiante y una por docente) y el resto de JSON necesarios (`administradores.json`, `materias.json`, etc.). Puede ampliar registros editando los archivos o desde el panel de gestión.

---

## Pie de página

El texto del pie se define con `SITE_FOOTER_LINE` en `config/config.php` y se muestra en `partials/footer.php`.
