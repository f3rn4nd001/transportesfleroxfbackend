DROP PROCEDURE IF EXISTS stpInsertarCatBanco;
CREATE PROCEDURE `stpInsertarCatBanco` (`uuiv` VARCHAR(50), `tNombrev` VARCHAR(80), `tNombreCortov` VARCHAR(15), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80))

BEGIN	

DECLARE MDupri VARCHAR(250);

DECLARE existedata int;

DECLARE existe int;
set existe = (select count(*) from catbanco where ecodBanco = uuiv);
if existe = 0
	
then
	set existedata = (select count(*) from catbanco where tNombre = tNombrev OR tNombreCorto = tNombreCortov);
	if existedata = 0
	then
		
		insert into catbanco(`ecodBanco`, `tNombre`, `tNombreCorto`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
		values (uuiv, tNombrev, tNombreCortov, loginEcodUsuarios, NOW(), ecodEstatusv);
		SELECT uuiv AS Codigo;
		
	else
		SET MDupri = CONCAT('Datos duplicados');
		SELECT MDupri AS mensaje;
	END if;

else
		UPDATE catbanco set tNombre = tNombrev, tNombreCorto = tNombreCortov, ecodEdicion = loginEcodUsuarios, fhEdicion = NOW(), ecodEstatus = ecodEstatusv where ecodBanco = uuiv;
		SELECT uuiv AS Codigo;	
END if;
end