<?php

function bracketsMatch(string $string, array $symb_arr)
{
    $len = strlen($string);
    $stack = [];
    $symbols = [];
    $symb_form = [];

    $symb_form = prepareArr($symb_arr);
    $symbols = array_keys($symb_form);

    for ($i = 0; $i < $len; $i++) {
        $cur_char = $string[$i];

        if(in_array($cur_char, $symbols))
            switch ($symb_form[$cur_char]['pair']){
                case 0:
                        array_push($stack, $symb_form[$cur_char]['key']);
                break;
                case 1:
                        if (array_pop($stack) !== $symb_form[$cur_char]['key'])
                            return false;
                break;
                default: break;
            }
    }
    return (empty($stack));
}

function prepareArr(array $arr)
{
    $symb_form = [];
    
    foreach($arr as $key => $val) {
        if(!is_array($val) || count($val) != 2)
            throw new Exception($errors[0]);
            
        $symb_form[$val[0]] = [
            'key' => $key,
            'pair'=> 0,
        ];
        $symb_form[$val[1]] = [
            'key' => $key,
            'pair'=> 1,
        ];
    }
    return $symb_form;
}


$errors = [
    0 => 'Wrong format of the config arr',
];

$symbols = [
             ['(',')'],
             ['{','}'],
             ['[',']'],
];

$string_good = '{(a+b)*c-[d/f]}-[d+a]+(a-c)';
$string_bad  = '{(a+b))*c-[[d/f]}-[d+a]+(a-c)';

var_dump(['string_good' => bracketsMatch($string_good, $symbols)],
         ['string_bad' => bracketsMatch($string_bad, $symbols)]);
