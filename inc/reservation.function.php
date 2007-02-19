<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2006 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------

// device_type
// 1 computers
// 2 networking
// 3 printers
// 4 monitors
// 5 peripherals
// 6 


// FUNCTIONS Reservation

if (!defined('GLPI_ROOT')){
	die("Sorry. You can't access directly to this file");
	}


function titleReservation(){
	global  $LANG,$CFG_GLPI;

	displayTitle($CFG_GLPI["root_doc"]."/pics/reservation.png",$LANG["reservation"][1],$LANG["reservation"][1],array("reservation.php?show=resa&amp;ID"=>$LANG["reservation"][26]));

}



function showReservationForm($device_type,$id_device){

	global $CFG_GLPI,$LANG;

	if (!haveRight("reservation_central","w")) return false;


	if ($resaID=isReservable($device_type,$id_device)) {
		// Supprimer le materiel
		echo "<br><div><a href=\"javascript:confirmAction('".addslashes($LANG["reservation"][38])."\\n".addslashes($LANG["reservation"][39])."','".$CFG_GLPI["root_doc"]."/front/reservation.php?ID=".$resaID."&amp;delete=delete')\" class='icon_consol'>".$LANG["reservation"][6]."</a></div>";	

	}else {
		echo "<br><div><a href=\"".$CFG_GLPI["root_doc"]."/front/reservation.php?";
		echo "id_device=$id_device&amp;device_type=$device_type&amp;comments=&amp;add=add\" class='icon_consol' >".$LANG["reservation"][7]."</a></div>";      
	}
}

