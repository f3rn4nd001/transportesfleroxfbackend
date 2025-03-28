DROP PROCEDURE IF EXISTS stpInsertarLogBanco;
CREATE PROCEDURE `stpInsertarLogBanco` (`loguuid2` VARCHAR(50),`uuiv` VARCHAR(50), `tNombrev` VARCHAR(80), `tNombreCorto` VARCHAR(15), `ecodEstatusv` VARCHAR(50), `logecodCreacion` VARCHAR(50),`logfhCreacion` VARCHAR(50),`logecodEdicion` VARCHAR(50),`logfhEdicion` VARCHAR(50))

BEGIN
	insert into logcatbanco(`ecodLogBanco`,`ecodBanco`,`tNombre`,`tNombreCorto`,`ecodEstatus`,`ecodCreacion`,`fhCreacion`,`ecodEdicion`,`fhEdicion`)
	values (loguuid2, uuiv, tNombrev, tNombreCorto, ecodEstatusv, logecodCreacion, logfhCreacion, logecodEdicion, logfhEdicion);
	SELECT loguuid2 AS Codigo;

end