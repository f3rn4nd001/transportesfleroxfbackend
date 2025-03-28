
DROP PROCEDURE IF EXISTS stpInsertarLogCatUsuarioEliminar;
CREATE PROCEDURE `stpInsertarLogCatUsuarioEliminar` (`loguuid2` VARCHAR(50), `ecodUsuariov` VARCHAR(50), `tNombrev` VARCHAR(40), `tApellidov` VARCHAR(40), `tCRUPv` VARCHAR(25), `tRFCv` VARCHAR(30), `tSexov` VARCHAR(20),`nTelefonov` VARCHAR(20),`tNotasv` VARCHAR(250),`nEdadv` INT(8), `ecodEstatusv` VARCHAR(50),`fhNacimientov` VARCHAR(50),`ecodCreacionv` VARCHAR(50),`fhCreacionv` VARCHAR(50),`mEliminacionv` VARCHAR(250),`InsertecodUsuario` VARCHAR(50),`iUsuariov` longtext )

BEGIN

    DECLARE ecodCorreov VARCHAR(50);
	DECLARE exito VARCHAR(250);

	insert into logcatusuarios(`ecodLogUsuario`,`ecodUsuario`, `tNombre`, `tApellido`, `tCRUP`, `tRFC`,`nTelefono`, `nEdad`, `tSexo`, `ecodEstatus`, `fhCreacion`, `ecodCreacion`,`fhNacimiento`,`ecodEdicion`,`fhEdicion`,`iUsuario`,`tNotas`,`tMotivoEliminacon` )
	values (loguuid2,ecodUsuariov,tNombrev,tApellidov,tCRUPv,tRFCv,nTelefonov,nEdadv,tSexov,ecodEstatusv,fhCreacionv,ecodCreacionv,fhNacimientov,InsertecodUsuario,NOW(),iUsuariov,tNotasv,mEliminacionv);
	
	DELETE FROM catusuarios WHERE ecodUsuario = ecodUsuariov;

  	SELECT ruc.ecodCorreo INTO ecodCorreov FROM relusuariocorreo ruc WHERE ruc.ecodUsuario = ecodUsuariov;
	
	DELETE FROM bitcorreo WHERE ecodCorreo = ecodCorreov;

	DELETE FROM relusuariocorreo WHERE ecodUsuario  = ecodUsuariov;

	SET exito = CONCAT('eliminar un registro relacionados el usuario con codigo - ', ecodUsuariov);
    SELECT exito AS mensaje;
		
end