-- ==========================================
-- ESTRUCTURA DE BASE DE DATOS: MIS TRAPITOS
-- Basado en ERS v2.2 y Diagrama de Diseño 4.1
-- ==========================================

-- 1. Tabla de PROVEEDORES (RF-09)
-- Almacena información de contacto y productos suministrados[cite: 31, 99, 561].
CREATE TABLE PROVEEDORES (
    id_proveedor SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100)
);

-- 2. Tabla de CLIENTE (RF-07)
-- Guarda datos personales y permite consultar el historial de compras[cite: 30, 97, 565].
CREATE TABLE CLIENTE (
    id_cliente SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20)
);

-- 3. Tabla de PRODUCTOS (RF-01, RF-05)
-- Registra descripción, precio, categoría, talla, color y cantidad[cite: 29, 95, 571].
CREATE TABLE PRODUCTOS (
    id_producto SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio NUMERIC(10, 2) NOT NULL, -- Precisión para transacciones financieras
    stock INTEGER DEFAULT 0,
    categoria VARCHAR(50), -- Para cumplir con RF-04 y RF-14 [cite: 86, 104]
    talla VARCHAR(10),
    color VARCHAR(20),
    id_proveedor INTEGER,
    CONSTRAINT fk_proveedor FOREIGN KEY (id_proveedor) 
        REFERENCES PROVEEDORES(id_proveedor) ON DELETE SET NULL
);

-- 4. Tabla de VENTAS (RF-10, RF-11)
-- Registra transacciones, fecha y método de pago[cite: 27, 101, 585].
CREATE TABLE VENTAS (
    id_venta SERIAL PRIMARY KEY,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Esencial para reportes diarios/mensuales [cite: 61, 105]
    metodo_pago VARCHAR(50), -- Efectivo, tarjeta o transferencia [cite: 101]
    id_cliente INTEGER,
    CONSTRAINT fk_cliente FOREIGN KEY (id_cliente) 
        REFERENCES CLIENTE(id_cliente) ON DELETE SET NULL
);

-- 5. Detalles de Venta (Intersección entre Ventas y Productos)
-- Permite el registro de múltiples productos por venta y actualización de inventario[cite: 100, 102, 577].
CREATE TABLE DETALLES_VENTA (
    id_detalle SERIAL PRIMARY KEY,
    id_venta INTEGER NOT NULL,
    id_producto INTEGER NOT NULL,
    cantidad INTEGER NOT NULL CHECK (cantidad > 0),
    CONSTRAINT fk_venta FOREIGN KEY (id_venta) REFERENCES VENTAS(id_venta) ON DELETE CASCADE,
    CONSTRAINT fk_producto FOREIGN KEY (id_producto) REFERENCES PRODUCTOS(id_producto)
);

-- 6. Tabla de DESCUENTOS (RF-06)
-- Aplicación de ofertas con porcentaje y duración determinada[cite: 32, 96, 590].
CREATE TABLE DESCUENTOS (
    id_descuento SERIAL PRIMARY KEY,
    porcentaje NUMERIC(5, 2) NOT NULL,
    id_producto INTEGER UNIQUE, -- Relación con productos específicos [cite: 592]
    CONSTRAINT fk_descuento_producto FOREIGN KEY (id_producto) REFERENCES PRODUCTOS(id_producto) ON DELETE CASCADE
);

-- 7. Tabla de USUARIOS (RF-17)
-- Control de acceso mediante usuarios y contraseñas para personal y administrador[cite: 66, 107, 150].
CREATE TABLE USUARIOS (
    id_usuario SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL, -- Seguridad: nunca guardar en texto plano [cite: 150]
    rol VARCHAR(20) CHECK (rol IN ('administrador', 'empleado')) -- Gestión de privilegios [cite: 66, 169]
);