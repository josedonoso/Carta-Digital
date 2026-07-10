# 🍽️ Carta Digital - Restaurante & Cafetería

Sistema desarrollado en **PHP + MySQL** para la administración y visualización de una **Carta Digital** accesible mediante código QR.

El objetivo del proyecto es reemplazar la carta física por una versión digital moderna, responsive y fácil de administrar.

---

## 📱 Características

### Carta Digital

- ☕ Separación entre Cafetería y Restaurante
- 📂 Navegación por categorías
- 📷 Fotografías de productos
- ⭐ Productos destacados
- 🚫 Productos agotados
- 🍦 Helados artesanales con precios por tamaño
- 📋 Sabores del día administrables
- 🌙 Diseño oscuro (Dark Mode)
- 📱 Diseño responsive para teléfonos móviles

---

## 🔐 Panel Administrativo

El administrador puede:

- Gestionar categorías
- Agregar productos
- Editar productos
- Eliminar productos
- Subir imágenes
- Marcar productos destacados
- Marcar productos agotados
- Configurar precios
- Administrar sabores de helados
- Activar o desactivar productos

---

## 🍦 Gestión de Helados Artesanales

El sistema incorpora un módulo especial para helados.

Permite:

- Precio Simple
- Precio Doble
- Administración de sabores disponibles
- Mostrar únicamente sabores activos en la carta

Ejemplo:

```
Helado Artesanal

Sabores del día

• Chocolate
• Frutilla
• Pistacho
• Vainilla

Simple $2.500

Doble $4.000
```

---

## 🛠 Tecnologías utilizadas

### Backend

- PHP 8
- MySQL
- PDO

### Frontend

- HTML5
- CSS3
- Bootstrap 5
- JavaScript

### Servidor

- XAMPP

### Control de versiones

- Git
- GitHub

---

## 📂 Estructura del proyecto

```
carta_digital/

├── admin/
│   ├── dashboard.php
│   ├── categorias.php
│   ├── productos.php
│   ├── editar_productos.php
│   └── login.php
│
├── assets/
│   ├── css/
│   ├── img/
│   └── js/
│
├── database/
│   └── bd.sql
│
├── includes/
│   ├── auth.php
│   └── conexion.php
│
├── uploads/
│   └── productos/
│
├── carta.php
└── index.php
```

---

## 🗄 Base de Datos

El sistema utiliza las siguientes tablas:

- usuarios
- categorias
- productos
- producto_precios
- sabores_helado

---

## 🚀 Instalación

1. Clonar el repositorio

```bash
git clone https://github.com/josedonoso/Carta-Digital.git
```

2. Copiar el proyecto a:

```
xampp/htdocs/
```

3. Crear una base de datos llamada:

```
carta_digital
```

4. Importar:

```
database/bd.sql
```

5. Configurar la conexión en:

```
includes/conexion.php
```

6. Abrir:

```
http://localhost/carta_digital
```

---

## 📷 Capturas

Próximamente se incorporarán capturas del sistema.

- Login
- Dashboard
- Administración
- Carta Digital
- Vista móvil

---

## 📌 Estado del proyecto

🚧 En desarrollo

Actualmente se encuentra en una versión Beta con las funcionalidades principales implementadas.

---

## 💡 Próximas mejoras

- Panel administrativo completamente responsive
- Estadísticas del restaurante
- Configuración del logo desde el panel
- Publicación en hosting
- Generación de código QR
- Carta bilingüe
- Optimización de imágenes

---

## 👨‍💻 Autor

**José Donoso Catalán**

Proyecto desarrollado como práctica y portafolio personal utilizando PHP, MySQL y Bootstrap.
