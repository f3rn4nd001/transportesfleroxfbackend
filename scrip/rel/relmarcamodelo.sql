DROP PROCEDURE IF EXISTS stpInsertarRelMarcaModelo;
CREATE PROCEDURE `stpInsertarRelMarcaModelo` (`uuiv` VARCHAR(50), `ecodMarcav` VARCHAR(50), `ecodModelov` VARCHAR(50))

BEGIN

	DECLARE existe int;
	set existe = (select count(*) from relmarcamodelo where ecodMarca = ecodMarcav AND ecodModelo = ecodModelov);
	if existe = 0
	then
		insert into relmarcamodelo(`ecodrelmarcamodelo`, `ecodMarca`, `ecodModelo`)
		values (uuiv, ecodMarcav, ecodModelov);
	
	END if;
end
