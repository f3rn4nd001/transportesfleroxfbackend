DROP PROCEDURE IF EXISTS stpInsertarCatMoneda;
CREATE PROCEDURE `stpInsertarCatMoneda` (`uuiv` VARCHAR(50), `tNombrev` VARCHAR(50), `tNombreCortov` VARCHAR(5),  `nValorMexicoAv` FLOAT, `nValorAMexicov` FLOAT, `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80))

BEGIN	

DECLARE MDupri VARCHAR(250);

DECLARE existedata int;

DECLARE existe int;
set existe = (select count(*) from catmoneda where ecodMoneda = uuiv);
if existe = 0
	
then
	set existedata = (select count(*) from catmoneda where tNombre = tNombrev OR tNombreCorto = tNombreCortov);
	if existedata = 0
	then
		
		insert into catmoneda(`ecodMoneda`, `tNombre`, `tNombreCorto`,  `nValorMexicoA`, `nValorAMexico` ,`ecodCreacion`, `fhCreacion`, `ecodEstatus`)
		values (uuiv, tNombrev, tNombreCortov, nValorMexicoAv, nValorAMexicov, loginEcodUsuarios, NOW(), ecodEstatusv);
		SELECT uuiv AS Codigo;
		
	else
		SET MDupri = CONCAT('Datos duplicados');
		SELECT MDupri AS mensaje;
	END if;

else
		UPDATE catmoneda set tNombre = tNombrev, tNombreCorto = tNombreCortov, nValorMexicoA = nValorMexicoAv, nValorAMexico = nValorAMexicov, ecodEdicion = loginEcodUsuarios, fhEdicion = NOW(), ecodEstatus = ecodEstatusv where ecodMoneda = uuiv;
		SELECT uuiv AS Codigo;	
END if;
end