function printCalendrier($target,$ID=""){
	global $LANG, $CFG_GLPI;

	if (!haveRight("reservation_helpdesk","1")) return false;

	if (!isset($_GET["mois_courant"]))
		$mois_courant=strftime("%m");
	else $mois_courant=$_GET["mois_courant"];
	if (!isset($_GET["annee_courante"]))
		$annee_courante=strftime("%Y");
	else $annee_courante=$_GET["annee_courante"];

	$mois_suivant=$mois_courant+1;
	$mois_precedent=$mois_courant-1;
	$annee_suivante=$annee_courante;
	$annee_precedente=$annee_courante;
	if ($mois_precedent==0){
		$mois_precedent=12;
		$annee_precedente--;
	}

	if ($mois_suivant==13){
		$mois_suivant=1;
		$annee_suivante++;
	}

	$str_suivant="?show=resa&amp;ID=$ID&amp;mois_courant=$mois_suivant&amp;annee_courante=$annee_suivante";
	$str_precedent="?show=resa&amp;ID=$ID&amp;mois_courant=$mois_precedent&amp;annee_courante=$annee_precedente";


	if (!empty($ID)){
		$m=new ReservationItem;
		$m->getfromDB($ID);
		$ci=new CommonItem();
		$ci->getFromDB($m->fields["device_type"],$m->fields["id_device"]);
		$type=$ci->getType();
		$name=$ci->getName();
		$all="<a href='$target?show=resa&amp;ID=&amp;mois_courant=$mois_courant&amp;annee_courante=$annee_courante'>".$LANG["reservation"][26]."</a>";
	} else {
		$type="";
		$name=$LANG["reservation"][25];
		$all="&nbsp;";
	}


	echo "<div align='center'><table border='0'><tr><td>";
	echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/reservation.png\" alt='' title=''></td><td><b><span class='icon_consol'>".$type." - ".$name."</span>";
	echo "</b></td></tr><tr><td colspan='2' align ='center'>$all</td></tr></table></div>";



	// Check bisextile years
	if (($annee_courante%4)==0) $fev=29; else $fev=28;
	$nb_jour= array(31,$fev,31,30,31,30,31,31,30,31,30,31);

	// Datas used to put right informations in columns
	$jour_debut_mois=strftime("%w",mktime(0,0,0,$mois_courant,1,$annee_courante));
	if ($jour_debut_mois==0) $jour_debut_mois=7;
	$jour_fin_mois=strftime("%w",mktime(0,0,0,$mois_courant,$nb_jour[$mois_courant-1],$annee_courante));

	echo "<div align='center'>";

	echo "<table cellpadding='20' ><tr><td><a href=\"".$target.$str_precedent."\"><img src=\"".$CFG_GLPI["root_doc"]."/pics/left.png\" alt='".$LANG["buttons"][12]."' title='".$LANG["buttons"][12]."'></a></td><td><b>".
		$LANG["calendarM"][$mois_courant-1]."&nbsp;".$annee_courante."</b></td><td><a href=\"".$target.$str_suivant."\"><img src=\"".$CFG_GLPI["root_doc"]."/pics/right.png\" alt='".$LANG["buttons"][11]."' title='".$LANG["buttons"][11]."'></a></td></tr></table>";
	// test
	echo "<table width='90%'><tr><td valign='top'  width='100'>";

	echo "<table><tr><td width='100' valign='top'>";

	// today date
	$today=getdate(time());
	$mois=$today["mon"];
	$annee=$today["year"];


	$annee_avant = $annee_courante - 1;
	$annee_apres = $annee_courante + 1;


	echo "<div class='verdana1'>";
	echo "<div style='text-align:center'><b>$annee_avant</b></div>";
	for ($i=$mois_courant; $i < 13; $i++) {
		echo "<div style='margin-left: 10px; padding: 2px; -moz-border-radius: 5px; margin-top: 2px; border: 1px solid #cccccc; background-color: #eeeeee;'><a href=\"".$target."?show=resa&amp;ID=$ID&amp;mois_courant=$i&amp;annee_courante=$annee_avant\">".
			$LANG["calendarM"][$i-1]."</a></div>";
	}

	echo "<div style='text-align:center'><b>$annee_courante</b></div>";
	for ($i=1; $i < 13; $i++) {
		if ($i == $mois_courant) {
			echo "<div style='margin-left: 10px; padding: 2px; -moz-border-radius: 5px; margin-top: 2px; border: 1px solid #666666; background-color: white;'><b>".
				$LANG["calendarM"][$i-1]."</b></div>";
		}
		else {
			echo "<div style='margin-left: 10px; padding: 2px; -moz-border-radius: 5px; margin-top: 2px; border: 1px solid #cccccc; background-color: #eeeeee;'><a href=\"".$target."?show=resa&amp;ID=$ID&amp;mois_courant=$i&amp;annee_courante=$annee_courante\">".
				$LANG["calendarM"][$i-1]."</a></div>";
		}
	}

	echo "<div style='text-align:center'><b>$annee_apres</b></div>";
	for ($i=1; $i < $mois_courant+1; $i++) {
		echo "<div style='margin-left: 10px; padding: 2px; -moz-border-radius: 5px; margin-top: 2px; border: 1px solid #cccccc; background-color: #eeeeee;'><a href=\"".$target."?show=resa&amp;ID=$ID&amp;mois_courant=$i&amp;annee_courante=$annee_apres\">".
			$LANG["calendarM"][$i-1]."</a></div>";
	}
	echo "</div>";

	echo "</td></tr></table>";

	echo "</td><td valign='top' width='100%'>";



	// test 


	echo "<table class='tab_cadre' width='100%'><tr>";
	echo "<th width='14%'>".$LANG["calendarD"][1]."</th>";
	echo "<th width='14%'>".$LANG["calendarD"][2]."</th>";
	echo "<th width='14%'>".$LANG["calendarD"][3]."</th>";
	echo "<th width='14%'>".$LANG["calendarD"][4]."</th>";
	echo "<th width='14%'>".$LANG["calendarD"][5]."</th>";
	echo "<th width='14%'>".$LANG["calendarD"][6]."</th>";
	echo "<th width='14%'>".$LANG["calendarD"][0]."</th>";
	echo "</tr>";
	echo "<tr class='tab_bg_3' >";

	// Insert blank cell before the first day of the month
	for ($i=1;$i<$jour_debut_mois;$i++)
		echo "<td style='background-color:#ffffff'>&nbsp;</td>";

	// voici le remplissage proprement dit
	if ($mois_courant<10&&strlen($mois_courant)==1) $mois_courant="0".$mois_courant;

	for ($i=1;$i<$nb_jour[$mois_courant-1]+1;$i++){
		if ($i<10) $ii="0".$i;
		else $ii=$i;

		echo "<td  valign='top' height='100'>";

		echo "<table align='center' ><tr><td align='center' ><span style='font-family: arial,helvetica,sans-serif; font-size: 14px; color: black'>".$i."</span></td></tr>";

		if (!empty($ID)){
			echo "<tr><td align='center'><a href=\"".$target."?show=resa&amp;add_item[$ID]=$ID&amp;date=".$annee_courante."-".$mois_courant."-".$ii."\"><img style='color: blue; font-family: Arial, Sans, sans-serif; font-size: 10px;' src=\"".$CFG_GLPI["root_doc"]."/pics/addresa.png\" alt='".$LANG["reservation"][8]."' title='".$LANG["reservation"][8]."'></a></td></tr>";
		}
		//if (($i-1+$jour_debut_mois)%7!=6&&($i-1+$jour_debut_mois)%7!=0){
		echo "<tr><td>";
		printReservation($target,$ID,$annee_courante."-".$mois_courant."-".$ii);
		echo "</td></tr>";
		//}
		//	echo $annee_courante."-".$mois_courant."-".$ii;
		//if (($i-1+$jour_debut_mois)%7!=6&&($i-1+$jour_debut_mois)%7!=0)




		echo "</table>";
		echo "</td>";

		// il ne faut pas oubli�d'aller �la ligne suivante enfin de semaine
		if (($i+$jour_debut_mois)%7==1)
		{echo "</tr>";
			if ($i!=$nb_jour[$mois_courant-1])echo "<tr class='tab_bg_3'>";
		}
	}

	// on recommence pour finir le tableau proprement pour les m�es raisons

	if ($jour_fin_mois!=0)
		for ($i=0;$i<7-$jour_fin_mois;$i++) 	echo "<td style='background-color:#ffffff'>&nbsp;</td>";

	echo "</tr></table>";

	echo "</td></tr></table></div>";

}

