# EduFolio — Portafolio Virtual Docente

Aplicación web tipo portafolio virtual para docentes (al estilo Classroom / Moodle).
Permite a los profesores registrarse, iniciar sesión y organizar su trabajo en
secciones: **Documentos, Notas, Material didáctico y Tareas**.

> Proyecto de investigación dividido en 3 fases de entrega.
> **Fase 1 (actual):** aplicación base + autenticación + estructura de secciones.

**Demo en línea:** <https://edufolio.freedev.app>
**Arquitectura del proyecto:** ver [ARQUITECTURA.md](ARQUITECTURA.md)

---

## Tecnologías

- **PHP 8.2** (con PDO)
- **MariaDB / MySQL**
- **HTML5 + CSS3 + JavaScript** (sin frameworks)
- **Bootstrap Icons** (local) · tipografía *Plus Jakarta Sans*

## Estructura del proyecto

```
CLASRROOM/
├── app/                     # Lógica (fuera del web root, protegida)
│   ├── config.php           # Configuración y conexión, sesiones seguras
│   ├── db.php               # Conexión PDO (singleton)
│   ├── auth.php             # Registro, login, logout, sesión
│   ├── helpers.php          # Escape, CSRF, flash, validaciones
│   └── layout/              # Plantillas (header, sidebar, footer, parciales)
├── public/                  # Web root (lo que sirve el servidor)
│   ├── index.php            # Portada pública
│   ├── login.php · registro.php · logout.php
│   ├── dashboard.php        # Panel principal
│   ├── documentos.php · notas.php · material.php · tareas.php
│   └── assets/              # CSS y JS
├── sql/
│   └── 01_schema.sql        # Esquema de base de datos
└── docs/                    # Documentación del proyecto de investigación
```

## Puesta en marcha (local con XAMPP)

1. **Crear la configuración:** copia `app/config.example.php` como `app/config.php`
   (el bloque local ya viene listo para XAMPP).
2. **Iniciar MySQL** desde el panel de control de XAMPP (o `C:\xampp\mysql_start.bat`).
3. **Importar la base de datos:**
   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root < sql/01_schema.sql
   ```
4. **Levantar el servidor** (servidor embebido de PHP):
   ```bash
   C:\xampp\php\php.exe -S 127.0.0.1:8077 -t public
   ```
5. Abrir en el navegador: <http://127.0.0.1:8077>

> Alternativa: copiar la carpeta a `C:\xampp\htdocs\` y servir con Apache,
> apuntando el navegador a `http://localhost/CLASRROOM/public/`.

## Seguridad implementada (Fase 1)

- Contraseñas cifradas con `password_hash()` (bcrypt).
- Consultas con sentencias preparadas (PDO) → previene inyección SQL.
- Tokens **CSRF** en todos los formularios.
- `session_regenerate_id()` al iniciar sesión → previene fijación de sesión.
- Escape de salida con `htmlspecialchars()` → previene XSS.
- Cookies de sesión `HttpOnly` y `SameSite=Lax`.


