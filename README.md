# Tienda Doña Elena 

Sistema de Punto de Venta con arquitectura MVC 
---

## 📁 Estructura del Proyecto

```
Tienda_Elena/
├── config/
│   └── database.php          ← Credenciales PostgreSQL
│
├── controllers/
│   ├── AuthController.php    ← Login / Logout
│   ├── DashboardController.php
│   ├── InventarioController.php
│   ├── ProductoController.php
│   ├── UsuarioController.php
│   └── VentaController.php   ← POS + transacciones
│
├── core/
│   ├── Autoload.php          ← Clase registradora del SPL autoload
│   ├── Controller.php        ← Base: view / redirect / json / auth
│   ├── Database.php          ← Singleton PDO PostgreSQL
│   ├── Flash.php             ← Mensajes flash de sesión
│   ├── Helpers.php           ← Utilidades globales
│   ├── Middleware.php        ← auth() / role() / guest()
│   ├── Model.php             ← Base: fetch/fetchAll/execute/CRUD/transacciones
│   ├── Response.php          ← json / redirect / abort
│   ├── Router.php            ← Resolución automática /controlador/accion/param
│   ├── Session.php           ← Abstracción $_SESSION
│   └── Validator.php         ← Validación encadenable
│
├── functions/
│   └── validacion.php        ← Funciones validar*() al estilo h3_act3
│
├── models/
│   ├── Categoria.php
│   ├── Inventario.php
│   ├── Producto.php          ← findBySKU / search / lowStock / expirados
│   ├── Usuario.php           ← createUser / updateUser con password_hash
│   └── Venta.php             ← createVenta() con transacción PostgreSQL
│
├── public/
│   ├── css/style.css
│   ├── js/main.js
│   ├── .htaccess             ← mod_rewrite → index.php
│   └── index.php             ← Front controller + spl_autoload_register
│
├── views/
│   ├── auth/login.php
│   ├── dashboard/index.php
│   ├── inventario/index.php + movimiento.php
│   ├── layout/
│   │   ├── header.php / header_auth.php
│   │   ├── navbar.php        ← Flash::render() aquí
│   │   ├── sidebar.php
│   │   └── footer.php / footer_auth.php
│   ├── productos/index.php + crear.php + editar.php
│   ├── usuarios/index.php + editar.php
│   └── ventas/pos.php        ← POS con JS puro
│
└── respaldo_tienda.sql       ← Dump original de la BD
```

---

## ⚙️ Instalación

### 1. Configurar base de datos

```bash
psql -U postgres -c "CREATE DATABASE tienda;"
psql -U postgres -d tienda -f respaldo_tienda.sql
```

### 2. Configurar credenciales

Editar `config/database.php`:

```php
return [
    'host'     => 'localhost',
    'port'     => '5432',
    'dbname'   => 'tienda',
    'user'     => 'postgres',
    'password' => 'TU_PASSWORD',
];
```

### 3. Configurar Apache

Virtual host apuntando al directorio `public/`:

```apache
<VirtualHost *:80>
    DocumentRoot /ruta/al/proyecto/Tienda_Elena/public
    <Directory /ruta/al/proyecto/Tienda_Elena/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Habilitar `mod_rewrite`:
```bash
a2enmod rewrite
systemctl restart apache2
```

### 4. Permisos

```bash
chmod 755 public/
```

---

## 🗺️ Enrutamiento Automático

El `Router.php` resuelve URLs automáticamente sin rutas manuales:

| URL                       | Resuelve a                          |
|---------------------------|-------------------------------------|
| `/`                       | `AuthController->loginForm()`       |
| `/login` (POST)           | `AuthController->login()`           |
| `/logout`                 | `AuthController->logout()`          |
| `/dashboard`              | `DashboardController->index()`      |
| `/productos`              | `ProductoController->index()`       |
| `/productos/crear`        | `ProductoController->crear()`       |
| `/productos/guardar`      | `ProductoController->guardar()`     |
| `/productos/editar/5`     | `ProductoController->editar(5)`     |
| `/productos/actualizar/5` | `ProductoController->actualizar(5)` |
| `/productos/eliminar/5`   | `ProductoController->eliminar(5)`   |
| `/productos/buscar?q=...` | `ProductoController->buscar()`      |
| `/inventario`             | `InventarioController->index()`     |
| `/inventario/movimiento`  | `InventarioController->movimiento()`|
| `/inventario/registrar`   | `InventarioController->registrar()` |
| `/ventas/pos`             | `VentaController->pos()`            |
| `/ventas/guardarVenta`    | `VentaController->guardarVenta()`   |
| `/usuarios`               | `UsuarioController->index()`        |
| `/usuarios/guardar`       | `UsuarioController->guardar()`      |
| `/usuarios/editar/3`      | `UsuarioController->editar(3)`      |
| `/usuarios/actualizar/3`  | `UsuarioController->actualizar(3)`  |
| `/usuarios/eliminar/3`    | `UsuarioController->eliminar(3)`    |

---

## 🔐 Seguridad Implementada

- `password_hash()` / `password_verify()` en autenticación
- Prepared statements en **todos** los modelos (sin SQL injection)
- `htmlspecialchars()` en toda salida a vistas (`Helpers::e()`)
- Middleware de roles en cada controlador
- Token CSRF básico en el formulario de login
- Stock validado **antes** de procesar la venta (servidor + SQL)

---

## 💳 Flujo del POS

```
Usuario agrega productos al carrito (JS)
    ↓
Selecciona cliente, empleado, método de pago
    ↓
POST /ventas/guardarVenta (JSON)
    ↓
VentaController valida stock de cada producto
    ↓
Venta::createVenta() abre BEGIN
    ↓
INSERT venta → INSERT detalle_venta × N → UPDATE producto stock × N → INSERT pago
    ↓
COMMIT  (o ROLLBACK si cualquier paso falla)
    ↓
Respuesta JSON { success: true, id_venta: N }
```

---

## 🔄 Cambios Respecto al Proyecto Original

| Aspecto             | Antes (Tienda_Elena)          | Ahora                              |
|---------------------|-------------------------------|------------------------------------|
| Autoload            | require_once manual           | spl_autoload_register dinámico     |
| Enrutamiento        | Array manual de rutas         | Resolución automática por URL      |
| Validaciones        | Dispersas en controladores    | `Validator` + `functions/validacion.php` |
| PDO                 | Instancias múltiples          | Singleton `Database::getConnection()` |
| Mensajes flash      | `$_SESSION['mensaje']` directo | `Flash::success/error/render()`   |
| Transacciones venta | Sin transacciones             | BEGIN/COMMIT/ROLLBACK explícitos   |
| Passwords           | Sin hash consistente          | `password_hash()` siempre         |
| Stock en venta      | Sin validación                | Validación previa + UPDATE condicional |
| Layout              | Duplicado en cada vista       | `header/navbar/sidebar/footer` reutilizables |
