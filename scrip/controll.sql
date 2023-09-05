DROP PROCEDURE IF EXISTS stpInsertarCatControllers;
CREATE PROCEDURE `stpInsertarCatControllers`(`uuiv` VARCHAR(50),`tNombrev` VARCHAR(50), `tUrlv` VARCHAR(80),`ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80) )   
BEGIN

declare existe int;
set existe = (select count(*) from cotcontroller where ecodControler = uuiv);
if existe =0
	then
	insert into cotcontroller(`ecodControler`, `tNombre`, `turl`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
	values (uuiv,tNombrev,tUrlv,loginEcodUsuarios,NOW(),ecodEstatusv);
	SELECT uuiv AS Codigo;
else
		UPDATE cotcontroller set tNombre=tNombrev, turl=tUrlv, ecodEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus=ecodEstatusv where ecodControler=uuiv;
		SELECT uuiv AS Codigo;	
END if;
end