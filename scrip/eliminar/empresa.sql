DROP PROCEDURE IF EXISTS stpEliminarEmpresa;
CREATE PROCEDURE `stpEliminarEmpresa` (`loguuid2` VARCHAR(50),`uuiv` VARCHAR(50), `tNombrev` VARCHAR(100), `tRazonSocialv` VARCHAR(60), `ecodTipoEmpresav` VARCHAR(50),`ecodEstadov` INT(6), `ecodMunicipiov` INT(7),`nCPv` INT(10),`tColoniav` VARCHAR(40), `tCallev` VARCHAR(40), `nNumerov` INT(10), `tComplementosv` VARCHAR(150),   `ecodEstatusv` VARCHAR(50), `logecodCreacion` VARCHAR(50),`logfhCreacion` VARCHAR(50),`logecodEdicion` VARCHAR(50),`mEliminacionv` VARCHAR(250))

BEGIN
	DECLARE exito VARCHAR(250);

	insert into logcatempresas(`ecodLogEmpresas`,`ecodEmpresas`,`tNombre`,`tRazonSocial`,`ecodTipoEmpresa`,`ecodEstado`,`ecodMunicipio`,`nCP`,`tColonia`,`tCalle`,`nNumero`,`tComplementos`,`ecodEstatus`,`ecodCreacion`,`fhCreacion`,`ecodEdicion`,`fhEdicion`,`tMotivoEliminacion`)
	values (loguuid2, uuiv, tNombrev, tRazonSocialv, ecodTipoEmpresav, ecodEstadov, ecodMunicipiov, nCPv, tColoniav, tCallev, nNumerov, tComplementosv, ecodEstatusv, logecodCreacion, logfhCreacion, logecodEdicion, NOW(), mEliminacionv);
	
	DELETE FROM catempresas WHERE ecodEmpresas = uuiv;

	SET exito = CONCAT('eliminar un registro relacionados con la empresas con codigo - ', uuiv);
  SELECT exito AS mensaje;

end