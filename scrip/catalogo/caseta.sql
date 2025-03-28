DROP PROCEDURE IF EXISTS stpInsertarCatCasetas;
CREATE PROCEDURE `stpInsertarCatCasetas` (`uuiv` VARCHAR(50), `tNombrev` VARCHAR(100), `ecodOrigenv` VARCHAR(50),`ecodDestinov` VARCHAR(50),`tUbicacionv` VARCHAR(200), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80))

BEGIN

declare existe int;
set existe = (select count(*) from catcaseta where ecodCaseta = uuiv);
if existe = 0
then
	insert into catcaseta(`ecodCaseta`, `tNombre`, `ecodOrigen`, `ecodDestino`, `tUbicacion`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
	values (uuiv,tNombrev,ecodOrigenv,ecodDestinov,tUbicacionv,loginEcodUsuarios,NOW(),ecodEstatusv);
	SELECT uuiv AS Codigo;
else
		UPDATE catcaseta set tNombre=tNombrev, ecodOrigen=ecodOrigenv, ecodDestino=ecodDestinov, tUbicacion=tUbicacionv, ecodEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus = ecodEstatusv where ecodCaseta =uuiv;
		SELECT uuiv AS Codigo;	
END if;
end


DROP PROCEDURE IF EXISTS stpInsertarBitCatCasetas;
CREATE PROCEDURE `stpInsertarBitCatCasetas` (`ecodBitCasetav` VARCHAR(50), `uuiv` VARCHAR(50), `nEjesv` VARCHAR(50),`nCostov` FLOAT)
BEGIN

declare existe int;
set existe = (select count(*) from  bitcaseta where ecodBitCaseta  = ecodBitCasetav);
if existe = 0
then
	insert into bitcaseta(`ecodBitCaseta`, `ecodCaseta`, `nEjes`, `nCosto`)
	values (ecodBitCasetav,uuiv,nEjesv,nCostov);
	SELECT uuiv AS Codigo;
else
		UPDATE bitcaseta set ecodCaseta = uuiv, nEjes = nEjesv, nCosto = nCostov where ecodBitCaseta = ecodBitCasetav;
		SELECT ecodBitCasetav AS Codigo;	
END if;
end