function showAddReservationForm($target,$items,$date,$resaID=-1){
	global $LANG;

	if (!haveRight("reservation_helpdesk","1")) return false;
	if (count($items)==0) return false;

	$resa= new ReservationResa;

	if ($resaID!=-1){
		if (!$resa->getFromDB($resaID)){
			return false;
		}
		if (!haveRight("reservation_central","w")&&$resa->fields['id_user']!=$_SESSION['glpiID']) {
			return false;
		}

	} else {
		$resa->getEmpty();
		$resa->fields["begin"]=$date." 12:00:00";
		$resa->fields["end"]=$date." 13:00:00";
	}
	$begin=strtotime($resa->fields["begin"]);
	$end=strtotime($resa->fields["end"]);
	$begin_date=date("Y-m-d",$begin);
	$end_date=date("Y-m-d",$end);
	$begin_hour=date("H:i",$begin);
	$end_hour=date("H:i",$end);

	echo "<div align='center'><form method='post' name=form action=\"$target\">";

	if ($resaID!=-1)
		echo "<input type='hidden' name='ID' value='$resaID'>";

	echo "<table class='tab_cadre' cellpadding='2'>";
	echo "<tr><th colspan='2'><b>";
	echo $LANG["reservation"][9];
	echo "</b></th></tr>";

	// Add Hardware name
	$r=new ReservationItem;
	$ci=new CommonItem();

	echo "<tr class='tab_bg_1'><td>".$LANG["reservation"][4].":	</td>";
	echo "<td>";
	foreach ($items as $ID){
		$r->getfromDB($ID);
		$ci->getFromDB($r->fields["device_type"],$r->fields["id_device"]);
		echo "<b>".$ci->getType()." - ".$ci->getName()."</b><br>";
		echo "<input type='hidden' name='items[$ID]' value='$ID'>";
	}
	echo "</td></tr>";
	if (!haveRight("reservation_central","w"))
		echo "<input type='hidden' name='id_user' value='".$_SESSION["glpiID"]."'>";
	else {
		echo "<tr class='tab_bg_2'><td>".$LANG["reservation"][31].":	</td>";
		echo "<td>";
		if ($resaID==-1)
			dropdownAllUsers("id_user",$_SESSION["glpiID"],1,$ci->getField('FK_entities'));
		else dropdownAllUsers("id_user",$resa->fields["id_user"],1,$ci->getField('FK_entities'));
		echo "</td></tr>";

	}


	echo "<tr class='tab_bg_2'><td>".$LANG["search"][8].":	</td><td>";
	showCalendarForm("form","begin_date",$begin_date);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td>".$LANG["reservation"][12].":	</td>";
	echo "<td>";

	dropdownHours("begin_hour",$begin_hour,1);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td>".$LANG["search"][9].":	</td><td>";
	showCalendarForm("form","end_date",$end_date);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td>".$LANG["reservation"][13].":	</td>";
	echo "<td>";
	dropdownHours("end_hour",$end_hour,1);
	echo "</td></tr>";

	if ($resaID==-1){
		echo "<tr class='tab_bg_2'><td>".$LANG["reservation"][27].":	</td>";
		echo "<td>";
		echo "<select name='periodicity'>";
		echo "<option value='day'>".$LANG["reservation"][29]."</option>";	
		echo "<option value='week'>".$LANG["reservation"][28]."</option>";		
		echo "</select>";
		dropdownInteger('periodicity_times',1,1,60);	
		echo $LANG["reservation"][30];
		echo "</td></tr>";
	}

	echo "<tr class='tab_bg_2'><td>".$LANG["common"][25].":	</td>";
	echo "<td><textarea name='comment'rows='8' cols='30'>".$resa->fields["comment"]."</textarea>";
	echo "</td></tr>";

	if ($resaID==-1){
		echo "<tr class='tab_bg_2'>";
		echo "<td colspan='2'  valign='top' align='center'>";
		echo "<input type='submit' name='add_resa' value=\"".$LANG["buttons"][8]."\" class='submit'>";
		echo "</td></tr>\n";
	} else {
		echo "<tr class='tab_bg_2'>";
		echo "<td valign='top' align='center'>";
		echo "<input type='submit' name='clear_resa' value=\"".$LANG["buttons"][6]."\" class='submit'>";
		echo "</td><td valign='top' align='center'>";
		echo "<input type='submit' name='edit_resa' value=\"".$LANG["buttons"][14]."\" class='submit'>";
		echo "</td></tr>\n";
	}

	echo "</table>";
	echo "</form></div>";
}

