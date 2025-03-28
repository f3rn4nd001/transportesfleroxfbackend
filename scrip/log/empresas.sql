DROP PROCEDURE IF EXISTS stpInsertarLogEmpresa;
CREATE PROCEDURE `stpInsertarLogEmpresa` (`loguuid2` VARCHAR(50),`uuiv` VARCHAR(50), `tNombrev` VARCHAR(100), `tRazonSocialv` VARCHAR(60), `ecodTipoEmpresav` VARCHAR(50),`ecodEstadov` INT(6), `ecodMunicipiov` INT(7),`nCPv` INT(10),`tColoniav` VARCHAR(40), `tCallev` VARCHAR(40), `nNumerov` INT(10), `tComplementosv` VARCHAR(150),   `ecodEstatusv` VARCHAR(50), `logecodCreacion` VARCHAR(50),`logfhCreacion` VARCHAR(50),`logecodEdicion` VARCHAR(50),`logfhEdicion` VARCHAR(50))

BEGIN
	insert into logcatempresas(`ecodLogEmpresas`,`ecodEmpresas`,`tNombre`,`tRazonSocial`,`ecodTipoEmpresa`,`ecodEstado`,`ecodMunicipio`,`nCP`,`tColonia`,`tCalle`,`nNumero`,`tComplementos`,`ecodEstatus`,`ecodCreacion`,`fhCreacion`,`ecodEdicion`,`fhEdicion`)
	values (loguuid2, uuiv, tNombrev, tRazonSocialv, ecodTipoEmpresav, ecodEstadov, ecodMunicipiov, nCPv, tColoniav, tCallev, nNumerov, tComplementosv, ecodEstatusv, logecodCreacion, logfhCreacion, logecodEdicion, logfhEdicion);
	SELECT loguuid2 AS Codigo;

end