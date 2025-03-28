DROP PROCEDURE IF EXISTS stpEliminarBanco;
CREATE PROCEDURE `stpEliminarBanco` (`loguuid2` VARCHAR(50),`uuiv` VARCHAR(50), `tNombrev` VARCHAR(50), `tNombreCortov` VARCHAR(10),  `ecodEstatusv` VARCHAR(50), `logecodCreacion` VARCHAR(50),`logfhCreacion` VARCHAR(50),`logecodEdicion` VARCHAR(50),`mEliminacionv` VARCHAR(250))

BEGIN
	DECLARE exito VARCHAR(250);

	insert into logcatbanco(`ecodLogBanco`,`ecodBanco`,`tNombre`,`tNombreCorto`,`ecodEstatus`,`ecodCreacion`,`fhCreacion`,`ecodEdicion`,`fhEdicion`,`tMotivoEliminacion`)
	values (loguuid2, uuiv, tNombrev, tNombreCortov, ecodEstatusv, logecodCreacion, logfhCreacion, logecodEdicion, NOW(), mEliminacionv);
	
	DELETE FROM catbanco WHERE ecodBanco = uuiv;

	SET exito = CONCAT('eliminar un registro relacionados con el banco con codigo - ', uuiv);
  SELECT exito AS mensaje;

end