function printReservation($target,$ID,$date){
	global $DB;
	if (!empty($ID))
		printReservationItem($target,$ID,$date);
	else  {

		$debut=$date." 00:00:00";
		$fin=$date." 23:59:59";

		$query = "SELECT DISTINCT glpi_reservation_item.ID FROM glpi_reservation_item INNER JOIN glpi_reservation_resa ON (glpi_reservation_item.ID = glpi_reservation_resa.id_item )".
			" WHERE (('".$debut."' < begin AND '".$fin."' > begin) OR ('".$debut."' < end AND '".$fin."' > end) OR (begin < '".$debut."' AND end > '".$debut."') OR (begin < '".$fin."' AND end > '".$fin."')) ORDER BY begin";
		//echo $query;
		$result=$DB->query($query);

		if ($DB->numrows($result)>0){
			$m=new ReservationItem;

			while ($data=$DB->fetch_array($result)){

				$m->getfromDB($data['ID']);
				$ci=new CommonItem();
				$ci->getFromDB($m->fields["device_type"],$m->fields["id_device"]);

				list($annee,$mois,$jour)=split("-",$date);
				echo "<tr class='tab_bg_1'><td><a href='$target?show=resa&amp;ID=".$data['ID']."&amp;mois_courant=$mois&amp;annee_courante=$annee'>".$ci->getType()." - ".$ci->getName()."</a></td></tr>";
				echo "<tr><td>";
				printReservationItem($target,$data['ID'],$date);
				echo "</td></tr>";
			}
		}
	}

}


