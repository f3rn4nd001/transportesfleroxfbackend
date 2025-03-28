DROP PROCEDURE IF EXISTS stpInsertarLogModelo;
CREATE PROCEDURE `stpInsertarLogModelo`(loguuid2v VARCHAR(50),ecodModelov VARCHAR(50),tNombrev VARCHAR(60),ecodCreacionv VARCHAR(50),fhCreacionv VARCHAR(60),ecodEdicionv VARCHAR(50),fhEdicionv VARCHAR(50),ecodEstatusv VARCHAR(50))

BEGIN
	insert into logcatmodelo(`ecodLogmodelo`, `ecodModelo`, `tNombre`, `ecodCreacion`, `fhCreacion`,`ecodEdicion`, `fhEdicion`, `ecodEstatus`)
	values (loguuid2v,ecodModelov,tNombrev,ecodCreacionv,fhCreacionv,ecodEdicionv,fhEdicionv,ecodEstatusv);
	SELECT loguuid2v AS Codigo;

end

