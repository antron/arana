<?php

namespace Antron\Arana;

/**
 * Arana.
 */
class Arana
{

    /**
     * Default config.
     *
     * @var array
     */
    private static $config = [
        'delimiter' => ',',
        'header' => true,
        'encode' => 'UTF-8',
        'quotation' => true,
        'kana' => false,
    ];

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

    public static function readTxt($filepath, $encode = '', $kana = false)
    {
        $texts = [];

        if (!file_exists($filepath)) {
            return $texts;
        }

        $buf = file_get_contents($filepath);

        if ($encode !== 'UTF-8') {
            $buf = mb_convert_encoding($buf, 'UTF-8', $encode);
        }

        if ($kana) {
            $buf = mb_convert_kana($buf, "KV");
        }

        $lines = str_getcsv(rtrim($buf, "\x1A"), "\n");

        foreach ($lines as $line) {
            $texts[] = str_replace(["\r\n", "\r", "\n"], '', $line);
        }

        return $texts;
    }

    public static function write($arrays, $config_arg = [])
    {
        if (!$arrays) {
            return;
        }

        $string_implode = '';

        $config = self::config($config_arg);

        if ($config['header']) {
            $headers = [];
            foreach ($arrays[0] as $key => $value) {
                $headers[] = $key;
                $value = null;
            }

            $string_implode.=implode($config['delimiter'], $headers) . "\n";
        }

        foreach ($arrays as $array) {
            $string_implode.=implode($config['delimiter'], $array) . "\n";
        }

        $string_texts = mb_convert_encoding($string_implode, $config['encode'], 'UTF-8');

        file_put_contents($config['filepath'], $string_texts);
    }

    private static function config($config)
    {
        if (!isset($config['filepath'])) {
            $config['filepath'] = storage_path('arana.txt');
        }

        return $config + self::$config;
    }

    private static function toHash($csv)
    {
        $hash = [];

        $heads = [];

        $count = 1;

        $dupli = [];

        if (!$csv) {
            return $hash;
        }

        foreach (array_shift($csv) as $key => $value) {
            if (isset($dupli[$value])) {
                $heads[$key] = "$value$count";
                
                $count++;
            } else {
                $heads[$key] = $value;
            }
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