function printReservationItem($target,$ID,$date){
	global $DB,$LANG;

	$id_user=$_SESSION["glpiID"];

	$user=new User;
	list($year,$month,$day)=split("-",$date);
	$debut=$date." 00:00:00";
	$fin=$date." 23:59:59";
	$query = "SELECT * FROM glpi_reservation_resa".
		" WHERE (('".$debut."' < begin AND '".$fin."' > begin) OR ('".$debut."' < end AND '".$fin."' > end) OR (begin < '".$debut."' AND end > '".$debut."') OR (begin < '".$fin."' AND end > '".$fin."')) AND id_item=$ID ORDER BY begin";
	//		echo $query."<br>";
	if ($result=$DB->query($query)){
		if ($DB->numrows($result)>0){
			echo "<table width='100%' >";
			while ($row=$DB->fetch_array($result)){
				echo "<tr>";
				$user->getfromDB($row["id_user"]);
				$display="";					
				if ($debut>$row['begin']) $heure_debut="00:00";
				else $heure_debut=get_hour_from_sql($row['begin']);

				if ($fin<$row['end']) $heure_fin="24:00";
				else $heure_fin=get_hour_from_sql($row['end']);

				if (strcmp($heure_debut,"00:00")==0&&strcmp($heure_fin,"24:00")==0)
					$display=$LANG["reservation"][15];
				else if (strcmp($heure_debut,"00:00")==0) 
					$display=$LANG["reservation"][16]."&nbsp;".$heure_fin;
				else if (strcmp($heure_fin,"24:00")==0) 
					$display=$LANG["reservation"][17]."&nbsp;".$heure_debut;
				else $display=$heure_debut."-".$heure_fin;

				$rand=mt_rand();		
				$modif=$modif_end="";
				if (haveRight("reservation_central","w")||$row['id_user']==$_SESSION['glpiID']) {
					$modif="<a onmouseout=\"cleanhide('content_".$ID.$rand."')\" onmouseover=\"cleandisplay('content_".$ID.$rand."')\" href=\"".$target."?show=resa&amp;edit=".$row['ID']."&amp;edit_item[$ID]=$ID&amp;mois_courant=$month&amp;annee_courante=$year\">";
					$modif_end="</a>";
				}
				$comment="<div class='over_link' id='content_".$ID.$rand."'>".nl2br($row["comment"])."</div>";

				echo "<td   align='center' class='tab_resa'>". $modif."<span>".$display."<br><b>".$user->fields["name"]."</b></span>";

				echo $modif_end.$comment."</td></tr>";

			}

			echo "</table>";
		}
	}

}


