#!/usr/bin/php
<?php
/**
 *	@author Heyapple
 */
function verifica_modo($interface){
	$output = shell_exec("sudo iwconfig $interface");
	preg_match('/Mode:(.*?) /s',$output,$arr2);
	return $arr2[1];
}

function cor($text, $status){
	$out = "";
	switch ($status) {
		case "SUCESSO":
			$out = "[1;32m";		//Cor Verde
			break;
		case "ERRO":
			$out = "[1;31m";		//Cor Vermelha
			break;
		case "NOTA":
			$out = "[1;33m";		//Cor Amarela
			break;
		case "OPCAO":
			$out = "[38;5;166;1m";	//Cor Laranja
			break;
		case "PERGUNTA":
			$out = "[38;5;228;1m";	//Cor Amarelo Claro
			break;
		case "LINHA":
			$out = "[38;5;208;1m";	//Cor Laranja Claro
			break;
		default:
			$out = "[0m";			//Padrão
			break;
	}
	return chr(27) . "$out" . "$text" . chr(27) . "[0m";	//Imprime cor selecionada
}

function linha(){																//{
	echo cor("\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n","LINHA");			//{Imprime linha
}																				//{
////////////////////////////////////////////////////////////////////////////////////
$opcoes = getopt("a::i:m:",array('interface:','modo:'));
$modos = array('null','Monitor','Managed','' );	//Lista de modos diponíveis

$output = shell_exec('sudo airmon-ng | grep -P "phy[0-90-9]"');			//Pegando interfaces disponíveis
preg_match_all('/(.*?)		.*? 	.*?\]\n/s',$output,$arr1);										//Separando palavras para busca
$i = 0;
$count = 0;
$limit = count($arr1[1]);
while ($count < $limit) {
	$count++;
	$inter[$count] = $arr1[1][$i];											//Guardando interfaces em um array
	$i++;
}

system('clear');	//Limpar tela

echo cor((' _    _  _______  _      __   ____   ______   ______   _       ________
| \  | \|       \| \    /  \ /    \ |      \ |      \ | \     |        \
| $  | $| $$$$$$$ \\$\  /   $| $$$$$\| $$$$$$\| $$$$$$\| $     | $$$$$$$
| $__| $| $__      \\$\/   $ | $__| $| $__/ $$| $__/ $$| $     | $__
| $    $| $  \      \\$   $  | $    $| $    $$| $    $$| $     | $  \
| $$$$$$| $$$$       \\$$$   | $$$$$$| $$$$$$ | $$$$$$ | $     | $$$$
| $  | $| $______    | $    | $  | $| $      | $      | $____ | $______
| $  | $| $      \   | $    | $  | $| $      | $      | $    \| $      \
 \\$   \\$ \\$$$$$$$$    \\$     \\$   \\$ \\$       \\$       \\$$$$$$ \\$$$$$$$$'),"NOTA");
echo cor("\n[H] Modificador de modo de interface wi-fi\n","NOTA");

linha();
if (isset($opcoes["i"])) {
	$interface = $opcoes["i"];
}elseif (isset($opcoes["interface"])) {
	$interface = $opcoes["interface"];
}else{
	if ($count == 1) { 										//Verifica quantas interfaces tem disponível
		$interface = $inter[$count];
		echo cor("\n[!] Somente uma interface disponivel:".chr(27)."[38;5;166;1m"." $interface (".verifica_modo($interface).")\n", "NOTA");
	}else{
		echo cor("\n[+] Interfaces Disponíveis [$count]: \n", "NOTA");
		$a = 1;
		while ($a <= $count) {
			echo cor("\n\t[$a] $inter[$a] (".verifica_modo("$inter[$a]").")\n","OPCAO");	//Lista interfaces
			$a++;
		}
		echo cor("\n[?] Selecione sua interface: \033[s","PERGUNTA");
		$interopt = intval(fgets(STDIN));	//Usuario entra com sua opção
		if ($interopt > 0 && $interopt <= $count) {
			$interface = $inter[$interopt];	//Guarda interface selecionada
			echo "\033[u$interface\n";
		}else{
			echo "\033[u###\n";
			exit(cor("\n[ERRO] Interface inexistente!\n\n","ERRO"));
		}
	}
linha();
}
$execou = exec("sudo iwconfig '$interface' 2>&1 ",$output,$result);
if ($result == '0') {
	//ok
}else{
	exit(cor("\n[ERRO] Interface inexistente!\n\n","ERRO"));
}


if (isset($opcoes["m"])) {
	if ($opcoes["m"] > 0) {
		$modo = $modos[$opcoes["m"]];
	}else{
		$modo = $opcoes["m"];
	}
}elseif (isset($opcoes["modo"])) {
	if ($opcoes["modo"] > 0) {
		$modo = $modos[$opcoes["modo"]];
	}else{
		$modo = $opcoes["modo"];
	}
}else{
	$countm = intval($modos)+1;							//Conta quantos modos tem salvos
	echo cor("\n[+] Modos:\n", "NOTA");
	$a = 1;
	while ($modos[$a]) {
		echo cor("\n\t[$a] $modos[$a]\n","OPCAO");	//Lista os modos
		$a++;
	}
	echo cor("\n[?] Selecione o modo: \033[s","PERGUNTA");
	$modoopt = intval(fgets(STDIN));
	if ($modoopt > 0 && $modoopt <= $countm) {
		$modo = strtolower($modos[$modoopt]);
		echo "\033[u".ucfirst($modo) ."\n";
	}else{
		echo "\033[u###\n";
		exit(cor("\n[ERRO] Modo inválido\n\n","ERRO"));
	}
linha();
}
if (in_array(ucfirst($modo), $modos)) {

}else{
	exit(cor("\n[ERRO] Modo inválido\n\n","ERRO"));
}


echo cor("\n[!] ".ucfirst($modo)."?", "NOTA");
	sleep(1);
echo cor("  Ok!","NOTA");
	sleep(1);

	echo cor("\n\n[...] Alterando","NOTA");
system("sudo ifconfig $interface down");		//Desativa (desocupa) interface
	echo cor('.',"NOTA");
system("sudo iwconfig $interface mode $modo");	//Altera seu modo
	echo cor('.',"NOTA");
system("sudo rfkill unblock all");				//Desbloqueia interface (para evitar erros)
	echo cor('.',"NOTA");
system("sudo ifconfig $interface up");			//Ativa interface
	echo cor("\n\n[OK] Prontinho =)\n","SUCESSO");

if ($modo == 'monitor') {
	linha();
	echo cor("\n[?] Airodump?[Y/n]: \033[s", "PERGUNTA");
	$dump = strtolower(fgets(STDIN));
	if ($dump == "y\n" || $dump == "\n") {
		echo "\033[uYes\n";
		echo cor("\n[!] Ok! Um segundo!", "SUCESSO");
			sleep(1);
		system("sudo airodump-ng $interface --wps --uptime");
	}elseif ($dump == "n\n") {
		echo "\033[uNope\n";
		echo cor("\n[!] Ok! Bye!\n\n", "NOTA");
	}else{
		echo "\033[u###\n";
		echo cor("\n[ERRO] Opção Inválida!\n", "ERRO");
	}
}
