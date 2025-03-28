
DROP PROCEDURE IF EXISTS stpInsertarLogCatControllersEliminar;
CREATE PROCEDURE `stpInsertarLogCatControllersEliminar` (`loguuid2` VARCHAR(50), `ecodv` VARCHAR(50), `tNombrev` VARCHAR(40), `tUrl` VARCHAR(40),  `ecodEstatusv` VARCHAR(50),`ecodCreacionv` VARCHAR(50),`fhCreacionv` VARCHAR(50),`mEliminacionv` VARCHAR(250),`InsertecodUsuario` VARCHAR(50))

BEGIN
  DECLARE exito VARCHAR(250);

	insert into logcatcontrollers(`ecodLogControllers`,`ecodControllers`, `tNombre`, `tUrl`,`ecodEstatus`, `fhCreacion`, `ecodCreacion`,`ecodEdicion`,`fhEdicion`,`tMotivoEliminacon`)
	values (loguuid2,ecodv,tNombrev,tUrl,ecodEstatusv,fhCreacionv,ecodCreacionv,InsertecodUsuario,NOW(),mEliminacionv);

	DELETE FROM catcontroller WHERE ecodControler = ecodv;

	SET exito = CONCAT('Se creo un registro en la tabla logcatcontrollers con codigo - ',loguuid2,' eliminar un registro de la tabra catcontroller con codigo - ', ecodv);
  SELECT exito AS mensaje;
end