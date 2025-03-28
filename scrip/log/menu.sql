DROP PROCEDURE IF EXISTS stpInsertarLogMenu;
CREATE PROCEDURE `stpInsertarLogMenu`(loguuid2v VARCHAR(50),ecodMenuv VARCHAR(50),
tNombrev VARCHAR(25),ecodIconosv VARCHAR(50),ecodCreacionv VARCHAR(50),fhCreacionv VARCHAR(60)
,ecodEdicionv VARCHAR(50),fhEdicionv VARCHAR(50),ecodEstatusv VARCHAR(50))

BEGIN
	insert into logcatmenu(`ecodLogMenu`, `ecodMenu`, `tNombre`, `ecodIconos`, `ecodCreacion`, `fhCreacion`,`ecodEdicion`, `fhEdicion`, `ecodEstatus`)
	values (loguuid2v,ecodMenuv,tNombrev,ecodIconosv,ecodCreacionv,fhCreacionv,ecodEdicionv,fhEdicionv,ecodEstatusv);
	SELECT loguuid2v AS Codigo;

end