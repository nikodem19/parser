<?php
/*
 *   IPP projekt 1
 *   Nikodem Babirad
 *   xbabir01
*/

/*
 * Kontrolovanie instrukcii
 * Pridava opcode ak je istrukcia spravna
 *
 * @param   $arrs     pole instrukcii
 * @param   $arr      jednotlive instrukcie
 * @param   $type     typ instrukcie
 * @param   $op_code  meno instrukcie
 */
function checkInstruction(&$arrs, $arr, $type, $op_code ){
    if (!empty($arr)){
        $arr = array_values(array_filter($arr, 'strlen'));
        $arr["type"] = $type;
        $arr["op"] = $arr[0];
        if ($arr["type"] == "type"){
            $arr["value"] = $arr[0];
        }
        elseif($arr["type"] == "symbol" || $arr["type"] == "var" || $arr["type"] == "literal"){
            if ($arr[1] == "GF" || $arr[1] == "LF" || $arr[1] == "TF"){
                $arr["type"] = "var";
                $arr["value"] = $arr[0];
            }
            elseif($arr[1] == "string" || $arr[1] == "int" || $arr[1] == "bool"){
                $arr["type"] = $arr[1];
                if (sizeof($arr) === 5){
                    $arr["value"] = "";
                }
                else{
                    $arr["value"] = $arr[3];
                }
            }
        }
        elseif($arr["type"] == "label"){
            $arr["value"] = $arr[0];
        }
        foreach ($arr as $k => $value) {
            if (is_int($k)) {
                unset($arr[$k]);
            }
        }
    }
    $arrs["op_code"] = $op_code;
    array_push($arrs, $arr);
}

/*
 * Syntakticka kontrola
 * Vykonava sa pre kazdy prikay
 * Prikazy su rozdelene do skupin podla poctu parametrov a dalej sa pouzivaju
 * Kazdy parameter instrukcie program pomocou regexou skontroluje
 *
 * @param   $item  prikaz
 * @return  array  spracovany prikaz alebo err
 */
function checkSyntax($item){
    $symb ='/^(nil)(@)(nil)$|^(bool)(@)(true|false)$|^(int)(@)(\S*)$|^(string)(@)([\S]*)$|^(GF|LF|TF)(@)([a-zA-Z_\-$&%*!?]+[a-zA-Z0-9_\-$&%*!?]*)$/';
    $label = '/^([a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]+)$/';
    $type = '/^(bool|int|string)$/';
    $var = '/^(GF|LF|TF)(@)([a-zA-Z_\-$&%*!?]+[a-zA-Z0-9_\-$&%*!?]*)$/';

    $operations = array();
    $operationsWithNone = array("CREATEFRAME","PUSHFRAME","POPFRAME","RETURN","BREAK");
    $operationsWithOne = array("DEFVAR","CALL","PUSHS","POPS","DPRINT", "WRITE","LABEL","JUMP","EXIT");
    $operationsWithTwo = array("MOVE","INT2CHAR","READ","STRLEN","TYPE","NOT");
    $operationsWithTree = array("ADD","SUB","MUL","IDIV","LT","GT","EQ","AND","OR","STRI2INT","CONCAT","GETCHAR","SETCHAR","JUMPIFEQ","JUMPIFNEQ");
    $item[0] = strtoupper($item[0]);

    if (in_array($item[0], $operationsWithNone)){ // 0
        if ((sizeof($item) - 1) != 0){
            exit(23);
        }
        $key_reg = array();
        checkInstruction($operations, $key_reg, "label", $item[0]);
        return $operations;
    }

    elseif (in_array($item[0], $operationsWithOne)){ // 1
        if ((sizeof($item) - 1) != 1){
            exit(23);
        }
        if($item[0] == "DEFVAR" || $item[0] == "POPS"){
            if(preg_match($var, $item[1], $key_reg) === 1){
                checkInstruction($operations, $key_reg, "var", $item[0]);
            }
            else{
                exit(23);
            }
        }
        if($item[0] == "CALL" || $item[0] == "LABEL" || $item[0] == "JUMP"){
            if(preg_match($label, $item[1], $key_reg) === 1){
                checkInstruction($operations, $key_reg, "label", $item[0]);
            }
            else{
                exit(23);
            }
        }
        if($item[0] == "PUSHS" || $item[0] == "WRITE" || $item[0] == "EXIT" || $item[0] == "DPRINT"){
            if(preg_match($symb, $item[1], $key_reg) === 1){
                checkInstruction($operations, $key_reg, "symbol", $item[0]);
            }
            else{
                exit(23);
            }
        }
        return $operations;
    }

    elseif (in_array($item[0], $operationsWithTwo)){ // 2
        if ((sizeof($item) - 1) != 2){
            exit(23);
        }
        if(preg_match($var, $item[1], $key_reg) === 1){
            checkInstruction($operations, $key_reg, "var", $item[0]);
        }
        else{
            exit(23);
        }
        if ($item[0] === "READ"){
            if(preg_match($type, $item[2], $key_reg) === 1){
                checkInstruction($operations, $key_reg, "type", $item[0]);
            }
            else{
                exit(23);
            }
        }
        else{
            if(preg_match($symb, $item[2], $key_reg) === 1){
                checkInstruction($operations, $key_reg, "symbol", $item[0]);
            }
            else{
                exit(23);
            }
        }
        return $operations;
    }

    elseif (in_array($item[0], $operationsWithTree)){  //3
        if ((sizeof($item) -1) != 3){
            exit(23);
        }
        elseif ($item[0] === "JUMPIFEQ" || $item[0] === "JUMPIFNEQ"){
            if (preg_match($label, $item[1], $key_reg) === 1){ //first argument <label>
                checkInstruction($operations, $key_reg, "label", $item[0]);
            }
            else{
                exit(23);
            }
        }
        else{
            if (preg_match($var, $item[1], $key_reg) === 1){ //first argument <var>
                checkInstruction($operations, $key_reg, "var", $item[0]);
            }
            else{
                exit(23);
            }
        }
        for($i = 2; $i <= 3; $i++){
            if (preg_match($symb, $item[$i], $key_reg) === 1){
                checkInstruction($operations, $key_reg, "symbol", $item[0]);
            }
            else{
                exit(23);
            }
        }
        return $operations;
    }
    else
        exit(22);
}

