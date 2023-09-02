DROP PROCEDURE IF EXISTS stpInsertarCatMenu;
CREATE PROCEDURE `stpInsertarCatMenu`(`uuiv` VARCHAR(50),`tNombrev` VARCHAR(50), `Iiconsv` VARCHAR(80),`ecodEstatusv` VARCHAR(50), `loginEcodUsuarios` VARCHAR(80) )   
BEGIN

declare existe int;
set existe = (select count(*) from catmenu where ecodMenu = uuiv);
if existe =0
	then
	insert into catmenu(`ecodMenu`, `tNombre`, `Iconos`, `ecodCreacion`, `fhCreacion`, `ecodEstatus`)
	values (uuiv,tNombrev,Iiconsv,loginEcodUsuarios,NOW(),ecodEstatusv);
	SELECT uuiv AS Codigo;
else
		UPDATE catmenu set tNombre=tNombrev, Iconos=Iiconsv, ecodEdicion=loginEcodUsuarios, fhEdicion=NOW(), ecodEstatus=ecodEstatusv where ecodMenu =uuiv;
		SELECT uuiv AS Codigo;	
END if;
end

CREATE PROCEDURE `stpInsertarLogMenu`(
`ecodLogMenuv` VARCHAR(50),
`ecodMenuv` VARCHAR(50),
`tNombrev` VARCHAR(50),
`Iconosv` VARCHAR(50),
`ecodCreacionv` VARCHAR(50),
`fhCreacionv` VARCHAR(50),
`ecodEdicionv` VARCHAR(50),
`fhEdicionv` VARCHAR(50),
`ecodEstatusv` VARCHAR(50))
BEGIN

	insert into logCatMenu(`ecodLogMenu`, `ecodMenu`, `tNombre`, `Iconos`, `ecodCreacion`, `fhCreacion`,`ecodEdicion`, `fhEdicion`, `ecodEstatus`)
	values (ecodLogMenuv,ecodMenuv,tNombrev,Iconosv,ecodCreacionv,fhCreacionv,ecodEdicionv,fhEdicionv,ecodEstatusv);
	SELECT ecodLogMenuv AS Codigo;

end

