<?php
/* 
 * Author: Heyapple
 */

function colorize($text, $status){
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
	echo colorize("\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n","LINHA");	//{Imprime linha
}																				//{

$modos = array('null','Monitor','Managed','' );	//Lista de modos diponíveis

$output = shell_exec('sudo airmon-ng');			//Pegando interfaces disponíveis
$arr1 = explode("\t", $output);					//Separando palavras para busca
$i = 0;
$count = 0;
while ($i <= 10) {
	$arr2 = array_search("wlan$i", $arr1);		//Filtrando nome da interface
	if ($arr1[$arr2] == "\nPHY") {
		
	}else{
		$count++;
		$inter[$count] = $arr1[$arr2];			//Guardando interfaces em um array
	}
	$i++;
}

system('clear');	//Limpar tela

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

if ($count == 1) { 										//Verifica quantas interfaces tem disponível
	$interface = $inter[$count];
	echo colorize("\n[!] Somente uma interface disponivel ($interface)\n", "NOTA");
}else{
	echo colorize("\n[+] Interfaces Disponíveis [$count]: \n", "NOTA");
	$a = 1;
	while ($a <= $count) {
		echo colorize("\n\t[$a] $inter[$a]\n","OPCAO");	//Lista interfaces
		$a++;
	}
	
	echo colorize("\n[?] Selecione sua interface: \033[s","PERGUNTA");
	$interopt = intval(fgets(STDIN));	//Usuario entra com sua opção
	if ($interopt > 0 && $interopt <= $count) {
		$interface = $inter[$interopt];	//Guarda interface selecionada
		echo "\033[u$interface\n";
	}else{
		echo "\033[u###\n";
		exit(colorize("\n[ERRO] Interface inexistente!\n\n","ERRO"));
	}
	
}

linha();

$countm = intval($modos)+1;							//Conta quantos modos tem salvos
echo colorize("\n[+] Modos:\n", "NOTA");
$a = 1;
while ($modos[$a]) {
	echo colorize("\n\t[$a] $modos[$a]\n","OPCAO");	//Lista os modos
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
system("sudo ifconfig $interface down");		//Desativa (desocupa) interface
	echo colorize('.',"NOTA");
system("sudo iwconfig $interface mode $modo");	//Altera seu modo
	echo colorize('.',"NOTA");
system("sudo rfkill unblock all");				//Desbloqueia interface (para evitar erros)
	echo colorize('.',"NOTA");
system("sudo ifconfig $interface up");			//Ativa interface
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