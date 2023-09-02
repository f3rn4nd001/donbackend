DROP PROCEDURE IF EXISTS stpInsertarUsuario;
CREATE PROCEDURE `stpInsertarUsuario`(`tCURPv` VARCHAR(50),`tRFCv` VARCHAR(50), `tNombrev` VARCHAR(80), `tApellidov` VARCHAR(50), `EcodEstatusv` VARCHAR(250),`ecodTipoUsuariov` VARCHAR(250), `ecodUsuariosv` VARCHAR(250), `loginEcodUsuarios` VARCHAR(250), `motivoEliminacion` VARCHAR(250))   
BEGIN

declare existe int;
set existe = (select count(*) from catusuarios where ecodUsuario = ecodUsuariosv);
if existe =0
	then
		insert into catusuarios(`ecodUsuario`, `tCRUP`, `tRFC`, `tNombre`, `tApellido`,`fhCreacion`,`ecodEstatus`,`ecodTipoUsuario`,`ecodCreacion`)
		values (ecodUsuariosv,tCURPv,tRFCv,tNombrev,tApellidov,NOW(),EcodEstatusv,ecodTipoUsuariov,loginEcodUsuarios);
		SELECT  ecodUsuariosv AS Codigo;

		else
			if EcodEstatusv = 'ouiytuhfgdfsdcxvcvbngjutyrtfdsvhbvfrgfb'
				then
					UPDATE catusuarios set tCRUP=tCURPv, tRFC=tRFCv, tNombre=tNombrev, ecodTipoUsuario=ecodTipoUsuariov, tApellido=tApellidov, ecoEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus=EcodEstatusv, ecodEliminacion=loginEcodUsuarios, fhEliminacion=NOW(),tMotivoEliminacion=motivoEliminacion where ecodUsuario =ecodUsuariosv;
					SELECT ecodUsuariosv AS Codigo;
				ELSE
					UPDATE catusuarios set tCRUP=tCURPv, tRFC=tRFCv, tNombre=tNombrev, ecodTipoUsuario=ecodTipoUsuariov, tApellido=tApellidov, ecoEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus=EcodEstatusv where ecodUsuario =ecodUsuariosv;
					SELECT ecodUsuariosv AS Codigo;
			END if;
END if;
end



DROP PROCEDURE IF EXISTS stpInsertarBitCorreo;
CREATE PROCEDURE `stpInsertarBitCorreo`(`ecodCorreov` VARCHAR(250),`tCorreov` VARCHAR(250),`tcontrasena` VARCHAR(250),`loginEcodUsuarios` VARCHAR(250))   
BEGIN

declare existe int;
set existe = (select count(*) from bitcorreo where ecodCorreo = ecodCorreov);
if existe =0

then

insert into bitcorreo(`ecodCorreo`, `tCorreo`, `tpassword`,`fhCreacion`,`ecodCreacion`)
values (ecodCorreov,tCorreov,tcontrasena,NOW(),loginEcodUsuarios);

SELECT  ecodCorreov AS Codigo;
else

SELECT ecodCorreo AS Codigo from bitcorreo where ecodCorreo = ecodCorreov;
UPDATE bitcorreo set tCorreo=tCorreov,tpassword=tcontrasena ,ecodEdicion=loginEcodUsuarios, fhEdicion=NOW() where ecodCorreo =ecodCorreov;

END if;
end


DROP PROCEDURE IF EXISTS stpInsertarrelusuariocorreo;
CREATE PROCEDURE `stpInsertarrelusuariocorreo`(`uuidrelusuariocorreo2v` VARCHAR(250),`ecodCorreov` VARCHAR(250),`codigoUsuariov` VARCHAR(250))   
BEGIN
declare existe int;
set existe = (select count(*) from relusuariocorreo where ecodCorreo = ecodCorreov AND ecodUsuario = codigoUsuariov );
if existe =0

then

insert into relusuariocorreo(`ecodRelUsuarioCorreo`, `ecodCorreo`, `ecodUsuario` )
values (uuidrelusuariocorreo2v,ecodCorreov,codigoUsuariov);

SELECT  uuidrelusuariocorreo2v AS Codigo;
else

SELECT ecodRelUsuarioCorreo AS Codigo from relusuariocorreo where ecodCorreo = ecodCorreov AND ecodUsuario = codigoUsuariov;

END if;
end


DROP PROCEDURE IF EXISTS stpInsertarLogUsusario;
CREATE PROCEDURE `stpInsertarLogUsusario`(
`ecodLogUsusariov` VARCHAR(50),
`ecodUsuariov` VARCHAR(50),
`tNombrev` VARCHAR(30),
`tApellidov` VARCHAR(30),
`tCRUPv` VARCHAR(30),
`tRFCv` VARCHAR(30)
,`ecodEstatusv` VARCHAR(50),
`ecodTipoUsuariov` VARCHAR(250)
,`fhCreacionv` VARCHAR(30)
,`ecoEdicionv,` VARCHAR(50)
,`fhEdicionv` VARCHAR(30)
,`ecodCreacionv` VARCHAR(50)
,`tMotivoEliminacionv` VARCHAR(30)
,`fhEliminacionv` VARCHAR(30)
,`ecodEliminacionv` VARCHAR(50) 
)  
BEGIN
		insert into logcatusuarios(`ecodLogUsusario`, `ecodUsuario`, `tNombre`, `tApellido`, `tCRUP`,`tRFC`,`ecodEstatus`,`ecodTipoUsuario`,`fhCreacion`,`ecoEdicion`,`fhEdicion`,`ecodCreacion`,`tMotivoEliminacion`,`fhEliminacion`,`ecodEliminacion`)
		values (ecodLogUsusariov,ecodUsuariov,tNombrev,tApellidov,tCRUPv,tRFCv,ecodEstatusv,ecodTipoUsuariov,fhCreacionv,ecoEdicionv,fhEdicionv,ecodCreacionv,tMotivoEliminacionv,fhEliminacionv,ecodEliminacionv);
		SELECT  ecodLogUsusariov AS Codigo;

end