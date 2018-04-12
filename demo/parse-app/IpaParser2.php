<?php
/**
 * 解析Ipa plist文件
 * 
 * @author zhoushen extrembravo@gmail.com
 * @since  2014/2/14
 */
require dirname(__FILE__) . '/CFPropertyList/CFPropertyList.php';

class IpaParser{

	const INFO_PLIST = 'Info.plist';

	public function parse($ipaFile, $infoFile=self::INFO_PLIST){
		$zipObj = new ZipArchive;
		if($zipObj->open($ipaFile) !== true){
			throw new PListException("unable to open {$ipaFile} file!");
		}
		//scan plist file
		$plistFile = null;
	    for ($i=0; $i < $zipObj->numFiles; $i++) {
	    	$name = $zipObj->getNameIndex($i);
	    	if(preg_match('/Payload\/(.+)?\.app\/' . preg_quote($infoFile) . '$/i', $name)){
	    		$plistFile = $name;
	    		break;
	    	}			
	    }	    
	    //parse plist file
	    if(!$plistFile){
	    	throw new PListException("unable to parse plist file！");
	    }

	    //deal in memory
	    $plistHandle = fopen('php://memory', 'wb');
	    fwrite( $plistHandle, $zipObj->getFromName($plistFile) );
	    rewind($plistHandle);
	    $zipObj->close();
	    $plist = new CFPropertyList($plistHandle, CFPropertyList::FORMAT_AUTO);
	    $this->plistContent = $plist->toArray();

	    //get icon
        $icon = $this->plistContent['CFBundleIcons']['CFBundlePrimaryIcon']['CFBundleIconFiles'];
        if (preg_match('/\./', $icon[0])) {
            $old_icon = $d . '/' . $icon[0];
        } else {
            $ext = preg_match('/20x20/', $icon[0]) ? '@3x.png' : '@2x.png';
            $oldfile = $d . '/' . $icon[0] . $ext;
        }
	    return true;
	}

	//获取包名
	public function getPackage(){
		return $this->plistContent['CFBundleIdentifier'];
	}
	//获取版本
	public function getVersion(){
		return $this->plistContent['CFBundleVersion'];
	}
	//获取应用名称
	public function getAppName(){
		return $this->detect_encoding($this->plistContent['CFBundleDisplayName']);
	}
	//获取解析后的plist文件
	public function getPlist(){
		return $this->plistContent;
	}
	public function getIcon(){

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
