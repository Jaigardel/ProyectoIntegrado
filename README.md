# 📸 Rally Fotográfico

**Rally Fotográfico** es un proyecto de fin de grado centrado en el desarrollo de una plataforma web para la gestión de concursos de fotografía. El sistema permite la participación de usuarios mediante la subida de fotos, la votación pública y la visualización de resultados a través de galerías interactivas.

---

## 🔍 Descripción General

Esta aplicación web está diseñada para:

- Gestionar concursos fotográficos limitados en el tiempo (rallies)
- Permitir a los usuarios subir imágenes
- Facilitar la votación pública
- Mostrar galerías activas, históricas y ganadoras
- Administrar todo el ciclo de vida de los rallies desde un panel de administración

---

## 🗂️ Estructura del Proyecto

El sistema está dividido en varias interfaces y módulos:

### 👥 Roles de Usuario

| Rol             | Descripción                             |
|------------------|-----------------------------------------|
| Invitado         | Puede ver galerías y fotos ganadoras    |
| Usuario registrado | Puede subir fotos, votar y gestionar sus imágenes |
| Administrador    | Acceso completo a gestión de usuarios, fotos y rallies |

### 🖼️ Interfaces

- **Público general**: Acceso a galerías y votación
- **Usuario registrado**: Subida de fotos y gestión personal
- **Administrador**: Gestión completa de concursos y usuarios

---

## 🧱 Arquitectura y Componentes

- **Frontend**: HTML, CSS (Bootstrap 5), JS
- **Backend**: PHP
- **Base de datos**: MySQL con PDO
- **Almacenamiento de imágenes**: [Supabase](https://supabase.io/)
- **Gestión de sesiones**: PHP nativo

---

## 🧩 Páginas clave

| Página                | Función                                | Acceso         |
|------------------------|-----------------------------------------|----------------|
| `index.php`           | Página de inicio                        | Todos          |
| `galeriaActiva.php`   | Fotos del rally activo + votación       | Todos          |
| `todasGalerias.php`   | Histórico de rallies                    | Todos          |
| `fotosGanadoras.php`  | Galería de fotos ganadoras              | Todos          |
| `usuario.php`         | Panel personal del usuario              | Usuario        |
| `admin.php`           | Panel de administración                 | Administrador  |

---

## 🧮 Base de Datos

El sistema utiliza varias tablas para organizar la información:

- `usuarios`, `roles`, `fotos`, `rallys`, `votos`, `categorias`
- Relaciones entre usuarios, sus fotos y los rallies en los que participan
- Estados de rallies: 0 (inactivo), 1 (activo), 2 (finalizado)

---

## ☁️ Flujo de Subida y Almacenamiento de Fotos

1. El usuario sube una imagen a través de `subirFotos.php`
2. Se guarda en Supabase y se registra la metadata en la base de datos MySQL
3. El sistema muestra la confirmación y permite la gestión de las imágenes

---

## 🔐 Control de Acceso

El control de acceso se basa en el rol del usuario:

- Visitantes no autenticados ven solo contenido público
- Usuarios registrados pueden interactuar con el contenido
- Administradores gestionan toda la plataforma

---

## 📂 Archivos Relevantes

- `index.php`: Página principal
- `admin.php`: Panel de control
- `fotosGanadoras.php`: Visualización de fotos ganadoras
- `usuario.php`: Gestión de imágenes del usuario
- `subirFotos.php`: Carga de nuevas imágenes
- `scripts/supabase.js`: Comunicación con el servicio de almacenamiento

---

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/Jaigardel/ProyectoIntegrado)


