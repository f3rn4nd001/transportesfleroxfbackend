DROP PROCEDURE IF EXISTS stpInsertarLogMarca;
CREATE PROCEDURE `stpInsertarLogMarca`(loguuid2v VARCHAR(50),ecodMarcav VARCHAR(50),tNombrev VARCHAR(30),tPaisOrigenv VARCHAR(80),ecodCreacionv VARCHAR(50),fhCreacionv VARCHAR(60),ecodEdicionv VARCHAR(50),fhEdicionv VARCHAR(50),ecodEstatusv VARCHAR(50))

BEGIN
	insert into logcatmarca(`ecodLogMarca`, `ecodMarca`, `tNombre`, `tPaisOrigen`, `ecodCreacion`, `fhCreacion`,`ecodEdicion`, `fhEdicion`, `ecodEstatus`)
	values (loguuid2v,ecodMarcav,tNombrev,tPaisOrigenv,ecodCreacionv,fhCreacionv,ecodEdicionv,fhEdicionv,ecodEstatusv);
	SELECT loguuid2v AS Codigo;

end

