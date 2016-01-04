<?php
/* 
 * Author: Heyapple
 */

function colorize($text, $status){
	$out = "";
	switch ($status) {
		case "SUCESSO":
			$out = "[1;32m"; //Green Foreground
			break;
		case "ERRO":
			$out = "[1;31m"; //Red Foreground;
			break;
		case "NOTA":
			$out = "[1;33m"; //Blue Foreground;
			break;
		case "OPCAO":
			$out = "[38;5;166;1m";
			break;
		case "PERGUNTA":
			$out = "[38;5;228;1m";
			break;
		case "LINHA":
			$out = "[38;5;208;1m";
			break;
		default:
			$out = "[0m"; //Default
			break;
	}
	return chr(27) . "$out" . "$text" . chr(27) . "[0m";
}

function linha(){
	echo colorize("\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n","LINHA");
}

$modos = array('null','Monitor','Managed','' );

$output = shell_exec('sudo airmon-ng');
$arr1 = explode("\t", $output);
$i = 0;
$count = 0;
while ($i <= 10) {
	$arr2 = array_search("wlan$i", $arr1);
	if ($arr1[$arr2] == "\nPHY") {
		
	}else{
		$count++;
		$inter[$count] = $arr1[$arr2];
	}
	$i++;
}

system('clear');

echo colorize((' _    _  _______  _      __   ____   ______   ______   _       ________
| \  | \|       \| \    /  \ /    \ |      \ |      \ | \     |        \
| $  | $| $$$$$$$ \\$\  /   $| $$$$$\| $$$$$$\| $$$$$$\| $     | $$$$$$$
| $__| $| $__      \\$\/   $ | $__| $| $__/ $$| $__/ $$| $     | $__ 
| $    $| $  \      \\$   $  | $    $| $    $$| $    $$| $     | $  \
| $$$$$$| $$$$       \\$$$   | $$$$$$| $$$$$$ | $$$$$$ | $     | $$$$
| $  | $| $______    | $    | $  | $| $      | $      | $____ | $______
| $  | $| $      \   | $    | $  | $| $      | $      | $    \| $      \
 \\$   \\$ \\$$$$$$$$    \\$     \\$   \\$ \\$       \\$       \\$$$$$$ \\$$$$$$$$'),"NOTA");
echo colorize("\n[H] Modificador de modo de interface wi-fi\n","NOTA");

linha();

if ($count == 1) {
	$interface = $inter[$count];
	echo colorize("\n[!] Somente uma interface disponivel ($interface)\n", "NOTA");
}else{
	echo colorize("\n[+] Interfaces Disponíveis [$count]: \n", "NOTA");
	$a = 1;
	while ($a <= $count) {
		echo colorize("\n\t[$a] $inter[$a]\n","OPCAO");
		$a++;
	}
	
	echo colorize("\n[?] Selecione sua interface: \033[s","PERGUNTA");
	$interopt = intval(fgets(STDIN));
	if ($interopt > 0 && $interopt <= $count) {
		$interface = $inter[$interopt];	
		echo "\033[u$interface\n";
	}else{
		echo "\033[u###\n";
		exit(colorize("\n[ERRO] Interface inexistente!\n\n","ERRO"));
	}
	
}

linha();

$countm = intval($modos)+1;
echo colorize("\n[+] Modos:\n", "NOTA");
$a = 1;
while ($modos[$a]) {
	echo colorize("\n\t[$a] $modos[$a]\n","OPCAO");
	$a++;
}
echo colorize("\n[?] Selecione o modo: \033[s","PERGUNTA");
$modoopt = intval(fgets(STDIN));
if ($modoopt > 0 && $modoopt <= $countm) {
	$modo = strtolower($modos[$modoopt]);
	echo "\033[u".ucfirst($modo) ."\n";
}else{
	echo "\033[u###\n";
	exit(colorize("\n[ERRO] Modo inválido\n\n","ERRO"));
}

linha();

echo colorize("\n[!] ".ucfirst($modo)."?", "NOTA");
	sleep(1);
echo colorize("  Ok!","NOTA");
	sleep(1);

	echo colorize("\n\n[...] Alterando","NOTA");
system("sudo ifconfig $interface down");
	echo colorize('.',"NOTA");
system("sudo iwconfig $interface mode $modo");
	echo colorize('.',"NOTA");
system("sudo rfkill unblock all");
	echo colorize('.',"NOTA");
system("sudo ifconfig $interface up");
	echo colorize("\n\n[OK] Prontinho =)\n","SUCESSO");

if ($modo == 'monitor') {
	linha();
	echo colorize("\n[?] Airodump?[Y/n]: \033[s", "PERGUNTA");
	$dump = strtolower(fgets(STDIN));
	if ($dump == "y\n" || $dump == "\n") {
		echo "\033[uYes\n";
		echo colorize("\n[!] Ok! Um segundo!", "SUCESSO");
			sleep(1);
		system("sudo airodump-ng $interface --wps --uptime");
	}elseif ($dump == "n\n") {
		echo "\033[uNope\n";
		echo colorize("\n[!] Ok! Bye!\n\n", "NOTA");
	}else{
		echo "\033[u###\n";
		echo colorize("\n[ERRO] Opção Inválida!\n", "ERRO");
	}
}