<?php
/**
 * Arana.
 */
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

    /**
     * Read CSV File.
     * 
     * @param array $config_arg Config
     * @return array Data
     */
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

    /**
     * Read Text.
     * 
     * @param string $filepath Filepath
     * @param string $encode encode
     * @param boolean $kana TRUE is 
     * @return array
     */
    public static function readTxt($filepath, $encode = 'UTF-8', $kana = false)
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

    /**
     * Write FIle.
     *
     * @param array $arrays
     * @param array $config_arg config
     * @return void
     */
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

    /**
     * make Config.
     *
     * @param array $config Config
     * @return array Config
     */
    private static function config($config)
    {
        if (!isset($config['filepath'])) {
            $config['filepath'] = storage_path('arana.txt');
        }

        return $config + self::$config;
    }

    /**
     * to Hash.
     *
     * @param array $csv
     * @return array
     */
    private static function toHash($csv)
    {
        $hash = [];

        if (!$csv) {
            return $hash;
        }
        
        $heads= self::getHeads(array_shift($csv));

        foreach ($csv as $datas) {
            $tmpdata = [];

            foreach ($datas as $key => $value) {
                if (isset($heads[$key])) {
                    $tmpdata[$heads[$key]] = $value;
                } else {
                    return [];
                }
            }

            $hash[] = $tmpdata;
        }

        return $hash;
    }

    /**
     * get Heads
     * 
     * @param array $csv
     * @return array
     */
    private static function getHeads($csv)
    {
        $count = 1;

        $dupli = [];
        
        $heads=[];

        foreach ($csv as $key => $value) {
            if (isset($dupli[$value])) {
                $heads[$key] = "$value$count";

                $count++;
            } else {
                $heads[$key] = $value;
            }
        }
        
        return $heads;
    }
}
