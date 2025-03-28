DROP PROCEDURE IF EXISTS stpInsertarCatOperadorPSP;
CREATE PROCEDURE `stpInsertarCatOperadorPSP` (`uuiv` VARCHAR(50), `ecodUsuariov`VARCHAR(50), `ecodtipopagov`VARCHAR(50), `ecodMonedav`VARCHAR(50), `ecodBancov`VARCHAR(50), `tCuentaBancariav`VARCHAR(40), `tCorreov`VARCHAR(100), `nTelefonov` VARCHAR(20), `tRFCv` VARCHAR(30),`tCRUPv` VARCHAR(25), `ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(50))

BEGIN

DECLARE existedata int;
declare existe int;
set existe = (select count(*) from catoperadorpsp where ecodOperadorPSP = uuiv);
if existe = 0
then
		set existedata = ( ecodtipopagov = 'a6c00ead-f1ed-4090-bd0c-16cb88c46090');
		if existedata = 1
		then
			UPDATE catusuarios set tRFC = tRFCv, tCRUP = tCRUPv where ecodUsuario = ecodUsuariov;
		END if;

		insert into catoperadorpsp(`ecodOperadorPSP`, `ecodBanco`, `ecodUsuario`, `nTelefono`, `ecodtipopago`, `tCuentaBancaria`, `ecodMoneda`, `tCorreo`, `fhCreacion`, `ecodCreacion`, `ecodEstatus`) 
    values (uuiv, ecodBancov, ecodUsuariov, nTelefonov, ecodtipopagov, tCuentaBancariav, ecodMonedav, tCorreov, NOW(), loginEcodUsuarios, ecodEstatusv);
    SELECT uuiv AS Codigo;
else

		set existedata = ( ecodtipopagov = 'a6c00ead-f1ed-4090-bd0c-16cb88c46090');
		if existedata = 1
		then
			UPDATE catusuarios set tRFC = tRFCv, tCRUP = tCRUPv where ecodUsuario = ecodUsuariov;
		END if;

    UPDATE catoperadorpsp set ecodBanco = ecodBancov, nTelefono = nTelefonov, ecodtipopago = ecodtipopagov, tCuentaBancaria = tCuentaBancariav, ecodMoneda = ecodMonedav, tCorreo = tCorreov, ecodEdicion = loginEcodUsuarios, fhEdicion = NOW(), ecodEstatus = ecodEstatusv where ecodOperadorPSP = uuiv;
    SELECT uuiv AS Codigo;	
	END if;
end