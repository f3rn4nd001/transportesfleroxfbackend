
DROP PROCEDURE IF EXISTS stpInsertarLogCatCasetaEliminar;
CREATE PROCEDURE `stpInsertarLogCatCasetaEliminar` (`ecodv` VARCHAR(50))

BEGIN
  DECLARE exito VARCHAR(250);
	
	DELETE FROM bitcaseta WHERE ecodCaseta = ecodv;

	DELETE FROM catcaseta WHERE ecodCaseta = ecodv;

	SET exito = CONCAT('eliminar un registro relacionados con la caseta con codigo - ', ecodv);
  SELECT exito AS mensaje;
end