/*
 * Pomocna funkcia na odstranovanie medzier a komentarov
 * @param   $array
 * @return upravene pole alebo false ak nie je co upravovat
*/
function clear($array){

    $ret = array();
    $j = 0;

    foreach ($array as $t){
        if ($t == ""){
            continue;
        }
        else if(strpos($t, "#")){
            $t = explode("#", $t)[0];
            $ret[$j] = trim($t);
            break;
        }
        else if ($t[0] == "#"){
            break;
        }
        else{
            $ret[$j] = trim($t);
            $j++;
        }
    }

    if (sizeof($ret) > 0)
        return array_filter($ret);

    return false;
}

/*
 * Funkcia generuje vystupny XML kod.
 *
 * @param   $instr    instrukcie a parametre
 */
function codeGenerator($instr){
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><program></program>');
    $xml->addAttribute('language', 'IPPcode20');

    for($i = 0; $i <= sizeof($instr) - 1; $i++){
        $instruction = $xml->addChild('instruction');
        $instruction->addAttribute('order', $i+1);
        $instruction->addAttribute('opcode', $instr[$i]['op_code']);

        for($j = 0; $j <= sizeof($instr[$i]) - 2; $j++){
            if(array_key_exists("value", $instr[$i][$j])){
                $arg = $instruction->addChild('arg'.($j+1), htmlspecialchars($instr[$i][$j]['value']));
                $arg->addAttribute("type", $instr[$i][$j]['type']);
            }
        }
    }

    $result = dom_import_simplexml($xml)->ownerDocument;
    $result->formatOutput = true;

    echo $result->saveXML();
}

/*
 * Hlavny program
 * Skontroluje argumenty programu a postupne po riadkoch nacitava zo vstupneho suboru data
 * Vysledny kod generuje do XML a vypisuje
 *
*/

if (sizeof($argv) == 2){
    if ($argv[1] == "--help"){ // help
        print
            'IPP 2019/2020 PROJECT'
            ."\nAuthor:\tNikodem Babirad"
            ."\nLogin:\txbabir01"
            ."\nLoads input code in IPPcode20"
            ."\nRuns a lexical and syntax analysis"
            ."\nWrite out a XML representation of the IPPcode20 program";
        exit(0);
    }
    else{
        exit(10);
    }
}

if (sizeof($argv) > 2){ // kontrola vstupov
    exit(10);
}
if (($file_header = fgets(STDIN)) === false){
    exit(21);
}
if (strpos($file_header, "#")){
    $file_header = explode("#", $file_header)[0];
}
if (strtoupper(trim($file_header)) !== ".IPPCODE20"){
    exit(21);
}
$instrArr = array();

while (!feof(STDIN)){
    $line = fgets(STDIN);
    $line = preg_replace('/\t+/', ' ', $line);
    $words = explode(" ", trim($line));
    $editedCode = clear($words);
    if ($editedCode !== false) {
        $analysed = checkSyntax($editedCode);
        array_push($instrArr, $analysed);
    }
}
codeGenerator($instrArr);
exit(0);
?>