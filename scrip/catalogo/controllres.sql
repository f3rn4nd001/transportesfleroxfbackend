DROP PROCEDURE IF EXISTS stpInsertarCatControllers;
CREATE PROCEDURE `stpInsertarCatControllers` (`uuiv` VARCHAR(50), `tNombrev` VARCHAR(50), `tUrlv` VARCHAR(100), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80))

BEGIN

declare existe int;
set existe = (select count(*) from catcontroller where ecodControler = uuiv);
if existe = 0
	then
	insert into catcontroller(`ecodControler`, `tNombre`, `tUrl`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
	values (uuiv,tNombrev,tUrlv,loginEcodUsuarios,NOW(),ecodEstatusv);
	SELECT uuiv AS Codigo;
else
    UPDATE catcontroller set tNombre = tNombrev, tUrl = tUrlv, ecodEdicion = loginEcodUsuarios, fhEdicion = NOW(), ecodEstatus = ecodEstatusv where ecodControler = uuiv;
    SELECT uuiv AS Codigo;	
END if;
end