<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/9
 * Time: 11:10
 */

require '../../../init.php';



$names = new getName('./1.txt');
$names->getName();
$names->save();


class getName
{
    public $sorce_path;
    public $name_list;

    public function __construct($sorce_path ='./', $target_path = './')
    {
        $this->sorce_path = $sorce_path;
        $this->target_path = $target_path;
    }

    public function getName(){
        $file_str=fopen($this->sorce_path, 'r');
        $body = fread($file_str,filesize($this->sorce_path));
        fclose($file_str);

        $line = explode("\r\n", $body);
        $count_find = 0;
        foreach ($line as $v){
            $name = explode('	',$v);
            echo "get : ".$name['1']." \n";
            $this->name_list[] = $name[1];
        }
    }

    public function save(){
        //save to file
        $count = count($this->name_list);
        echo "total : $count \n";
        for($i=0;$i<$count;$i++){
            if($i != 0 && $i%100000 === 0){
                if(!empty($save)){
                    foreach ($save as $v){
                        $str .= $v."\r\n";
                    }
                    echo 'the '.($i/1000).' is save'."\n";
                    saveFile('./name-'.time().'-'.ceil($i/1000).'.txt',$str);
                    continue;
                }
                unset($save);
                unset($str);
            }else{
                $save[] = $this->name_list[$i];
            }
        }
        if(!empty($save)){
            foreach ($save as $v){
                $str .= $v."\r\n";
            }
            echo 'the '.ceil($i/1000).' is save'."\n";
            saveFile('./name-'.time().'-'.ceil($i/1000).'.txt',$str);

        }
    }
}


