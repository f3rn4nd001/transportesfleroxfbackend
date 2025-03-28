DROP PROCEDURE IF EXISTS stpInsertarCatMarca;
CREATE PROCEDURE `stpInsertarCatMarca` (`uuiv` VARCHAR(50), `tNombrev` VARCHAR(50), `tPaisOrigenv` VARCHAR(80), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80))

BEGIN
DECLARE MDupri VARCHAR(250);
DECLARE existedata int;
DECLARE existe int;
set existe = (select count(*) from catmarca where ecodMarca = uuiv);
if existe = 0
	then
	set existedata = (select count(*) from catmarca where tNombre = tNombrev);
	if existedata = 0
	then
		insert into catmarca(`ecodMarca`, `tNombre`, `tPaisOrigen`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
		values (uuiv,tNombrev,tPaisOrigenv,loginEcodUsuarios,NOW(),ecodEstatusv);
		SELECT uuiv AS Codigo;
	else
		SET MDupri = CONCAT('Datos duplicados');
		SELECT MDupri AS mensaje;
	END if;
else
		UPDATE catmarca set tNombre=tNombrev, tPaisOrigen=tPaisOrigenv, ecodEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus = ecodEstatusv where ecodMarca = uuiv;
		SELECT uuiv AS Codigo;	
END if;
end