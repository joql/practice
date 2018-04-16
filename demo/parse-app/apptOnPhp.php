<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/16
 * Time: 16:57
 */



//test
$test = new apptOnPhp('test.apk', 'D:\tool\apk-tools\aapt');
$test->parse();

//
class apptOnPhp{

    //windows http://oykeubbl7.bkt.clouddn.com/apk-tools.7z
    private $aapt_path;
    private $apk_path;
    private $apk_info;
    public function __construct($apk_path, $aapt_path = 'aapt')
    {
        $this->aapt_path = $aapt_path;
        $this->apk_path = $apk_path;
    }
    public function parse(){
        exec($this->aapt_path.' d badging '.$this->apk_path.' 2>&1', $this->apk_info);
        if(strpos($this->apk_info[0],'package') !== false){
            return true;
        }
        return false;
    }


}