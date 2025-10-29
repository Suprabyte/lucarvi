<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Empleado, Area, Cargo};

class EmpleadosSeeder extends Seeder
{
    public function run(): void
    {
        // [area, cargo, ap_paterno, ap_materno, nombres, fecha_ingreso(dd.mm.yy), contrato, dni, sueldo_total, tipo_trabajador, celular, direccion, email]
        $rows = [
            ['AVES VIVAS','ESTIBADOR','CACHIQUE','GARCIA','REYSER ANTONIO','01.11.22','INDETER.','46910255','1,563.00','OBRERO','948873662','Mz. K Lt. 09 Asoc. de Viv Bello Horizonte De Copacabana - Puente Piedra- Lima','reysercachgar@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','CHAVARRIA','MEDINA','EDWIN FRANK','01.07.25','INDETER.','75326344','1,450.00','OBRERO','935332732','Asen.H San Pedro de Choque Mz. K Lt. 2 - Puente Piedra - Lima','e75102459@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','DE LA O','LUCAS','LUIS','18.05.19','INDETER.','43439875','1,563.00','OBRERO','901184611','AA.HH Las Esteras De Ancon Mz. K Lt. 14 - Ancon - Lima',null],
            ['AVES VIVAS','ESTIBADOR','HERRERA','BENDITO','OVER','01.02.25','MODALIDAD','71300457','1,563.00','OBRERO','971180755','Mz. K LT. 7 Asoc. de Viv. Proyectos Ecologicos Los hijos de Santa Rosa - Puente Piedra','overherrerabendito04@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','HUAYABAN','PAYAHUA','ERICK RICKY','01.11.22','INDETER.','70262353','1,563.00','OBRERO','933683138','Asoc. Prop Portales de Copacabana MZ. D LT. 42 - Puente Piedra - Lima','erickrickyhuayabanpayahua@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','JULCAHUANGA','LOPEZ','ELI','01.10.23','INDETER.','78023744','1,450.00','OBRERO','930531982','Av. San Juan Este Mz. C Lt. 3 - Puente Piedra - Lima','julcahuangalopezelysito@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','JULCA','HUERTA','ABEL CORNELIO','01.01.24','MODALIDAD','71017153','1,563.00','OBRERO','924673358','Asoc. de Viv Productiva la Arboleda Mz. D Lt. 12 – Santa Rosa - Lima','abeljulcahuerta28@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','LOPEZ','MUÑOZ','PABLO RONALD','01.06.14','INDETER.','73382815','1,563.00','OBRERO','981123059','Asoc las Palmeras Mz. A Lt. 6 Puente Piedra - Lima','palomuro95@gamil.com'],
            ['AVES VIVAS','ESTIBADOR','MAQUIN','SHICSHI','ROMMEL CAPISTRANO','01.04.25','MODALIDAD','70815172','1,563.00','OBRERO','917913688','CMTE. VEC. SANTA BARBARA II ET MZ. J LT. 36 LIMA-LIMA-PUENTE PIEDRA','maquinromeelcapistrano@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','PINEDO','SILVA','CARLOS ENRIQUE','01.01.11','INDETER.','45299567','1,563.00','OBRERO','978433410','Asoc. de Viv el Dorado Etapa II Mz. A Lt 05B - Puente Piedra - Lima','axelpinedosilva176@hotmail.com'],
            ['AVES VIVAS','ESTIBADOR','ROSALES','CHAVEZ','JUAN GUILLERMO','01.11.22','INDETER.','44376626','1,563.00','OBRERO','973523101','Mz. D Lt. 23  Asoc. Las Vegonias del Norte - Puente Piedra - Lima','hildaparedes8105@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','SANCHEZ','VELASQUEZ','CHAYO AUGUSTO','01.01.24','MODALIDAD','76660427','1,450.00','OBRERO','935013788','Asoc. Las Begonias del norte Mz. E Lt. 5 - Puente Piedra','augusto1234ch@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','TORRES','ALVA','MIGUEL ANTONY','01.04.24','MODALIDAD','76974642','1,450.00','OBRERO','934028090','Mz. J Lt. 13 Asoc. de Viv Los Frutales del Norte – Puente Piedra - Lima','migueltorreszx112@gmail.com'],
            ['AVES VIVAS','ESTIBADOR','VARGAS','CHAVEZ','JOSE ANGEL','01.05.25','INDETER.','44740860','1,563.00','OBRERO','983496615','Asoc. Prop. Portales de copacabana Mz. D. Lt.42 - Puente Pidra - Lima','vargaschavezjoseangel431@gmail.com'],

            ['AVES VIVAS','ENC. DE DESPACHO VIVO','QUIROGA','ANTON','JUAN ANTONIO','01.07.08','INDETER.','45857758','2,413.00','EMPLEADO','977754582','Calle Los Eucaliptos Mz B Lt 33 A - Asoc Los Algarrobos - Puente Piedra - Lima','quirogajuan200689@gmail.com'],

            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','AMARILLO','LAURA','JERSON ALCIDES','01.10.24','MODALIDAD','75229980','1,450.00','OBRERO','937235390','Mz. J  Lt. 12 A.H Hijos de Villa del Mar Mi Perú - Ventanilla - Callao','jersonlaura2830@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','AMARILLO','LAURA','JAIR ANTONY','01.07.25','MODALIDAD','62002951','1,450.00','OBRERO','928490256','A.H Hijos de Villa del Mar Mza. J Lote. 12 Mi Peru - Callao','jairamarillo427@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','ARIAS','RENGIFO','LILIANA CARMEN','01.08.20','INDETER.','46974274','1,563.00','OBRERO','912610446','Mz. D4 Lt 10 AA.HH Las Lomas de Ventanilla - Puente Piedra - Lima','liliarias110613@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','ASCUE','GUTIERREZ','NELSON LEONARDO','01.03.23','MODALIDAD','76041975','1,450.00','OBRERO','922601253','Asent. H Laderas de Chillon Etapa I Mz. L Lt.69 - Puente Piedra- Lima','leonardoascueg@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','DIESTRA','PEREZ','ENRIQUE BEDER','01.11.17','INDETER.','44875096','1,563.00','OBRERO','992910938','Calle San Luis Mz D LT 1D Asoc. de Viv El Dorado Segunda Etapa - Zapallal - Puente Piedra - Lima','ediestraperez@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','ESPINOZA','RODRIGUEZ','JUAN CARLOS','01.09.22','INDETER.','73456389','1,450.00','OBRERO','935809739','Asen.H Nueva Canada Mz. D Lt. 1 - Puente Piedra - Lima','juanKaespinozarodriguez@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','HUERTA','SAMAR','ISAEL MALETH','01.06.25','MODALIDAD','76911269','1,450.00','OBRERO','948263984','MZ.A LT.13 JERUSALEN ZAPALLAL LIMA-LIMA-PUENTE PIEDRA','isael.samar@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','HUAMAN','GALARRETA','LUIS FERNANDO','01.08.23','MODALIDAD','72156685','1,450.00','OBRERO','929901017','Mz. M Lt.39 Urb. Santo Domingo II Etapa - Carabayllo - Lima','luishuamangalarreta@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','NEIRA','BERMEO','CRISTHIAN ANTAURO','01.11.24','MODALIDAD','73104264','1,450.00','OBRERO','964251334','Asociación de Pobladores Micaela Bastidas Mz. B  Lt. 07 – Puente Piedra','cristhianantauroneirabermeo@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','PAUCAR','MONTAÑO','ARTURO RODOLFO','01.06.25','MODALIDAD','75296202','1,450.00','OBRERO','929197608','AMPL. LADERAS DE CHILLON MZ. A3 LT. 265 LIMA-LIMA-PUENTE PIEDRA','4rturin52junio@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','PEREZ','TAVARA','FREDY EDMUNDO','01.09.05','INDETER.','03325792','1,450.00','OBRERO','941729542','Jr. Asunción 680 el Parral Urb el Parral - Comas - Lima','pfredy662@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','QUISPE','ASTOPILLO','MARIA YOLANDA','01.01.21','INDETER.','70210911','1,563.00','OBRERO','900698861','Mz. F Lt. 1 AA.HH 05 de Octubre - Puente Piedra - Lima','quispeastopillonmariayolanda@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','RIVAS','RESURRECCION','ARIAN ALBERTO','01.08.24','MODALIDAD','74748892','1,563.00','OBRERO','922409387','Asent. H San Pedro de Choque Mz. V1 Lt.3 - Puente Piedra - Lima','arianrivas98@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','RIVERA','PANTOJA','DORIS EUSILDA','01.04.18','INDETER.','72091431','1,563.00','OBRERO','944954052','Calle los Eucaliptos Mz B Lt 33A  Asoc. Los Algarrobos - Puente Piedra - Lima','dorisriverapantoja@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','RODRIGUEZ','ALARCON','EDWIN','01.05.23','MODALIDAD','73214065','1,563.00','OBRERO','994378179','Asoc De Viv Proyectores Ecologicos Los Hijos De Santa Rosa Mz: L Lt: 18 - Puente Piedra - Lima','edwinrodriguezalarcon52@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','SANCHEZ','VELASQUEZ','ANDY ERICK','01.03.25','MODALIDAD','60814425','1,450.00','OBRERO','969803725','Mz. 12 Lt. 4B Asoc. Pequeños Agricultores de Zapallal – Puente Piedra – Lima','0723sanchezandy@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','SEGURA','CASIQUE','OSBAL WIXON','01.03.25','MODALIDAD','48504149','1,450.00','OBRERO','948848228','Asoc de Viv Proyectos Ecológicos Los hijos de Santa Rosa  Mz. L  Lt. 17 – Puente Piedra - Lima','wilsonseguracasique@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','SOLSOL','TITO','SAMUEL','01.05.23','INDETER.','46700173','1,563.00','OBRERO','967883812','Jr. Pedro la Gasca 1147 el Carmen - Comas - Lima.','andrewsolsoltaza@gmail.com'],
            ['AVES BENEFICIADAS','OPERARIO DE PRODUCCIÓN','VILLALOBOS','GOMEZ','BENJAMIN NEIL','01.06.22','MODALIDAD','74482430','1,450.00','OBRERO','9344167369','A.H Nueva Canada Mz B Lt 03 - Puente Piedra - Lima','neil_villalobos_gomez2@outlook.com'],

            ['AVES BENEFICIADAS','LAVADOR DE BANDEJAS','TANCHIVA','SHAPIAMA','GERARDO HOLSEN','01.08.21','INDETER.','75312226','1,200.00','EMPLEADO','957198414','Asen Humano Juan Luis Cipriani Mz. F Lt. 4 - Puente Piedra - Lima','gerardoshapiama148@gmail.com'],

            ['AVES BENEFICIADAS','AUXILIAR DE LIMPIEZA','CACHIQUE','GARCIA','DELIA','01.08.24','MODALIDAD','44205049','1,313.00','OBRERO','902400863','Mz. B Lt. 7- 3er piso Asoc. de Viv Residencial Palmeras de  Copacabana – Puente Piedra - Lima','deliacachiquegarcia@gmail.com'],

            ['AVES BENEFICIADAS','OPERARIO DE CALDERO','MASCCO','AIQUIPA','CRISTHIAN ALFREDO','01.02.22','INDETER.','71389765','1,550.00','EMPLEADO','976835142','A.H La Alborada II Etapa Mz. 9 Lt. 5 Zapallal- Puente Piedra - Lima','cristianmascco1002@gmail.com'],

            ['AVES BENEFICIADAS','JEFE DE PRODUCCIÓN','CARRILLO','HUAMAN','MIGUEL PAUL','01.08.17','INDETER.','41485033','2,413.00','EMPLEADO','955269882','Jr. Cajamarca Mz. B Lt 1-A - Puente Piedra - Lima','flyblack.u@hotmail.com'],

            ['AVES VIVAS','FACTURADOR VIVO','ASTO','VEGA','RICARDO','01.07.10','INDETER.','40605914','1,800.00','EMPLEADO','964011031','Mz. A1 Lt. 15 Urb. Augusto Bedoya - Puente Piedra - Lima','ricardo.asto1980@gmail.com'],
            ['AVES BENEFICIADAS','FACTURADOR BENEFICIADO','QUIROZ','CHAVEZ','MIGUEL ANGEL','01.08.25','MODALIDAD','46098701','1,713.00','EMPLEADO','923558722','pasaje san jose mz 1 lt 17- puente piedra - lima','miguel_quirozch@hotmail.com'],

            ['AVES VIVAS','CH VIVO','CCORA','GOMEZ','ROGER','01.08.23','INDETER.','73417205','1,563.00','EMPLEADO','977515287','Mz. C1 Lt. 07 Asen. H Los Eucaliptos ETP II - Puente Piedra - Lima','escorpio94rl@gmail.com'],
            ['AVES VIVAS','CH VIVO','GERONIMO','MATENCIO','LUGER AVILES','01.01.25','INDETER.','42817031','1,450.00','EMPLEADO','991970413','Mz. B Lt. 8 Asoc. Viv LA GRAMA - Puente Piedra - Lima','lugeravilesgeronimomatencio@gmail.com'],
            ['AVES VIVAS','CH VIVO','HUAMANI','FLORES','MANUEL HENRY','01.04.25','MODALIDAD','73873439','1,450.00','EMPLEADO','932991740','Asoc. 15 de septiembre Mz. E Lt. 8 - Puente Piedra','henryhuamaniflores@gmail.com'],
            ['AVES VIVAS','CH VIVO','LAGOS','PINEDA','JAIME FAUSTO','01.07.22','INDETER.','10406527','1,563.00','EMPLEADO','925668242','Mz. O Lt. 22 Asen. H. Jaime Yoshiyama Sector 4 Pachacutec – Ventanilla – Ancon','jaimelagospineda8@gmail.com'],
            ['AVES VIVAS','CH VIVO','MURILLO','MARCOS','LUIS ALFREDO','01.03.10','INDETER.','07984557','1,450.00','EMPLEADO','987195852','Urb. Sto Domingo Mz. C Lt. 21 - Puente Piedra - Lima','luisalfredomurillomarcos@gmail.com'],
            ['AVES VIVAS','CH VIVO','RIVAS','VILLA','JUAN JESUS','01.07.22','INDETER.','41154811','1,613.00','EMPLEADO','924625761','Calle Cipreses Mz. J1 Lt. 41 Asoc. de Prop de la Alameda del Norte - Puente Piedra - Lima','juanjesusrivas@gmail.com'],

            ['AVES BENEFICIADAS','CH BENEF','ANTONIO','LAURA','EDINSON BRUS','01.11.23','INDETER.','47143644','1,563.00','EMPLEADO','957649878','Asent.H Ampl Hijos de Villa del Mar Mz. E Lt. 17 - Ventanilla - Callao','brusantoniolaura2022@gmail.com'],
            ['AVES BENEFICIADAS','CH BENEF','COTRINA','VARGAS','ROSMEL FREISIN','01.05.24','MODALIDAD','77022970','1,563.00','EMPLEADO','926449034','Mz. B Lt. 3 Asoc de Viv Magisterial - Puente Piedra - Lima','cotrinarosmel@gmail.com'],
            ['AVES BENEFICIADAS','CH BENEF','PISCOCHE','BERNARDO','FISER MORY','01.10.24','MODALIDAD','41416488','1,450.00','EMPLEADO','931597172','Coop. Copacabana Mz. C Lt. 8 - Puente Piedra - Lima','bernardofiser@gmail.com'],
            ['AVES BENEFICIADAS','CH BENEF','QUISPE','VERA','CRISTHIAN JESUS','01.06.20','INDETER.','47109147','1,563.00','EMPLEADO','974429263','Mz "J" lote 05 calle N° 08 con calle N° 06 - Quebrada del Norte Carabayllo','cristiavera1612@gmail.com'],
            ['AVES BENEFICIADAS','CH BENEF','HINOSTROZA','GONZALES','HENRY JHON','01.08.25','INDETER.','45283262','1,563.00','EMPLEADO','907843775','AA.HH LUIS FELIPE CASAS MZ R1 LT 18 -  VENTANILLA CALLAO','jhon_romantico_23@hotmail.com'],
            ['AVES BENEFICIADAS','CH BENEF','RODRIGUEZ','VELASQUEZ','ALIDO AGUSTIN','03.01.12','INDETER.','25817266','1,563.00','EMPLEADO','971085081','Av Carlos Mareategui Mz. A Lt 5 Asoc. Los Portales de Copacabana - Puente  Piedra – Lima','alido70rovrlasquez008@gmail.com'],

            ['COMERCIAL','INS.COBRANZA','CUEVA','MUCHA','JESUS JAVIER','01.04.24','MODALIDAD','10404778','1,313.00','EMPLEADO','982837098','Mz. F Lt. 1 A.H Mercado Central Pachacutec - Ventanilla - Callao','cuevamuchajesusjavier1969@gmail.com'],
            ['COMERCIAL','INS.COBRANZA','DEL CARPIO','MENA','HENRRY MARTIN','01.05.17','INDETER.','25582958','1,200.00','EMPLEADO','966451683','Calle 9 Mz 31 Lt 2 Satelite - Ventanilla - Callao','hdelcapio0807@gmail.com'],
            ['COMERCIAL','JEFE DE RESGUARDOS','GUERRA','NEIRA','ANDRES YSMAEL','01.04.14','INDETER.','09026182','1,600.00','EMPLEADO','946154447','Av Progreso N° 516 Pj San Gabriel -Villa Maria Del Triunfo - Lima','andresguerra3011@gmail.com'],
            ['COMERCIAL','RESGUARDO','MEDRANO','MUÑOZ','ANIBAL RAUL','01.03.19','INDETER.','31823365','1,713.00','EMPLEADO','965376472','Mz. 131 Lt. 19 AA.HH Enrique Milla Ochoa - Los Olivos - Lima','rmedrano1507@gmail.com'],
            ['COMERCIAL','INS.COBRANZA','ROLDAN','JAQUI','DAVID RUBEN','01.08.21','INDETER.','10383963','1,313.00','EMPLEADO','993634618','Jr. De la Mar Jose 142 Urb. Sta. Luz Mila - Comas - Lima','david.roldan.2015@gmail.com'],
            ['COMERCIAL','RESGUARDO','VILCHEZ','RODRIGUEZ','JULIO ELISEO','01.08.16','INDETER.','10748565','1,713.00','EMPLEADO','918976072','Las torres de Copacabana Mz.A Lt. 18 - Carabayllo - Lima','eliseovilchez1@gmai.com'],
            ['COMERCIAL','RESGUARDO','VILLOSLADA','TABARA','ROBERTO HUBER','01.08.16','INDETER.','09360528','1,600.00','EMPLEADO','930900648','Calle Zarumilla Mz. 26 Lt. 2 Asociación Popular Villamar de Ancon - Lima','yomioyodiosmioyo2708@gmail.com'],
            ['COMERCIAL','JEFE DE VENTAS','ABAD','ESPINOZA','JULIO ROMULO','01.03.10','INDETER.','45868748','2,613.00','EMPLEADO','977753897','Av. Jose Carlos Mareategui 315 Sub-block C1 Dpto. 201 – Puente Piedra - Lima','ajulio2624@gmail.com'],
            ['COMERCIAL','VENDEDOR','CACYA','CARBAJAL','ALFRED DAVID','01.07.14','INDETER.','47313156','1,913.00','EMPLEADO','929356613','Ampliación Nueva Estrella Mz E Lt 08 - Santa Rosa - Ancon','davidcacya2@gmail.com'],
            ['COMERCIAL','VENDEDOR','CARRILLO','SALAS','EDGAR','01.07.15','INDETER.','41997830','1,913.00','EMPLEADO','977754198','Jr. Carlos Lisson 220 - Comas - lima','edgarcarrillosalas2021@gmail.com'],
            ['COMERCIAL','VENDEDOR','GUERRA','NUÑEZ','PIERREE ALEXIS','01.03.18','INDETER.','42772915','1,913.00','EMPLEADO','924954016','Jr. Jose Olaya 318 -Comas - Lima','pierreeguerra33@gmail.com'],
            ['COMERCIAL','VENDEDOR','LEAÑO','CHINCHAYHUARA','THOMAS JEFFERSON','01.01.24','INDETER.','74467977','1,800.00','EMPLEADO','908859228','Mz. F Lt. 17 Urb. Virtgen de las Mercedes - Carabayllo - Lima','chinchayhuarat@gmail.com'],
            ['COMERCIAL','VENDEDOR','MENDOZA','TANTARICO','EDINSON','01.08.16','INDETER.','73439319','1,913.00','EMPLEADO','970314945','Av. Tahuantinsuyo Asent. H la Planicie de Ventanilla Mz. R Lt. 03 -  Ventanilla - Callao','mendozatantaricoedinson@gmail.com'],
            ['COMERCIAL','VENDEDOR','PAZ','ROJAS','CARLOS LIDOMIRO','01.05.21','INDETER.','23099116','1,913.00','EMPLEADO','919689435','Mz. A1 Lt. 8 Sector B3 Pachacutec - Ventanilla - Callao','pcarloslidomiro@gmail.com'],
            ['COMERCIAL','VENDEDOR','PIZARRO','BOLAÑOS','DORIS ELIZABETH','02.11.19','INDETER.','44502880','1,913.00','EMPLEADO','984128965','Mz. L5  Lt. 2 Los Licenciados - Ventanilla - Callao','eli_cn14@hotmail.com'],
            ['COMERCIAL','VENDEDOR','QUISPE','MUÑOZ','MARCO FABIAN','01.12.20','INDETER.','76108671','1,913.00','EMPLEADO','977754592','AAHH. Señor de los Milagros Mz. B Lt. 3 Pj. Garay - Puente Piedra - Lima','marcofabian0810@gmail.com'],
            ['COMERCIAL','VENDEDOR','RIOS','ARMAS','GEINER','01.05.17','INDETER.','42188798','1,913.00','EMPLEADO','955061085','Mz. K 4 Lt. 10 AA.HH 5 De Enero - Ventanilla - Callao','geinerrios9@hotmail.com'],

            ['ADMINISTRATIVO','VIGILANTE','SANDOVAL','VALDIVIESO','NILTON JOEL','01.10.16','INDETER.','47046984','1,477.00','EMPLEADO','973304048','Mz. A Lt. 5  Los Portales De Copacabana - Puenten Piedra  - Lima','niltonsandoval1991@gmail.com'],
            ['ADMINISTRATIVO','VIGILANTE','ASENCIOS','BENITES','NAIVE GODOY','01.08.17','INDETER.','15732330','1,130.00','EMPLEADO','984213754','Calle Avelino Cáceres Mz. 126 Lt. 13 - Asoc. Popular Lomas de Ancon – Lima','naiveasencios@gmail.com'],

            ['MANTENIMIENTO','JEFE DE MANTENIMIENTO','BRAVO','GAMBOA','JOHNNY JOFRED','01.03.19','INDETER.','44326546','2,513.00','EMPLEADO','997518877','Asen. H Los Proceres Mz. F2 Lt. 3 Payet - Independencia - Lima','johnny_bravotj@hotmail.com'],
            ['MANTENIMIENTO','TECNICO','CARRASCO','LARA','PAUL','16.03.19','INDETER.','47860611','1,613.00','EMPLEADO','910587071','A.H. C.M Cueto Fernandini Mz. L1 Lt. 13 - Los Olivos','pau.carrasco.l@hotmail.com'],
            ['MANTENIMIENTO','TECNICO','SANCHEZ','TAGLE','FERNANDO ROGER','01.02.25','MODALIDAD','48104626','1,500.00','EMPLEADO','916225420','A.H Alfonso Ugarte Ventanilla Mz. E Lt. 1 - Callao','801124@senati.pe'],

            ['ADMINISTRATIVO','ADMINISTRADOR','CARRILLO','VICENTE','EVA LUISA','01.12.08','INDETER.','08531541','3,613.00','EMPLEADO','981058520','Calle V Mz. G2 Lt. 13 Ubr. Santo Domingo 6ta Etapa - Carabayllo - Lima','eva.carrillo@outlook.com'],
            ['ADMINISTRATIVO','TITULAR GERENTE','CARRILLO','CARRASCO','JOMAYRA MADELAINE','02.01.14','INDETER.','46146107','4,613.00','EMPLEADO','946040007','Mz. A Lt. 25 Asociación los Alagarrobos - Puente Pidra - Lima','jomayra.madelaine@gmail.com'],
            ['AVES BENEFICIADAS','ENC. CONTROL DE CALIDAD','CARRILLO','CARRASCO','SERGIO MIGUEL','01.12.20','INDETER.','75325563','1,130.00','EMPLEADO','977930436','Jr. Pórvenir Mz.A Lt. 29 - Puente Piedra - Lima','sergiocc01@hotmail.com'],

            ['ADMINISTRATIVO','ASIS. DE GERENCIA','CARRILLO','CARRASCO','LUIS ANGEL','01.08.21','INDETER.','75327023','1,130.00','EMPLEADO','977754485','Jr. Porvenir Mz. A Lt. 25 - Puente Piedra - Lima','luiscc08@hotmail.com'],
            ['ADMINISTRATIVO','LOGISTICA/ENC. TRANS','CARRILLO','HUAMAN','DIANA MEDALIT','01.07.19','INDETER.','43721512','1,913.00','EMPLEADO','963310622','Mz. C Lt 21 Calle  Jorge Chavez - Puente Piedra - Lima','dmedalit.carrillo@gmail.com'],
            ['ADMINISTRATIVO','ENCARGADA CONTABLE','JACINTO','BLAS','CINDY RUBI','01.02.18','INDETER.','47931374','2,500.00','EMPLEADO','926861842','Mz. Ñ1 Lt. 27 Urb. Lomas de Zapallal - Puente Piedra - Lima','cindyrubi.jb@gmail.com'],
            ['ADMINISTRATIVO','JEFA DE RRHH','TAZA','VIVAR','BEATRIZ KAREN','01.12.16','INDETER.','45841461','2,413.00','EMPLEADO','975435372','Asoc. Las Begonias del norte Mz. E Lt. 5 - Puente Piedra','tazabeatriz@gmail.com'],
        ];

        DB::transaction(function () use ($rows) {
            foreach ($rows as $r) {
                [$areaName,$cargoName,$apPat,$apMat,$nombres,$fIngreso,$contrato,$dni,$sueldo,$tipoTrab,$cel,$dir,$email] = $r;

                $area  = Area::firstOrCreate(['nombre' => trim($areaName)]);
                $cargo = Cargo::firstOrCreate(['nombre' => trim($cargoName)]);

                Empleado::updateOrCreate(
                    ['dni' => trim($dni)],
                    [
                        'apellidos'        => trim($apPat.' '.$apMat),
                        'nombres'          => trim($nombres),
                        'fecha_ingreso'    => self::toDate($fIngreso),
                        'direccion'        => $dir ? trim($dir) : null,
                        'celular'          => $cel ? trim($cel) : null,
                        'email'            => $email ?: null,
                        'estado'           => 'ACTIVO',
                        'tipo_trabajador'  => trim($tipoTrab),         // OBRERO | EMPLEADO
                        'sueldo'           => self::money($sueldo),    // total (básico + asig. si aplica)
                        'area_id'          => $area->id,
                        'cargo_id'         => $cargo->id,
                    ]
                );
            }
        });
    }

    private static function toDate(?string $d): ?string
    {
        if (!$d) return null;
        $d = trim($d);
        $d = str_replace('/', '.', $d);
        $parts = explode('.', $d);
        if (count($parts) < 3) return null;
        [$dd,$mm,$yy] = $parts;

        if (strlen($yy) === 2) {
            $yy = (int) $yy;
            $yy = $yy >= 70 ? (1900 + $yy) : (2000 + $yy);
        }
        $dd = str_pad((int)$dd, 2, '0', STR_PAD_LEFT);
        $mm = str_pad((int)$mm, 2, '0', STR_PAD_LEFT);
        return "{$yy}-{$mm}-{$dd}";
    }

    private static function money(?string $m): ?float
    {
        if ($m === null) return null;
        $m = str_replace([' ', ','], ['', ''], $m);
        return (float) $m;
    }
}
