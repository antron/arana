<?php

namespace Antron\Arana;

class Arana
{

    public static function readCsv($filepath, $config)
    {
        if (isset($config['delimiter'])) {
            $delimiter = $config['delimiter'];
        } else {
            $delimiter = ',';
        }
        if (isset($config['header'])) {
            $header = $config['header'];
        } else {
            $header = true;
        }
        if (isset($config['encode'])) {
            $encode = $config['encode'];
        } else {
            $encode = 'UTF-8';
        }

        $csv = [];

        $texts = self::readTxt($filepath, $encode);

        foreach ($texts as $text) {
            $array = str_replace(['"'], '', $text);
            $csv[] = explode($delimiter, $array);
        }

        if ($header) {
            return self::toHash($csv);
        } else {
            return $csv;
        }
    }

    public static function readTxt($filepath, $encode = 'UTF-8')
    {
        $texts = [];

        if (!file_exists($filepath)) {
            return $texts;
        }

        $buf = file_get_contents($filepath);

        if ($encode !== 'UTF-8') {
            $buf = mb_convert_encoding($buf, 'UTF-8', $encode);
        }

        $lines = str_getcsv(rtrim($buf, "\x1A"), "\n");

        foreach ($lines as $line) {
            $texts[] = str_replace(["\r\n", "\r", "\n"], '', $line);
        }

        return $texts;
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
