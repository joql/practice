<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/11
 * Time: 22:40
 */

require './IpaParser.php';
require 'apptOnPhp.php';

error_reporting(E_ALL);
//权限设置为755
//

if(!empty($_FILES)){
	$filepart = pathinfo($_FILES['app']['name']);
	if(strtolower($filepart['extension']) == 'ipa'){

        $dir = './data/';
        $name = time().rand(111,999).'.ipa';
        copy($_FILES['app']['tmp_name'], $dir.$name);
        $ipa = new \Extend\IpaParser($dir, $name, $dir);
        if($ipa->parse()){
            //处理该上传文件
            $ctlist = array(
                'ssl_server' => 'http://' . $_SERVER['SERVER_NAME'] . '/',
                'bundle_identifier' =>  $ipa->getBid(),
                'title' => $ipa->getAppName(),
                'cid' => $domain, //下载地址
                'subtitle' =>  $ipa->getBid(),
                'versionname' => $ipa->getVersion(),
            );
            $fp = fopen('./Public/appipa/' . $_POST['iLocalId'] . ".plist", "w+");
            fwrite($fp, createplist($ctlist));
            fclose($fp);

        };
	}elseif (strtolower($filepart['extension'] == 'apk')){
        if(PATH_SEPARATOR == ';'){
            $aapt_path = 'cd ./aapt/windows/ && aapt.exe ';
        }else{
            $aapt_path = 'cd ./aapt/linux/ && ./ aapt ';
        }

        $apk = new apptOnPhp(PUBLIC_PATH.'/uploads/'.$domain, $aapt_path);
        if($aprse_result = $apk->parse()){
        }else{
            $data['status'] = 'error';
            $data['parse'] = $aprse_result;

            $this->ajaxReturn($data);
        }
	}
}



//创建plist
function createplist($ctlist)
{
    $title = $ctlist['title'];
    $subtitle = $ctlist['subtitle'];
    $versionname = $ctlist['versionname'];
    $bundle_identifier = $ctlist['bundle_identifier'];
    $ssl_server = $ctlist['ssl_server'];
    $channelid = $ctlist['cid'];
    if (!$versionname) {
        $versionname = '6.0.0';
    }
    $versioncode = str_replace('.', '', $versionname);

    if (!$channelid) {
        $channelid = '0';
    }

    header('Content-Type: application/xml');
    $plist = new XmlWriter();
    $plist->openMemory();
    $plist->setIndent(TRUE);
    $plist->startDocument('1.0', 'UTF-8');
    $plist->writeDTD('plist', '-//Apple//DTD PLIST 1.0//EN', 'http://www.apple.com/DTDs/PropertyList-1.0.dtd');
    $plist->startElement('plist');
    $plist->writeAttribute('version', '1.0');

    $pkg = array();
    $pkg['kind'] = 'software-package';

    $member = M('Member');
    $uptype = $member->where(array('id' => session('homeId')))->getField('uptype');
    if ($uptype == 1) {
        $pkg['url'] = $channelid;
    } elseif ($uptype == 2) {
        $pkg['url'] = $ssl_server . 'Public/uploads/' . $channelid;
    }


    $displayimage = array();
    $displayimage['kind'] = 'display-image';
    $displayimage['needs-shine'] = TRUE;
    $displayimage['url'] = $ssl_server . 'Icon.png';

    $fullsizeimage = array();
    $fullsizeimage['kind'] = 'full-size-image';
    $fullsizeimage['needs-shine'] = TRUE;
    $fullsizeimage['url'] = $ssl_server . 'Icon.png';

    $assets = array();
    $assets[] = $pkg;
    $assets[] = $displayimage;
    $assets[] = $fullsizeimage;

    $metadata = array();
    $metadata['bundle-identifier'] = $bundle_identifier;
    $metadata['bundle-version'] = $versionname;
    $metadata['kind'] = 'software';
    $metadata['subtitle'] = $subtitle;
    $metadata['title'] = $title;

    $items0 = array();
    $items0['assets'] = $assets;
    $items0['metadata'] = $metadata;

    $items = array();
    $items[] = $items0;

    $root = array();
    $root['items'] = $items;

    xmlWriteValue($plist, $root);

    $plist->endElement();
    $plist->endDocument();

    return $plist->outputMemory();
}
?>

