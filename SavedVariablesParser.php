<?php
#
# This script written for home use to parse World Of Warcraft auctions data file.
#   Do not use them in public servers, because somebody can remoley hack your site.
#   Primory target of this script to be fast, in that reasion this not converts
#   file to tokens, and parse tokens to multidim array.
#
# PS: If you wish to convert this, to parse real public files, you can use
#   jailed environment, or just continue my work using my regular expressions.
#
#  With regards, LatDEV
#
# PSS: Free Software `MIT Licensed` free to use, for everyone.
#

if (!in_array($_SERVER['SERVER_NAME'], ['alocalhost', '127.0.0.1', ''])) {
    throw new RuntimeException('Attention! This script is absolutely insecure! ' .
    'Never use it on public sites! ' .
    'It\'s raw and uses the eval function to transform the data!');
}



function phpeval_escape_string($string) {
    return str_replace('$', '\\$', $string);
}


function SavedVariablesToArray($filename) {
    $lines = file($filename);
    if ($lines === false) return false;
    $line_counter = 0;
    

    
    $lines = array_map(function($line) use (&$line_counter) {
        $line_counter += 1;
        $line = trim($line);
        if ($line === '') return '';
        # Секция верхнего уровня открывает массив
        if (preg_match('/^([A-Za-z0-9_]+) = \{/', $line, $matches)) {
            return sprintf('$DATA["%s"] = array(', $matches[1]);
        }
        # Секция верхнего уровня == NULL
        if (preg_match('/^([A-Za-z0-9_]+) = nil/', $line, $matches)) {
            return sprintf('$DATA["%s"] = NULL;', $matches[1]);
        }
        # Секция верхнего уровня закрывает массив
        if ($line === '}') return ');';
        
        # Секция верхнего уровня не открывает массив
        if (preg_match('/^([A-Za-z_]+) = (.*)/', $line, $matches)) {
            return sprintf('$DATA["%s"] = %s;', $matches[1], $matches[2]);
        }
        # Секция ассоциативного массива открывает массив
        if (preg_match('/^\[("(?:\\\\.|[^\"])*?")] = {/', $line, $matches)) {
            return sprintf('%s => array(', phpeval_escape_string($matches[1]));
        }

        # Секция ассоциативного массива закрывает массив
        if ($line === '},') return '),';

        # Секция ассоциативного массива НЕ открывает массив
        if (preg_match('/^\[("(?:\\\\.|[^\"])*?")] = (.*)/', $line, $matches)) {
            return sprintf('%s => %s', phpeval_escape_string($matches[1]), $matches[2]);
        }
        # Секция численно-индексированного массива открывает массив
        if ($line === '{') return 'array(';

        # Численно-индексированный массив, вдруг решил стать ассоциативным (збз логика)
        if (preg_match('/^\[(\d+)] = {$/', $line, $matches)) {
            return sprintf('%d => array(', intval($matches[1]) - 1);
        }
        # Численно-индексированный массив со ассоциативным значением NULL в ключе из коммента
        if (preg_match('/^nil, -- \[(\d+)]$/', $line, $matches)) {
            return sprintf('%d => NULL,', intval($matches[1]) - 1);
        }

        # Значение численно-индексированного массива, с ключём не от 0
        if (preg_match('/^\[(\d+)] = (.*)/', $line, $matches)) {
            return sprintf('%d => %s', intval($matches[1])-1, $matches[2]);
        }

        # Секция численно-индексированного массива закрывает массив
        if (preg_match('/^}, -- \[\d+]$/', $line)) return '),';

        # Секция численно-индексированного массива чистые данные
        if (preg_match('/, -- \[\d+]$/', $line, $matches)) return substr($line, 0, strlen($line)-strlen($matches[0])) . ',';

        # Эта линия некогда не должна произойти
        throw new RuntimeException("Unknown data type at line $line_counter (Data: `$line`)");
    }, $lines);
    
    eval(join("\n", $lines));
    return $DATA;
}


/*
 *
 *   Just for testing, we set it on the folder with all SavedVariables
 *

header('Content-Type: text/plain; charset=UTF-8');
function ParseCheck($filename) {
    echo $filename;
    $ok = SavedVariablesToArray($filename);
    if ($ok !== false) echo " [OK]\n";
}
foreach ( new DirectoryIterator('... World of Warcraft Litch ... SavedVariables') as $fileRec ) {
    if ($fileRec->isDir()) continue;
    ParseCheck($fileRec->getPathname());
}

*/