<?php
/*
phpIMAPArchiver archives message from INBO to another mailbox
Copyright (C) 2017  Nena Furtmeier

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//CHANGE SETTINGS HERE
$server = "localhost:143";
$user = "test@test.de";
$password = "123456";

$from = "INBOX";
$to = "INBOX.Archiv.{Y}";

$olderThan = "-6 months"; //strtotime format
//END SETTINGS


if(function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Berlin');

$server = "{".$server."/novalidate-cert}";
$olderThan = strtotime($olderThan);
$mbox = imap_open($server.$from, $user, $password);
imap_expunge($mbox);

$folders = imap_list($mbox, $server, "*");
foreach($folders AS $k => $folder)
	$folders[$k] = str_replace($server, "", $folder);

$UIDs = imap_search($mbox, "BEFORE ".date("d-M-Y", $olderThan), SE_UID);

if($UIDs === false)
	exit();

foreach($UIDs AS $UID){
	$header = imap_fetch_overview($mbox, $UID, FT_UID);
	$datum = $header[0]->udate;
	
	$currentTo = preg_replace_callback("/\{(.*)\}/", function($t) use ($datum) { 
		return date($t[1], $datum);
	}, $to);
	
	echo date("d.m.Y H:i:s", $header[0]->udate);
	
	echo " -> $currentTo";
	
	if(!in_array($currentTo, $folders))
		if(imap_createmailbox($mbox, imap_utf7_encode($server.$currentTo)))
			$folders[] = $currentTo;
	
	if(!in_array($currentTo, $folders)){
		echo " TARGET DOES NOT EXIST! \n";
		continue;
	}
	
	if(imap_mail_move($mbox, $UID, $currentTo, CP_UID))
		echo " OK";
	else
		echo " FEHLER!";
	
	echo "\n";
}

imap_expunge($mbox);