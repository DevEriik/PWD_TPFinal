# 📱 Venta de Accesorios de Celulares - Proyecto Final de Programación Web Dinámica

Este es el proyecto final para la materia **Programación Web Dinámica**. Consiste en un sitio de e-commerce (tienda online) enfocado en la venta de accesorios para celulares, donde los usuarios pueden registrarse, ver productos, agregarlos al carrito y simular un proceso de compra.

---

## 📚 Contexto Académico

Este proyecto fue desarrollado como trabajo práctico final.

* **Universidad:** Universidad Nacional del Comahue (UNCo)
* **Facultad:** Facultad de Informatica
* **Carrera:** Tecnicatura Universitaria en Desarrollo Web. 
* **Materia:** Programación Web Dinámica
* **Año:** 2025

---

## 🧑‍💻 Integrantes del Grupo

El equipo de desarrollo está conformado por 4 estudiantes:

| Nombre y Apellido | Legajo | Email |
| :--- | :--- | :--- |
| **Erick Gonzalez** | FAI-3433 | `erick.gonzalez@est.fi.uncoma.edu.ar` |
| **Irina Sol Bruschi Z.** | FAI-4446 | `irina.bruschi@est.fi.uncoma.edu.ar` |
| **Jorge Victor Manuel Gonzalez**| FAI-4460 | `jorge.gonzalez@est.fi.uncoma.edu.ar` |
| **Daniela Oñatibia** | FAI-4775 | `daniela.onatibia@est.fi.uncoma.edu.ar` |

---

## ✨ Funcionalidades Principales

* 👤 **Gestión de Usuarios:** Sistema de Registro (Sign-up) e Inicio de Sesión (Login).
* 🛍️ **Catálogo de Productos:** Vista de todos los productos disponibles con sus detalles (precio, stock, descripción).
* 🛒 **Carrito de Compras:** Funcionalidad para añadir, modificar y eliminar productos del carrito.
* 💳 **Proceso de Compra:** Simulación de un *checkout* para finalizar la compra.
* 🔐 **Panel de Administrador (Opcional):** "Panel para ABM (Alta, Baja, Modificación) de productos, gestión de stock y visualización de usuarios registrados.

---

## 🛠️ Tecnologías Utilizadas

Para el desarrollo del proyecto se utilizaron las siguientes tecnologías:

* **Frontend (Cliente):**
    * HTML5
    * CSS3 (Bootstrap)
    * JavaScript

* **Backend (Servidor):**
    * PHP

* **Base de Datos:**
    * MySQL

* **Entorno de Desarrollo:**
    * Servidor web Apache
    * Visual Studio Code

---

## 🚀 Instalación y Puesta en Marcha

Para correr este proyecto en un entorno local, sigue estos pasos:

1.  Clonar el repositorio:
    ```bash
    git clone [https://github.com/DevEriik/PWD_TPFinal.git](https://github.com/DevEriik/PWD_TPFinal.git)
    ```
2.  Mover la carpeta del proyecto (`PWD_TPFinal`) al directorio `htdocs` de tu servidor local (XAMPP, WAMP, LAMPP).
3.  **Importar la Base de Datos:**
    * Abrir `phpMyAdmin` (o el gestor de BBDD que utilices).
    * Crear una nueva base de datos (ej: `pwd_tpfinal`).
    * Importar el archivo `.sql` que se encuentra en la carpeta `[ruta/a/tu/archivo.sql]` de este proyecto. *(¡Asegúrate de incluir este archivo en tu repo!)*
4.  **Configurar la conexión:**
    * Revisa el archivo de configuración de la base de datos (ej: `config/db.php` o `conexion.php`).
    * Asegúrate de que el nombre de la base de datos, el usuario (ej: `root`) y la contraseña coincidan con tu configuración local.
5.  Iniciar los servicios de **Apache** y **MySQL** desde el panel de control de XAMPP.
6.  Abrir tu navegador y acceder a:
    ```
    http://localhost/PWD_TPFinal/
    ```

---
