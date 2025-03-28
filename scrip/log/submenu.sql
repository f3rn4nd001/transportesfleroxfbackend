DROP PROCEDURE IF EXISTS stpInsertarLogSubMenu;
CREATE PROCEDURE `stpInsertarLogSubMenu`(loguuid2v VARCHAR(50),ecodSubMenuv VARCHAR(50),
tNombrev VARCHAR(25),logtUrlv VARCHAR(50),ecodCreacionv VARCHAR(50),fhCreacionv VARCHAR(60)
,ecodEdicionv VARCHAR(50),fhEdicionv VARCHAR(50),ecodEstatusv VARCHAR(50))

BEGIN
	insert into logcatsubmenu(`ecodLogSubmenu`, `ecodSubmenu`, `tNombre`, `tUrl`, `ecodCreacion`, `fhCreacion`,`ecodEdicion`, `fhEdicion`, `ecodEstatus`)
	values (loguuid2v,ecodSubMenuv,tNombrev,logtUrlv,ecodCreacionv,fhCreacionv,ecodEdicionv,fhEdicionv,ecodEstatusv);
	SELECT loguuid2v AS Codigo;

end