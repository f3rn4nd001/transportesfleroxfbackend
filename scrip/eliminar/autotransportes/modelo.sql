DROP PROCEDURE IF EXISTS stpInsertarLogCatMarcaEliminar;
CREATE PROCEDURE `stpInsertarLogCatMarcaEliminar` (`loguuid2` VARCHAR(50), `ecodv` VARCHAR(50), `tNombrev` VARCHAR(40), `tPaisOrigenv` VARCHAR(20),  `ecodEstatusv` VARCHAR(50),`ecodCreacionv` VARCHAR(50),`fhCreacionv` VARCHAR(50),`mEliminacionv` VARCHAR(250),`InsertecodUsuario` VARCHAR(50))

BEGIN
  DECLARE exito VARCHAR(250);

	insert into logcatmarca(`ecodLogMarca`,`ecodMarca`, `tNombre`, `tPaisOrigen`,`ecodEstatus`, `fhCreacion`, `ecodCreacion`,`ecodEdicion`,`fhEdicion`,`tMotivoEliminacion`)
	values (loguuid2,ecodv,tNombrev,tPaisOrigenv,ecodEstatusv,fhCreacionv,ecodCreacionv,InsertecodUsuario,NOW(),mEliminacionv);

	DELETE FROM catmarca WHERE ecodMarca = ecodv;
	
	DELETE FROM relmarcamodelo WHERE ecodMarca = ecodv;

	SET exito = CONCAT('Se creo un registro en la tabla logcat con codigo - ',loguuid2,' eliminar un registro demarca con codigo - ', ecodv);
  SELECT exito AS mensaje;
end