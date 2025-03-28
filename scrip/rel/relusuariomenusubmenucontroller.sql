DROP PROCEDURE IF EXISTS stpInsertarRelUsuarioMenuSubContro;
CREATE PROCEDURE `stpInsertarRelUsuarioMenuSubContro` (`uuiv` VARCHAR(50), `ecodUsuariov` VARCHAR(50), `ecodMenuv` VARCHAR(50), `ecodSubmenuv` VARCHAR(50), `ecodControllerv` VARCHAR(50),`tTokenv` VARCHAR(500))

BEGIN
	insert into relusuariomenusubmenucontroller(`ecodRelusRarioMenuSubmenuController`, `ecodUsuario` ,`ecodMenu`, `ecodSubmenu`, `ecodController`,`tToken`)
	values (uuiv,ecodUsuariov,ecodMenuv,ecodSubmenuv,ecodControllerv,tTokenv);
	SELECT uuiv AS Codigo;
end