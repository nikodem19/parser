IPP - Principy programovacích jazyků 2019/2020
---
##Zadanie
Navrhněte, implementujte, dokumentujte a testujte sadu skriptů pro interpretaci nestrukturovaného imperativního jazyka IPPcode18.
K implementaci vytvořte odpovídající stručnou programovou dokumentaci.
Projekt se skládá ze dvou úloh a je individuální:
První úloha se skládá ze skriptu parse.php v jazyce PHP 5.6 (viz sekce 3).
Druhá úloha se skládá ze skriptu interpret.py v jazyce Python 3.6 (viz sekce 4), testovacího skriptu test.php v jazyce PHP 5.6 (viz sekce 5) a dokumentace těchto skriptů (viz sekce 2.1).

##parse.php
Skript typu filtr (parse.php v jazyce PHP 7.4) načte ze standardního vstupu zdrojový kód v IPPcode20 (viz sekce 6), zkontroluje lexikální a syntaktickou správnost kódu a vypíše na standardní
výstup XML reprezentaci programu dle specifikace v sekci 3.1

#####Tento skript bude pracovat s těmito parametry:
• --help viz společný parametr všech skriptů v sekci 2.2.

######Chybové návratové kódy specifické pro analyzátor:
#####• 21 - chybná nebo chybějící hlavička ve zdrojovém kódu zapsaném v IPPcode20;
#####• 22 - neznámý nebo chybný operační kód ve zdrojovém kódu zapsaném v IPPcode20;
#####• 23 - jiná lexikální nebo syntaktická chyba zdrojového kódu zapsaného v IPPcode20.

##Error kódy
#####• 10 - chybějící parametr skriptu (je-li třeba) nebo použití zakázané kombinace parametrů;
#####• 11 - chyba při otevírání vstupních souborů (např. neexistence, nedostatečné oprávnění);
#####• 12 - chyba při otevření výstupních souborů pro zápis (např. nedostatečné oprávnění);
#####• 20 – 69 - návratové kódy chyb specifických pro jednotlivé skripty;
#####• 99 - interní chyba (neovlivněná vstupními soubory či parametry příkazové řádky; např. chyba alokace paměti).

##Implementácia
Implementácia programu začala kontrolou argumentov a kontrolou vstupov. Kód načítaný zo vstupuju sa posiela na syntaktickú a sématickú analýzu. Pri analýze sa každý riadok zo vstupného kódu skontroluje pomocou regexou a vloží do pola spolu s jeho parametrami. 
Posledná hlavná časť programu je generovanie výsledného XML suboru. Všetky pripravené inštrukcie sa spolu s ich parametrami preložia v správnom formáte na výsledný kód.