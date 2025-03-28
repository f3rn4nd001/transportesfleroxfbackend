DROP PROCEDURE IF EXISTS stpInsertarCatOperadorTransportes;
CREATE PROCEDURE `stpInsertarCatOperadorTransportes` (`uuiv` VARCHAR(50), `nLicenciav` VARCHAR(20), `fhExpedicionv` VARCHAR(50), `fhVencimientov` VARCHAR(50), 
`tTipov` VARCHAR(10), `tClasev` VARCHAR(5), `ecodUsuariov`VARCHAR(50), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(50))

BEGIN

declare existe int;
set existe = (select count(*) from catoperadortransportes where ecodOperador = uuiv);
if existe = 0

then
	insert into catoperadortransportes(`ecodOperador`, `ecodUsuario`, `nLicencia`, `fhExpedicion`, `fhVencimiento`, `tTipo`, `tClase`, `fhCreacion`, `ecodCreacion`, `ecodEstatus`)
	values (uuiv, ecodUsuariov, nLicenciav, fhExpedicionv, fhVencimientov, tTipov, tClasev, NOW(), loginEcodUsuarios, ecodEstatusv);
	SELECT uuiv AS Codigo;

else
		UPDATE catoperadortransportes set nLicencia = nLicenciav, fhExpedicion = fhExpedicionv, fhVencimiento = fhVencimientov, tTipo = tTipov, tClase = tClasev, ecodEdicion = loginEcodUsuarios,fhEdicion = NOW(), ecodEstatus = ecodEstatusv where ecodOperador = uuiv;
		SELECT uuiv AS Codigo;	
END if;
end