-- Creación de la base de datos
CREATE DATABASE ConsultoraGestiónClientes;
USE ConsultoraGestiónClientes;

-- Tabla de Usuarios (para inicio de sesión)
CREATE TABLE Usuarios (
    usuario_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    rol ENUM('Administrador', 'Consultor', 'Gestor') NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_login DATETIME,
    activo BOOLEAN DEFAULT TRUE,
    reset_token VARCHAR(255),
    token_expira DATETIME
);

-- Tabla de Clientes (con muchas características)
CREATE TABLE Clientes (
    cliente_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_empresa VARCHAR(100) NOT NULL,
    ruc VARCHAR(20) NOT NULL UNIQUE,
    direccion VARCHAR(200),
    ciudad VARCHAR(100),
    pais VARCHAR(100) DEFAULT 'Perú',
    telefono_principal VARCHAR(20),
    telefono_secundario VARCHAR(20),
    email_principal VARCHAR(100),
    email_secundario VARCHAR(100),
    sector_industrial VARCHAR(100),
    tamaño_empresa ENUM('Pequeña', 'Mediana', 'Grande', 'Corporación'),
    fecha_registro DATE NOT NULL,
    fecha_ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    tipo_cliente ENUM('Potencial', 'Activo', 'Inactivo', 'VIP') DEFAULT 'Potencial',
    ingresos_anuales DECIMAL(15,2),
    sitio_web VARCHAR(255),
    redes_sociales JSON,
    persona_contacto_principal VARCHAR(150),
    cargo_contacto_principal VARCHAR(100),
    notas TEXT,
    calificacion INT CHECK (calificacion BETWEEN 1 AND 5),
    creado_por INT,
    FOREIGN KEY (creado_por) REFERENCES Usuarios(usuario_id)
);

-- Tabla de Proyectos (para asignar a clientes)
CREATE TABLE Proyectos (
    proyecto_id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    nombre_proyecto VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_fin_estimada DATE,
    fecha_fin_real DATE,
    presupuesto DECIMAL(12,2),
    costo_actual DECIMAL(12,2) DEFAULT 0,
    estado ENUM('Propuesta', 'Planificación', 'En progreso', 'En revisión', 'Completado', 'Cancelado') DEFAULT 'Propuesta',
    prioridad ENUM('Baja', 'Media', 'Alta', 'Crítica') DEFAULT 'Media',
    porcentaje_completado INT DEFAULT 0 CHECK (porcentaje_completado BETWEEN 0 AND 100),
    responsable_id INT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    notas TEXT,
    documentos_adjuntos JSON,
    FOREIGN KEY (cliente_id) REFERENCES Clientes(cliente_id) ON DELETE CASCADE,
    FOREIGN KEY (responsable_id) REFERENCES Usuarios(usuario_id)
);

-- Tabla intermedia para múltiples contactos por cliente (opcional)
CREATE TABLE ContactosClientes (
    contacto_id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cargo VARCHAR(100),
    departamento VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100) NOT NULL,
    es_principal BOOLEAN DEFAULT FALSE,
    notas TEXT,
    FOREIGN KEY (cliente_id) REFERENCES Clientes(cliente_id) ON DELETE CASCADE
);