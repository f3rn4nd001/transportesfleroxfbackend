DROP PROCEDURE IF EXISTS stpInsertarCatMenu;
CREATE PROCEDURE `stpInsertarCatMenu` (`uuiv` VARCHAR(50), `tNombrev` VARCHAR(50), `ecodIiconsv` VARCHAR(80), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80))

BEGIN

declare existe int;
set existe = (select count(*) from catmenu where ecodMenu = uuiv);
if existe = 0
	then
	insert into catmenu(`ecodMenu`, `tNombre`, `ecodIconos`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
	values (uuiv,tNombrev,ecodIiconsv,loginEcodUsuarios,NOW(),ecodEstatusv);
	SELECT uuiv AS Codigo;
else
		UPDATE catmenu set tNombre=tNombrev, ecodIconos=ecodIiconsv, ecodEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus = ecodEstatusv where ecodMenu =uuiv;
		SELECT uuiv AS Codigo;	
END if;
end