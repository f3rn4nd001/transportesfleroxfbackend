DROP PROCEDURE IF EXISTS stpInsertarLogControllers;
CREATE PROCEDURE `stpInsertarLogControllers`(loguuid2v VARCHAR(50),ecodControllersv VARCHAR(50),
tNombrev VARCHAR(80),logtUrlv VARCHAR(50),ecodCreacionv VARCHAR(50),fhCreacionv VARCHAR(60)
,ecodEdicionv VARCHAR(50),fhEdicionv VARCHAR(50),ecodEstatusv VARCHAR(50))

BEGIN
	insert into logCatControllers(`ecodLogControllers`, `ecodControllers`, `tNombre`, `tUrl`, `ecodCreacion`, `fhCreacion`,`ecodEdicion`, `fhEdicion`, `ecodEstatus`)
	values (loguuid2v,ecodControllersv,tNombrev,logtUrlv,ecodCreacionv,fhCreacionv,ecodEdicionv,fhEdicionv,ecodEstatusv);
	SELECT loguuid2v AS Codigo;

end

