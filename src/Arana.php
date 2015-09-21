<?php

namespace Antron\Arana;

class Arana
{

    public static function readCsv($config_arg)
    {

        $csv = [];

        $config = self::config($config_arg);

        $texts = self::readTxt($config['filepath'], $config['encode'], $config['kana']);

        foreach ($texts as $text) {
            if ($config['quotation']) {
                $text = str_replace(['"'], '', $text);
            }
            $csv[] = explode($config['delimiter'], $text);
        }

        if ($config['header']) {
            return self::toHash($csv);
        } else {
            return $csv;
        }
    }

    public static function readTxt($filepath, $encode,$kana)
    {
        $texts = [];

        if (!file_exists($filepath)) {
            return $texts;
        }

        $buf = file_get_contents($filepath);

	if($kana){
            $buf=mb_convert_kana($buf,"KV");
	}

        if ($encode !== 'UTF-8') {
            $buf = mb_convert_encoding($buf, 'UTF-8', $encode);
        }

        $lines = str_getcsv(rtrim($buf, "\x1A"), "\n");

        foreach ($lines as $line) {
            $texts[] = str_replace(["\r\n", "\r", "\n"], '', $line);
        }

        return $texts;
    }

    public static function write($arrays, $config_arg)
    {
        $config = self::config($config_arg);

        foreach ($arrays as $array) {
            $string_implode.=implode($config['delimiter'], $array) . "\n";
        }

        $string_texts = mb_convert_encoding($string_implode, $config['encode'], 'UTF-8');
        
        file_put_contents($config['filepath'], $string_texts);
    }

    private static function config($config)
    {
        if (!isset($config['delimiter'])) {
            $config['delimiter'] = ',';
        }
        if (!isset($config['header'])) {
            $config['header'] = true;
        }
        if (!isset($config['encode'])) {
            $config['encode'] = 'UTF-8';
        }
        if (!isset($config['quotation'])) {
            $config['quotation'] = true;
        }
        if (!isset($config['filepath'])) {
            $config['filepath'] = storage_path('arana.txt');
        }
        if (!isset($config['kana'])) {
            $config['kana'] = false;
        }
        return $config;
    }

    private static function toHash($csv)
    {
        $hash = [];
        foreach (array_shift($csv) as $key => $value) {
            $heads[$key] = $value;
        }

        foreach ($csv as $datas) {
            $tmpdata = [];

            foreach ($datas as $key => $value) {
                $tmpdata[$heads[$key]] = $value;
            }

            $hash[] = $tmpdata;
        }

        return $hash;
    }

}
