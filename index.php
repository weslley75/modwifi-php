<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$output = shell_exec('ipconfig');
echo "$output";
echo 'Digite um número: ';
$a = fgets(STDIN); 
echo 'Digite outro número: ';
$b = fgets(STDIN);
echo 'A soma dos números é: ',$a + $b;
