<?php

class ExampleTest extends TestCase
{

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasic()
    {
        $csv = \Arana::readCsv([
                    'filepath' => __DIR__ . '/arana.csv',
                    'encode' => 'sjis-win',
                    'delimiter' => "\t",
        ]);
        $this->assertEquals($csv[0]['県庁所在地'], '横浜市');
    }

}
