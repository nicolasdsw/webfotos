SET SESSION FOREIGN_KEY_CHECKS=0;

--
-- Database: `webfotos`
--
CREATE DATABASE IF NOT EXISTS `webfotos` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `webfotos`;


/* Drop Tables */

DROP TABLE IF EXISTS uploads;
DROP TABLE IF EXISTS albums;
DROP TABLE IF EXISTS users;

/* Create Tables */

CREATE TABLE albums
(
	id_album bigint NOT NULL AUTO_INCREMENT,
	name varchar(50) NOT NULL,
	description text,
	image longblob,
	image_type varchar(20),
	id_user bigint NOT NULL,
	PRIMARY KEY (id_album)
);


CREATE TABLE uploads
(
	id_upload bigint NOT NULL AUTO_INCREMENT,
	file longblob NOT NULL,
	file_type varchar(20) NOT NULL,
	subtitle varchar(255),
	id_album bigint NOT NULL,
	PRIMARY KEY (id_upload)
);


CREATE TABLE users
(
	id_user bigint NOT NULL AUTO_INCREMENT,
	login varchar(50) NOT NULL,
	password varchar(255) NOT NULL,
	email varchar(150),
	superuser boolean,
	PRIMARY KEY (id_user),
	UNIQUE (login),
	UNIQUE (email)
);



/* Create Foreign Keys */

ALTER TABLE uploads
	ADD FOREIGN KEY (id_album)
	REFERENCES albums (id_album)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE albums
	ADD FOREIGN KEY (id_user)
	REFERENCES users (id_user)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;

INSERT INTO `users` (`login`, `password`, `email`, `superuser`) VALUES
('admin', '$2y$11$zy3ag27xopRmvNMD.Imj6Oc5X5HeorcsNzArAFFm4/064fwKdW6aG', 'admin@webfotos.com.br', true);


