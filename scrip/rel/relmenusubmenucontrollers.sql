DROP PROCEDURE IF EXISTS stpInsertarRelMenuSubContro;
CREATE PROCEDURE `stpInsertarRelMenuSubContro` (`uuiv` VARCHAR(50), `ecodMenuv` VARCHAR(50), `ecodSubmenuv` VARCHAR(50), `ecodControllerv` VARCHAR(50))

BEGIN
	insert into relmenusubmenucontroller(`ecodRelMenuSubmenuController`, `ecodMenu`, `ecodSubmenu`, `ecodController`)
	values (uuiv,ecodMenuv,ecodSubmenuv,ecodControllerv);
	SELECT uuiv AS Codigo;
end