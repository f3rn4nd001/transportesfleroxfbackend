DROP PROCEDURE IF EXISTS stpInsertarCatModelo;
CREATE PROCEDURE `stpInsertarCatModelo` (`uuiv` VARCHAR(50), `tNombrev` VARCHAR(50), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80))

BEGIN
	DECLARE MDupri VARCHAR(250);
	DECLARE existedata int;
	DECLARE existe int;
	SET existe = (SELECT count(*) FROM catmodelo WHERE ecodModelo = uuiv);
	if existe = 0
		THEN
			SET existedata = (SELECT count(*) FROM catmodelo WHERE tNombre = tNombrev);
			if existedata = 0
				THEN
					INSERT INTO catmodelo(`ecodModelo`, `tNombre`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
					VALUES (uuiv,tNombrev,loginEcodUsuarios,NOW(),ecodEstatusv);
					SELECT uuiv AS Codigo;
				ELSE
				SET MDupri = CONCAT('Datos duplicados');
				SELECT MDupri AS mensaje;
			END if;
	ELSE
		UPDATE catmodelo set tNombre=tNombrev, ecodEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus = ecodEstatusv where ecodModelo = uuiv;
		SELECT uuiv AS Codigo;	
	END if;
end