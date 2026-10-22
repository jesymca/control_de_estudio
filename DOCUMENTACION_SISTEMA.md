# 🏛️ Documentación Técnica y Funcional del Sistema de Control de Estudios (UPTPC)

**Universidad Territorial Politécnica de Puerto Cabello**  
*Sistema de Gestión Académica, Control de Estudios y Administración Universitaria*

---

## 📋 1. Visión General del Sistema

El **Sistema de Control de Estudios (UPTPC)** es una plataforma web integral diseñada para centralizar, auditar y automatizar la gestión académica y administrativa de la **Universidad Territorial Politécnica de Puerto Cabello** (Sede Principal y Complejo Educativo COEF).

### 🎯 Objetivos Principales:
- **Gestión Académica Unificada**: Registro y control de estudiantes, docentes, mallas curriculares, secciones y asignación de materias por trayecto.
- **Transparencia en Calificaciones**: Carga de notas por los docentes, validación por Directores de Carrera y emisión automática de actas y constancias.
- **Multiperfil y Control de Acceso**: Roles diferenciados para Administradores, Directores de Carrera, Docentes, Estudiantes y Super Usuarios.
- **Auditoría e Integridad de Datos**: Trazabilidad completa de cambios de notas, registros de visitas y seguridad del sistema.

---

## 💻 2. Arquitectura Tecnológica y Stack

- **Lenguaje Principal**: PHP 8.x (Backend modular optimizado)
- **Base de Datos**: MySQL / MariaDB (Motor InnoDB, codificación `utf8mb4`)
- **Interfaz de Usuario (Frontend)**: HTML5, CSS3, JavaScript (ES6+), Bootstrap 4 / 5, jQuery
- **Librerías de Visualización**: DataTables (para tabulación interactiva), FontAwesome 5/6 (Iconografía)
- **Generación de Documentos PDF**: TCPDF / FPDF (Reportes de estudiantes, constancias y actas)
- **Seguridad**: Hashing de contraseñas, sanitización `mysqli_real_escape_string`, control de sesiones y auditoría de peticiones (`visita()`).

---

## 🗂️ 3. Estructura de Módulos y Directorios del Proyecto

```
control_de_estudio/
│
├── admin/                         --> Panel de Administración General
│   ├── estudiantes.php            --> Lista, filtro avanzado y búsqueda universal de estudiantes
│   ├── agregar_estudiante.php     --> Registro individual y carga masiva CSV
│   ├── add_docente.php            --> Gestión de docentes (Títulos, especialidades, potencialidades)
│   ├── carreras.php               --> Catálogo de carreras institucionales
│   ├── soporte.php                --> Centro de atención técnica y soporte
│   └── includes/                  --> Head, Footer y modales del módulo Admin
│
├── director_de_carrera/           --> Panel de Directores de Carrera
│   ├── index.php                  --> Dashboard principal del Director
│   ├── asignar_docentes.php       --> Asignación de carga académica a docentes
│   ├── asignar_secciones.php      --> Asignación y cupos de secciones por trayecto
│   ├── asignar_voceros.php        --> Designación de voceros estudiantiles
│   └── soporte.php                --> Centro de soporte para Directores
│
├── docente/                       --> Panel de Docentes
│   ├── index.php                  --> Dashboard y carga horaria del docente
│   ├── cargar_notas.php           --> Módulo de evaluación y notas definitivas
│   ├── mis_secciones.php          --> Listado de alumnos inscritos por sección
│   └── soporte.php                --> Centro de soporte para Docentes
│
├── estudiante/                    --> Panel de Estudiantes
│   ├── index.php                  --> Expediente estudiantil y estado académico
│   ├── mi_horario.php             --> Consulta de horario asignado y aula
│   ├── mis_notas.php              --> Historial de calificaciones por trayecto
│   └── soporte.php                --> Centro de soporte para Estudiantes
│
├── super_user/                    --> Panel de Super Usuario (Seguridad y Auditoría)
│   ├── index.php                  --> Panel de control general y auditoría
│   ├── seguridad.php              --> Control de cierres de sistema y bloqueos
│   ├── respaldos.php              --> Generador de respaldos SQL
│   └── soporte.php                --> Centro de soporte Super Usuario
│
├── funciones/                     --> Motor del Sistema y Conexiones
│   ├── conexion.php               --> Configuración de conexión MySQL (utf8mb4)
│   ├── functions.php              --> Biblioteca principal de funciones institucionales
│   ├── variables.php              --> Variables globales de entorno y logotipos
│   ├── cabecera_footer.php        --> Renderizado unificado de componentes visuales
│   └── registrar.php              --> Auditoría y registro de logs
│
├── images/                        --> Recursos Visuales Institucionales
│   ├── LOGO.png                   --> Logo principal de cabecera recortado
│   └── educacion_universitaria.jpg --> Logo institucional de footer
│
├── profile_selector.php           --> Selector dinámico de perfil de usuario
└── DOCUMENTACION_SISTEMA.md       --> Este manual técnico de arquitectura
```

---

## 🗃️ 4. Esquema Principal de Base de Datos (`proyecto_tsu`)

| Tabla | Descripción y Propósito |
|-------|-------------------------|
| `users` | Usuarios del sistema (Estudiantes, Docentes, Directores, Admin, SuperUser) con expediente completo. |
| `carreras` | Catálogo de Programas Nacionales de Formación (PNF) impartidos en la UPTPC. |
| `materias` | Unidades curriculares asignadas por mallas académicas y trayectos. |
| `secciones` | Secciones académicas habilitadas con límite de cupos y turnos (Puerto Cabello / COEF). |
| `estudiante_seccion` | Relación de estudiantes matriculados en secciones por período académico. |
| `notas_definitivas` | Calificaciones registradas y validadas por los docentes. |
| `historial_cambios_notas` | Auditoría de cualquier modificación aplicada sobre calificaciones de estudiantes. |
| `ciudades`, `estados`, `municipios`, `parroquias` | Catálogos territoriales oficializados de Venezuela. |
| `visitas` & `auditoria` | Registros de trazabilidad, IPs, navegadores e interacciones dentro de la plataforma. |

---

## 🔐 5. Seguridad, Permisos y Buenas Prácticas

1. **Autenticación y Sesión (`functions.php`)**:
   - `isLoggedIn()` valida que el usuario posea una sesión activa antes de renderizar vistas protegidas.
   - `visita()` registra automáticamente el acceso y navegador del usuario para prevenir abusos.
2. **Sanitización y Codificación `utf8mb4`**:
   - Conexiones protegidas vía `mysqli_set_charset($db, "utf8mb4")` en `funciones/conexion.php`, evitando vulnerabilidades de inyección SQL y corrigiendo la corrupción de caracteres (mojibake).
3. **Página de Soporte y Footers Unificados**:
   - Enlaces directos a `soporte.php` con contactos directos del Desarrollador (**Hector Marulanda**) y Control de Estudios (**Blanca Crespo**).
   - Enlace oficial al portal de la universidad: `https://www.uptpc.edu.ve/`.

---

## 🛠️ 6. Mantenimiento y Optimizaciones Aplicadas

- **Limpieza de Código Muerto**: Se removieron archivos temporales (`.php~`, scripts de prueba aislados) y funciones en desuso.
- **Validación PHP 8.x**: 100% de los archivos PHP verificados con la prueba de sintaxis `php -l` (**0 errores de sintaxis**).
