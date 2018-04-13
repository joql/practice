<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/12
 * Time: 16:44
 */

require 'zip.php';
require 'CFPropertyList/CFPropertyList.php';
require 'pngCompote.php';

class IpaParser
{
    private $newDir;//保存文件目录
    private $oldDir;//原目录
    private $filename;//文件名
    private $appDir;//app 目录名

    public function __construct($oldDir, $filename, $newDir)
    {
        $this->oldDir = $oldDir;
        $this->filename = $filename;
        $this->newDir = $newDir;
    }

    private function ipa2Dir(){
        $this->appDir = time().rand('111','999');
        @copy($this->oldDir.$this->filename, str_replace('ipa', 'zip', $this->newDir.$this->filename));
        $zip = new PclZip(str_replace('ipa', 'zip', $this->newDir.$this->filename));
        $dir = $this->newDir.$this->appDir.'/';
        if(!is_dir($dir)){
            @mkdir($dir, 0777, true);
        }
        $zip->extract(77001, $dir, 77016);
        return true;
    }

    private function parse(){
        $dir = $this->newDir.$this->appDir.'/Payload';
        if(!is_dir($dir)){
            return false;
        }


        $d = NULL;
        $h = opendir($dir);
        while ($f = readdir($h)) {
            if ($f != '.' && $f != '..' && is_dir($dir . '/' . $f)) {
                $d = $dir . '/' . $f;
            }
        }
        closedir($h);
        $info = file_get_contents($d . '/Info.plist');
        $plist = new \CFPropertyList\CFPropertyList();
        $plist->parse($info);
        $plist = $plist->toArray();

        $xml_name = $plist['CFBundleDisplayName'];
        $xml_mnvs = $plist['MinimumOSVersion'];
        $xml_bid = $plist['CFBundleIdentifier'];
        $xml_bsvs = $plist['CFBundleShortVersionString'];
        $xml_bvs = $plist['CFBundleVersion'];

        //icon
        $icon_path = $this->newDir.$this->appDir.'.png';
        $icon = $plist['CFBundleIcons']['CFBundlePrimaryIcon']['CFBundleIconFiles'];
        if (preg_match('/\./', $icon[0])) {
            for ($i = 0; $i < count($icon); $i++) {
                $array[] = filesize($d . '/' . $icon[$i]);
            }
            sort($array);
            for ($p = 0; $p < count($icon); $p++) {
                if ($array[0] == filesize($d . '/' . $icon[$p])) {
                    $oldfile = $d . '/' . $icon[$p];
                }
            }
        } else {
            for ($i = 0; $i < count($icon); $i++) {
                $array[] = filesize($d . '/' . $icon[$i] . '@2x.png');
            }
            sort($array);
            for ($p = 0; $p < count($icon); $p++) {
                if ($array[0] == filesize($d . '/' . $icon[$p] . '@2x.png')) {
                    $ext = preg_match('/20x20/', $icon[$p]) ? '@3x.png' : '@2x.png';
                    $oldfile = $d . '/' . $icon[$p] . $ext;
                }
            }
        }
        $png = new \PngFile\PngFile($oldfile);
        if (!$png->revertIphone($icon_path)) {
            //copy('../../../static/app/icon.png', $icon_path);
        }
        $xml_icon = $icon_path;

        return [
            'name'=>$xml_name,
            'mnvs'=>$xml_mnvs,
            'bid'=>$xml_bid,
            'bsvs'=>$xml_bsvs,
            'bvs'=>$xml_bvs,
            'icon'=>$xml_icon
        ];

    }

    public function handle(){
        $this->ipa2Dir();
       var_dump($this->parse());
    }

    /**
     * use for:编码转换
     * auth: Joql
     * @param $str
     * @return string
     * date:2018-04-12 15:45
     */
    private function detect_encoding($str){
        $chars = NULL;
        $list = array('GBK', 'UTF-8');
        foreach($list as $item){
            $tmp = mb_convert_encoding($str, $item, $item);
            if(md5($tmp) == md5($str)){
                $chars = $item;
            }
        }
        return strtolower($chars) !== 'gbk' ? iconv($chars, strtoupper('gbk').'//IGNORE', $str) : $str;
    }

}