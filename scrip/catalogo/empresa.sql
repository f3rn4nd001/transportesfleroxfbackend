DROP PROCEDURE IF EXISTS stpInsertarCatEmpresa;
CREATE PROCEDURE `stpInsertarCatEmpresa` (`uuiv` VARCHAR(50), `tNombrev` VARCHAR(100), `tRazonSocialv` VARCHAR(60), `tColoniav` VARCHAR(40), `tCallev` VARCHAR(40), `ecodTipoEmpresav` VARCHAR(50), `tComplementosv` VARCHAR(150),`nCPv` INT(10), `ecodEstadov` INT(6), `ecodMunicipiov` INT(7), `nNumerov` INT(10), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80))

BEGIN

declare existe int;
set existe = (select count(*) from catempresas where ecodEmpresas = uuiv);
if existe = 0
	then
	insert into catempresas(`ecodEmpresas`, `tNombre`, `tRazonSocial`, `ecodTipoEmpresa`, `ecodEstado`, `ecodMunicipio`, `nCP`, `tColonia`, `tCalle`, `nNumero`, `tComplementos`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
	values (uuiv, tNombrev, tRazonSocialv, ecodTipoEmpresav, ecodEstadov, ecodMunicipiov, nCPv,tColoniav, tCallev, nNumerov, tComplementosv, loginEcodUsuarios, NOW(), ecodEstatusv);
	SELECT uuiv AS Codigo;

else
		UPDATE catempresas set tNombre = tNombrev, tRazonSocial = tRazonSocialv, ecodTipoEmpresa = ecodTipoEmpresav, ecodEstado = ecodEstadov, ecodMunicipio = ecodMunicipiov, nCP = nCPv, tColonia = tColoniav, tCalle = tCallev, nNumero = nNumerov,tComplementos = tComplementosv, ecodEdicion = loginEcodUsuarios, fhEdicion = NOW(), ecodEstatus = ecodEstatusv where ecodEmpresas = uuiv;
		SELECT uuiv AS Codigo;	
END if;
end