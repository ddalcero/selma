

-- 15.3.24

-- NEW FOR v.14.12
-- two new tables: auxcli and solicitud

-- auxcli
CREATE TABLE `auxcli` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aux` varchar(10) NOT NULL,
  `clt_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Solicitud
CREATE TABLE `solicitud` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `estado` int(10) unsigned NOT NULL default 0, -- 0: Pendiente; 1: Facturada
  `user_id` int(10) NOT NULL,
  `fecha_sol` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `fecha_fac` timestamp NOT NULL,
  `lot_id` int(10) NOT NULL,
  `tipo_dte` int(10),
  `nr_dte` int(10),
  `clt_id` int(10) unsigned NOT NULL,
  `spj_id` int(10) unsigned NOT NULL,
  `cliente` varchar(50),
  `glosa` varchar(50),
  `detalle` varchar(255),
  `importe_clp` decimal(15,2) NOT NULL,
  `tasa_iva` decimal(4,2) NOT NULL default 0,
  `iva` decimal(15,2) NOT NULL default 0,
  `total` decimal(15,2) NOT NULL,
  `valor_uf` decimal(8,2) unsigned,
  `total_uf` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
-- ================================================================================

-- OLD SCRIPT

CREATE TABLE `llamadas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `fono` varchar(18) COLLATE utf8_unicode_ci NOT NULL DEFAULT '-',
  `tipo` int(10) unsigned NOT NULL,
  `opcion` int(10) unsigned NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci,
  `anexo` varchar(18) COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `valores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pmonth` int(10) unsigned NOT NULL,
  `pyear` int(10) unsigned NOT NULL,
  `uf` decimal(8,2) unsigned NOT NULL,
  `pdays` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `ufdia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pday` date NOT NULL,
  `uf` decimal(8,2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `persub` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `persub` varchar(15) NOT NULL,
  `uf` decimal(8,2) unsigned,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `chkproyecto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `spj_id` int(10) unsigned NOT NULL,
  `pmonth` int(10) unsigned NOT NULL,
  `pyear` int(10) unsigned NOT NULL,
  `chk` tinyint(1) unsigned NOT NULL default 0, 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE  TABLE IF NOT EXISTS `stickers` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT ,
  `spj_id` INT(10) unsigned NOT NULL ,
  `user_id` INT(10) unsigned NOT NULL ,
  `text` VARCHAR(2000) NULL ,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`) 
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE  TABLE IF NOT EXISTS `reuniones` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `per_id` INT(10) NOT NULL ,
  `user_id` INT(10) NOT NULL ,
  `subject` VARCHAR(200) NULL ,
  `text` TEXT NULL ,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`) 
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE  TABLE IF NOT EXISTS `candidato` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(50) NOT NULL ,
  `apellidos` VARCHAR(100) NOT NULL ,
  `rut` VARCHAR(15) NULL ,
  `fecha` timestamp NULL ,
  `liquido` int(10) NULL ,
  `salario_json` TEXT NULL ,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`) 
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--- tipo proyecto
CREATE  TABLE IF NOT EXISTS `tipoproyecto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `spj_id` int(10) unsigned NOT NULL,
  `tipoproyecto` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- insert into llamadas (user_id,fono,tipo,opcion,descripcion,anexo) value (0,'94337921',2,2,'descipción','101')


-- SCHEMA PARA TRABAJAR CON TABLAS HIERARQUICAS
use laravel;
CREATE  TABLE IF NOT EXISTS `orgchart` (
  `id` INT(10) unsigned NOT NULL  ,
  `parent` INT(10) unsigned default null ,
  PRIMARY KEY (`id`) 
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

delimiter //
CREATE FUNCTION hierarchy_connect_by_parent_eq_prior_id_with_level(value INT, maxlevel INT) RETURNS INT
NOT DETERMINISTIC
READS SQL DATA
BEGIN
        DECLARE _id INT;
        DECLARE _parent INT;
        DECLARE _next INT;
        DECLARE _i INT;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET @id = NULL;

        SET _parent = @id;
        SET _id = -1;
        SET _i = 0;

        IF @id IS NULL THEN
                RETURN NULL;
        END IF;

        LOOP
                SELECT  MIN(id)
                INTO    @id
                FROM    orgchart
                WHERE   parent = _parent
                        AND id > _id
                        AND COALESCE(@level < maxlevel, TRUE);
                IF @id IS NOT NULL OR _parent = @start_with THEN
                        SET @level = @level + 1;
                        RETURN @id;
                END IF;
                SET @level := @level - 1;
                SELECT  id, parent
                INTO    _id, _parent
                FROM    orgchart
                WHERE   id = _parent;
                SET _i = _i + 1;
        END LOOP;
        RETURN NULL;
END
//

delimiter //
CREATE FUNCTION hierarchy_sys_connect_by_path(delimiter TEXT, node INT) RETURNS TEXT
NOT DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE _path TEXT;
  DECLARE _cpath TEXT;
  DECLARE _id INT;
  DECLARE EXIT HANDLER FOR NOT FOUND RETURN _path;
  SET _id = COALESCE(node, @id);
  SET _path = _id;
  LOOP
    SELECT  parent
      INTO    _id
    FROM    orgchart
    WHERE   id = _id
      AND COALESCE(id <> @start_with, TRUE);
    SET _path = CONCAT(_id, delimiter, _path);
  END LOOP;
  END
//

-- EJEMPLO DE QUERY (TODOS LOS HIJOS DESDE UN PADRE, PADRE DEFINIDO EN START_WITH)
SELECT  CONCAT(REPEAT('    ', level - 1), hi.id) AS treeitem,
        hierarchy_sys_connect_by_path('/', hi.id) AS path,
        parent, level
FROM    (
        SELECT  hierarchy_connect_by_parent_eq_prior_id_with_level(id, 5) AS id,
                CAST(@level AS SIGNED) AS level
        FROM    (
                SELECT  @start_with := 10,
                        @id := @start_with,
                        @level := 0
                ) vars, orgchart
        WHERE   @id IS NOT NULL
        ) ho
JOIN    orgchart hi
ON      hi.id = ho.id

