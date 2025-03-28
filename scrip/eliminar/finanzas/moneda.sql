DROP PROCEDURE IF EXISTS stpEliminarMoneda;
CREATE PROCEDURE `stpEliminarMoneda` (`uuiv` VARCHAR(50))

BEGIN
	DECLARE exito VARCHAR(250);
	DELETE FROM catmoneda WHERE ecodMoneda = uuiv;

	SET exito = CONCAT('eliminar un registro relacionados con la moneda con codigo - ', uuiv);
  SELECT exito AS mensaje;

end