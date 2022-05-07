DROP DATABASE IF EXISTS tiempos_comida;

DROP USER IF EXISTS 'comidaAdmin'@'%';
DROP USER IF EXISTS 'comidaAdmin'@'localhost';
CREATE USER 'comidaAdmin'@'%' IDENTIFIED BY 'root123';
CREATE USER 'comidaAdmin'@'localhost' IDENTIFIED BY 'root123';
GRANT ALL PRIVILEGES ON proyecto1.* TO 'comidaAdmin'@'%';
GRANT ALL PRIVILEGES ON proyecto1.* TO 'comidaAdmin'@'localhost';

CREATE TABLE usuarios(
    username varchar(10) NOT NULL,
    password varchar(50) NOT NULL,
    PRIMARY KEY (username)
);

CREATE TABLE secciones(
    id VARCHAR(10) PRIMARY KEY NOT NULL,
    descripciON varchar(100) NOT NULL 
);

CREATE TABLE alumnos(
    cedula VARCHAR(12) PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    id_seccion VARCHAR(10) NOT NULL,
    FOREIGN KEY (id_seccion) REFERENCES secciones(id) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX(id_seccion)
);

CREATE TABLE tiempos(
    id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(20) NOT NULL,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE comidas(
    id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
    ced_alumno VARCHAR(12) NOT NULL,
    fecha DATE NOT NULL,
    id_tiempo bigint NOT NULL,
    FOREIGN KEY (ced_alumno) REFERENCES alumnos(cedula) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_tiempo) REFERENCES tiempos(id) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX(ced_alumno,fecha),
    INDEX(id_tiempo)
);

CREATE TABLE menus(
    id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
    fecha DATE NOT NULL,
    id_tiempo bigint NOT NULL,
    encargado varchar(10) NOT NULL,
    FOREIGN KEY (id_tiempo) REFERENCES tiempos(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (encargado) REFERENCES usuarios(username) ON UPDATE CASCADE ON DELETE CASCADE
);