function printReservationItems($target){
	global $DB,$LANG;

	if (!haveRight("reservation_helpdesk","1")) return false;

	$ri=new ReservationItem;
	$ci=new CommonItem();


	$query="select ID from glpi_reservation_item ORDER BY device_type";

	if ($result = $DB->query($query)) {
		
		echo "<div align='center'><form name='form' method='get' action='$target'><table class='tab_cadre' cellpadding='5'>";
		echo "<tr><th colspan='4'>".$LANG["reservation"][1]."</th></tr>";
		while ($row=$DB->fetch_array($result)){
			$ri->getfromDB($row['ID']);
			$ci->getFromDB($ri->fields["device_type"],$ri->fields["id_device"]);

			if ($ci->getField('deleted')=='N'){
				echo "<tr class='tab_bg_2'>";
				echo "<td><input type='checkbox' name='add_item[".$row["ID"]."]' value='".$row["ID"]."' ></td>";
				echo "<td><a href='".$target."?show=resa&amp;ID=".$row['ID']."'>".$ci->getType()." - ".$ci->getName()."</a></td>";
				echo "<td>".getDropdownName('glpi_dropdown_locations',$ci->getField('location'))."</td>";
				echo "<td>".nl2br($ri->fields["comments"])."</td>";
				echo "</tr>";
			}
		}
		echo "<tr class='tab_bg_1' align='center'><td colspan='4'><input type='submit' value=\"".$LANG["buttons"][8]."\" class='submit' ></td></tr>";
		echo "</table>";
		echo "<input type='hidden' name='show' value='resa'>";
		echo "</form></div>";

	}
}


function showReservationCommentForm($target,$ID){
	global $LANG;

	if (!haveRight("reservation_central","w")) return false;

	$r=new ReservationItem;
	if ($r->getfromDB($ID)){
		$ci=new CommonItem();
		$ci->getFromDB($r->fields["device_type"],$r->fields["id_device"]);

		echo "<div align='center'><form method='post' name=form action=\"$target\">";
		echo "<input type='hidden' name='ID' value='$ID'>";

		echo "<table class='tab_cadre' cellpadding='2'>";
		echo "<tr><th colspan='2'><b>";
		echo $LANG["reservation"][22];
		echo "</b></th></tr>";
		// Ajouter le nom du mat�iel
		echo "<tr class='tab_bg_1'><td>".$LANG["reservation"][4].":	</td>";
		echo "<td>";
		echo "<b>".$ci->getType()." - ".$ci->getName()."</b>";
		echo "</td></tr>";

		echo "<tr class='tab_bg_1'><td>".$LANG["common"][25].":	</td>";
		echo "<td>";
		echo "<textarea name='comments' cols='30' rows='10' >".$r->fields["comments"]."</textarea>";
		echo "</td></tr>";


		echo "<tr class='tab_bg_2'>";
		echo "<td colspan='2'  valign='top' align='center'>";
		echo "<input type='submit' name='updatecomment' value=\"".$LANG["buttons"][14]."\" class='submit'>";
		echo "</td></tr>\n";

		echo "</table>";
		echo "</form></div>";
		return true;
	} else return false;
}

