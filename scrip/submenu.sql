DROP PROCEDURE IF EXISTS stpInsertarCatSubMenu;
CREATE PROCEDURE `stpInsertarCatSubMenu`(`uuiv` VARCHAR(50),`tNombrev` VARCHAR(50), `tUrlv` VARCHAR(80),`ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80) )   
BEGIN

declare existe int;
set existe = (select count(*) from catsubmenu where ecodSubmenu = uuiv);
if existe =0
	then
	insert into catsubmenu(`ecodSubmenu`, `tNombre`, `tUrl`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
	values (uuiv,tNombrev,tUrlv,loginEcodUsuarios,NOW(),ecodEstatusv);
	SELECT uuiv AS Codigo;
else
		UPDATE catsubmenu set tNombre=tNombrev, tUrl=tUrlv, ecodEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus=ecodEstatusv where ecodSubmenu=uuiv;
		SELECT uuiv AS Codigo;	
END if;
end