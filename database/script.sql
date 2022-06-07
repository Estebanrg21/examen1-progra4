DROP DATABASE IF EXISTS scot;
CREATE DATABASE scot;
USE scot;
DROP USER IF EXISTS 'comidaAdmin'@'%';
DROP USER IF EXISTS 'comidaAdmin'@'localhost';
CREATE USER 'comidaAdmin'@'%' IDENTIFIED BY 'root123';
CREATE USER 'comidaAdmin'@'localhost' IDENTIFIED BY 'root123';
GRANT ALL PRIVILEGES ON scot.* TO 'comidaAdmin'@'%';
GRANT ALL PRIVILEGES ON scot.* TO 'comidaAdmin'@'localhost';

CREATE TABLE users(
    email varchar(100) NOT NULL,
    password varchar(50) NOT NULL,
    name varchar(100) NOT NULL,
    is_su TINYINT NOT NULL,
    is_admin TINYINT NOT NULL,
    PRIMARY KEY (email)
);

CREATE TABLE sections(
    id VARCHAR(10) PRIMARY KEY NOT NULL,
    description varchar(100) NOT NULL 
);

CREATE TABLE students(
    id VARCHAR(12) PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    lastnames VARCHAR(100) NOT NULL,
    id_section VARCHAR(10) NOT NULL,
    FOREIGN KEY (id_section) REFERENCES sections(id) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX(id_section)
);

CREATE TABLE food_times(
    id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    description VARCHAR(100) NOT NULL
);

CREATE TABLE foods(
    id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_student VARCHAR(12) NOT NULL,
    food_date DATE NOT NULL,
    id_food_time bigint NOT NULL,
    FOREIGN KEY (id_student) REFERENCES students(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_food_time) REFERENCES food_times(id) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX(id_student,food_date,id_food_time)
);

CREATE TABLE menus(
    id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(250) NOT NULL
);

CREATE TABLE menus_details(
    id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
    day_served DATE NOT NULL,
    id_food_time bigint NOT NULL,
    creator varchar(100) NOT NULL,
    id_menu bigint NOT NULL,
    description varchar(100) NOT NULL, 
    FOREIGN KEY (id_food_time) REFERENCES food_times(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_menu) REFERENCES menus(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (creator) REFERENCES users(email) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX(day_served)
);

insert into users values ("scot@estebanramirez.xyz",md5("muvpeq-5dikcU-beqgoz"),"Super usuario",1,0);