function showDeviceReservations($target,$type,$ID){
	global $DB,$LANG,$CFG_GLPI;
	$resaID=0;

	if (!haveRight("reservation_central","r")) return false;

	echo "<div align='center'>";

	showReservationForm($type,$ID);
	echo "<br>";

	if ($resaID=isReservable($type,$ID)){

		$now=$_SESSION["glpi_currenttime"];
		// Print reservation in progress
		$query = "SELECT * FROM glpi_reservation_resa WHERE end > '".$now."' AND id_item='$resaID' ORDER BY begin";
		$result=$DB->query($query);

		echo "<table class='tab_cadrehov'><tr><th colspan='5'><a href='".$CFG_GLPI["root_doc"]."/front/reservation.php?show=resa&ID=$resaID' >".$LANG["reservation"][35]."</a></th></tr>";
		if ($DB->numrows($result)==0){	
			echo "<tr class='tab_bg_2'><td align='center' colspan='5'>".$LANG["reservation"][37]."</td></tr>";
		} else {
			echo "<tr><th>".$LANG["search"][8]."</th><th>".$LANG["search"][9]."</th><th>".$LANG["reservation"][31]."</th><th>".$LANG["common"][25]."</th><th>&nbsp;</th></tr>";
			while ($data=$DB->fetch_assoc($result)){
				echo "<tr class='tab_bg_2'>";
				echo "<td align='center'>".convDateTime($data["begin"])."</td>";
				echo "<td align='center'>".convDateTime($data["end"])."</td>";
				echo "<td align='center'><a  href='".$CFG_GLPI["root_doc"]."/front/user.form.php?ID=".$data["id_user"]."'>".getUserName($data["id_user"])."</a></td>";
				echo "<td align='center'>".nl2br($data["comment"])."</td>";
				echo "<td align='center'>";
				
				list($annee,$mois,$jour)=split("-",$data["begin"]);
				echo "<a  href='".$CFG_GLPI["root_doc"]."/front/reservation.php?show=resa&amp;ID=".$resaID."&amp;mois_courant=$mois&amp;annee_courante=$annee' title='".$LANG["reservation"][21]."'><img src=\"".$CFG_GLPI["root_doc"]."/pics/reservation-3.png\" alt='' title=''></a>";
				
				
				echo "</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
		echo "<br>";
		// Print old reservations

		$query = "SELECT * FROM glpi_reservation_resa WHERE end <= '".$now."' AND id_item='$resaID' ORDER BY begin DESC";
		$result=$DB->query($query);

		echo "<table class='tab_cadrehov'><tr><th colspan='5'><a href='".$CFG_GLPI["root_doc"]."/front/reservation.php?show=resa&ID=$resaID' >".$LANG["reservation"][36]."</a></th></tr>";
		if ($DB->numrows($result)==0){	
			echo "<tr class='tab_bg_2'><td align='center' colspan='5'>".$LANG["reservation"][37]."</td></tr>";
		} else {
			echo "<tr><th>".$LANG["search"][8]."</th><th>".$LANG["search"][9]."</th><th>".$LANG["reservation"][31]."</th><th>".$LANG["common"][25]."</th><th>&nbsp;</th></tr>";
			while ($data=$DB->fetch_assoc($result)){
				echo "<tr class='tab_bg_2'>";
				echo "<td align='center'>".convDateTime($data["begin"])."</td>";
				echo "<td align='center'>".convDateTime($data["end"])."</td>";
				echo "<td align='center'><a  href='".$CFG_GLPI["root_doc"]."/front/user.form.php?ID=".$data["id_user"]."'>".getUserName($data["id_user"])."</a></td>";
				echo "<td align='center'>".nl2br($data["comment"])."</td>";
				echo "<td align='center'>";
				
				list($annee,$mois,$jour)=split("-",$data["begin"]);
				echo "<a  href='".$CFG_GLPI["root_doc"]."/front/reservation.php?show=resa&amp;ID=".$resaID."&amp;mois_courant=$mois&amp;annee_courante=$annee' title='".$LANG["reservation"][21]."'><img src=\"".$CFG_GLPI["root_doc"]."/pics/reservation-3.png\" alt='' title=''></a>";
				
				echo "</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
		echo "<br>";


	} else echo "<strong>".$LANG["reservation"][34]."</strong>";
	echo "</div>";

}

function showUserReservations($target,$ID){
	global $DB,$LANG,$CFG_GLPI;
	$resaID=0;

	if (!haveRight("reservation_central","r")) return false;

	echo "<div align='center'>";

	$now=$_SESSION["glpi_currenttime"];

	// Print reservation in progress
	$query = "SELECT * FROM glpi_reservation_resa WHERE end > '".$now."' AND id_user='$ID' ORDER BY begin";
	$result=$DB->query($query);
	$ri=new ReservationItem();
	echo "<table class='tab_cadrehov'><tr><th colspan='6'>".$LANG["reservation"][35]."</th></tr>";
	if ($DB->numrows($result)==0){	
		echo "<tr class='tab_bg_2'><td align='center' colspan='6'>".$LANG["reservation"][37]."</td></tr>";
	} else {
		echo "<tr><th>".$LANG["search"][8]."</th><th>".$LANG["search"][9]."</th><th>".$LANG["common"][1]."</th><th>".$LANG["reservation"][31]."</th><th>".$LANG["common"][25]."</th><th>&nbsp;</th></tr>";

		while ($data=$DB->fetch_assoc($result)){
			echo "<tr class='tab_bg_2'>";
			echo "<td align='center'>".convDateTime($data["begin"])."</td>";
			echo "<td align='center'>".convDateTime($data["end"])."</td>";
			if ($ri->getFromDB($data["id_item"]))
				echo "<td align='center'>".$ri->getLink()."</td>";
			else echo "<td align='center'>&nbsp;</td>";
			echo "<td align='center'>".getUserName($data["id_user"])."</td>";
			echo "<td align='center'>".nl2br($data["comment"])."</td>";
			echo "<td align='center'>";
				
				list($annee,$mois,$jour)=split("-",$data["begin"]);
				echo "<a  href='".$CFG_GLPI["root_doc"]."/front/reservation.php?show=resa&amp;ID=".$data["id_item"]."&amp;mois_courant=$mois&amp;annee_courante=$annee' title='".$LANG["reservation"][21]."'><img src=\"".$CFG_GLPI["root_doc"]."/pics/reservation-3.png\" alt='' title=''></a>";
				
				echo "</td>";
			echo "</tr>";
		}
	}
	echo "</table>";
	echo "<br>";
	// Print old reservations

	$query = "SELECT * FROM glpi_reservation_resa WHERE end <= '".$now."' AND id_user='$ID' ORDER BY begin DESC";
	$result=$DB->query($query);

	echo "<table class='tab_cadrehov'><tr><th colspan='6'>".$LANG["reservation"][36]."</th></tr>";
	if ($DB->numrows($result)==0){	
		echo "<tr class='tab_bg_2'><td align='center' colspan='6'>".$LANG["reservation"][37]."</td></tr>";
	} else {
		echo "<tr><th>".$LANG["search"][8]."</th><th>".$LANG["search"][9]."</th><th>".$LANG["common"][1]."</th><th>".$LANG["reservation"][31]."</th><th>".$LANG["common"][25]."</th><th>&nbsp;</th></tr>";
		while ($data=$DB->fetch_assoc($result)){
			echo "<tr class='tab_bg_2'>";
			echo "<td align='center'>".convDateTime($data["begin"])."</td>";
			echo "<td align='center'>".convDateTime($data["end"])."</td>";
			if ($ri->getFromDB($data["id_item"]))
				echo "<td align='center'>".$ri->getLink()."</td>";
			else echo "<td align='center'>&nbsp;</td>";
			echo "<td align='center'>".getUserName($data["id_user"])."</td>";
			echo "<td align='center'>".nl2br($data["comment"])."</td>";
			echo "<td align='center'>";
				
				list($annee,$mois,$jour)=split("-",$data["begin"]);
				echo "<a  href='".$CFG_GLPI["root_doc"]."/front/reservation.php?show=resa&amp;ID=".$data["id_item"]."&amp;mois_courant=$mois&amp;annee_courante=$annee' title='".$LANG["reservation"][21]."'><img src=\"".$CFG_GLPI["root_doc"]."/pics/reservation-3.png\" alt='' title=''></a>";
				
				echo "</td>";
			echo "</tr>";
		}
	}
	echo "</table>";
	echo "<br>";

	echo "</div>";

}

function isReservable($type,$ID){

	global $DB;
	$query="SELECT ID FROM glpi_reservation_item WHERE device_type='$type' AND id_device='$ID'";
	$result=$DB->query($query);
	if ($DB->numrows($result)==0){
		return false;
	} else return $DB->result($result,0,0);
}

?>
