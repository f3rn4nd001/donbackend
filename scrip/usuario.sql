DROP PROCEDURE IF EXISTS stpInsertarUsuario;
CREATE PROCEDURE `stpInsertarUsuario`(`tCURPv` VARCHAR(50),`tRFCv` VARCHAR(50), `tNombrev` VARCHAR(80), `tApellidov` VARCHAR(50), `EcodEstatusv` VARCHAR(250),`ecodTipoUsuariov` VARCHAR(250), `ecodUsuariosv` VARCHAR(250), `loginEcodUsuarios` VARCHAR(250))   
BEGIN

declare existe int;
set existe = (select count(*) from catusuarios where ecodUsuario = ecodUsuariosv);
if existe =0


then


insert into catusuarios(`ecodUsuario`, `tCRUP`, `tRFC`, `tNombre`, `tApellido`,`fhCreacion`,`ecodEstatus`,`ecodTipoUsuario`,`ecodCreacion`)
values (ecodUsuariosv,tCURPv,tRFCv,tNombrev,tApellidov,NOW(),EcodEstatusv,ecodTipoUsuariov,loginEcodUsuarios);

SELECT  ecodUsuariosv AS Codigo;

else

SELECT ecodUsuario AS Codigo from catusuarios where ecodUsuario = ecodUsuariosv;
UPDATE catusuarios set tCURP=tCURPv,tRFC=tRFCv,tNombre = tNombrev, ecodTipoUsuario=ecodTipoUsuariov, tApellido=tApellidov,ecoEdicion=loginEcodUsuarios,fhEdicion=NOW() where ecodUsuario =ecodUsuariosv;

END if;
end