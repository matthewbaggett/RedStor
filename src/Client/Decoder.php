<?php

namespace RedStor\Client;

use RedStor\Exceptions\RESPDecodeException;

class Decoder{
    const REDIS_SEPERATOR = "\r\n";


    public function decode($data){
        #\Kint::dump($data);
        list($data, $output) = $this->__decode($data);
        return $output;
    }

    public function __decode($data)
    {
        if(!$data){
            return null;
        }
        $originalInput = $data;
        $nugget = $this->getNugget($data);
        #\Kint::dump($data);
        $symbol = substr($nugget, 0,1);
        $payload = substr($nugget,1);
        #\Kint::dump($nugget,$symbol, $payload);
        $output = [];
        switch($symbol){
            case '*': // Array
                for($i = 0; $i < (int) $payload; $i++){
                    #\Kint::dump($data);
                    list($data, $newOutput) = $this->__decode($data);
                    #\Kint::dump($newOutput);
                    $output[] = $newOutput;
                    #\Kint::dump($output);
                }
                break;

            case '$': // Bulk Strings
                $stringLength = (int) $payload;
                $string = substr($data, 0, $stringLength);
                $newData = substr($data, $stringLength + 2);
                return [$newData, $string];
                break;

            case '+': // Simple Strings
                $output = (string) $payload;
                break;

            default:
                #\Kint::dump($originalInput);
                throw new RESPDecodeException(sprintf(
                    "Can't decode RESP begining with \"%s\". \nComplete message:\"%s\".",
                    $symbol,
                    $originalInput
                ));

        }
        #\Kint::dump($originalInput, $output);
        return [$data, $output];
    }

    private function getNugget(&$data)
    {
        $originalData = $data;
        //\Kint::dump(stripos($data, self::REDIS_SEPERATOR));
        @list($nugget, $crap) = explode(self::REDIS_SEPERATOR, $data);
        $data = substr($data, stripos($data, self::REDIS_SEPERATOR) + strlen(self::REDIS_SEPERATOR));
        #\Kint::dump($originalData, $nugget, $data);
        return trim($nugget);
    }
}