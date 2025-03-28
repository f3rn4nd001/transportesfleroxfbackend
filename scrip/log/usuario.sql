DROP PROCEDURE IF EXISTS stpInsertarLogCatUsuario;
CREATE PROCEDURE `stpInsertarLogCatUsuario` (`loguuid2` VARCHAR(50), `ecodUsuario` VARCHAR(50), `tNombrev` VARCHAR(40), `tApellidov` VARCHAR(40), `tCRUPv` VARCHAR(25), `tRFCv` VARCHAR(30), `tSexov` VARCHAR(20),`nTelefonov` VARCHAR(20),`tNotasv` VARCHAR(250),`nEdadv` INT(8), `ecodEstatusv` VARCHAR(50),`ecodTipoUsuariov` VARCHAR(50),`fhNacimientov` VARCHAR(50),`ecodCreacionv` VARCHAR(50),`fhCreacionv` VARCHAR(50),`ecoEdicionv` VARCHAR(50),`fhEdicionv` VARCHAR(50),`iUsuariov` longtext )

BEGIN

	insert into logcatusuarios(`ecodLogUsuario`,`ecodUsuario`, `tNombre`, `tApellido`, `tCRUP`, `tRFC`,`nTelefono`, `nEdad`, `tSexo`, `ecodEstatus`, `ecodTipoUsuario`, `fhCreacion`, `ecodCreacion`,`fhNacimiento`,`ecodEdicion`,`fhEdicion`,`iUsuario`,`tNotas` )
	values (loguuid2,ecodUsuario,tNombrev,tApellidov,tCRUPv,tRFCv,nTelefonov,nEdadv,tSexov,ecodEstatusv,ecodTipoUsuariov,fhCreacionv,ecodCreacionv,fhNacimientov,ecoEdicionv,fhEdicionv,iUsuariov,tNotasv);
	SELECT loguuid2 AS Codigo;

end