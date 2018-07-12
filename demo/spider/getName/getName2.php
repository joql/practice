<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/9
 * Time: 11:10
 */

require '../../../init.php';


ini_set('memory_limit', '-1');

echo 'run start';
$names = new getName('./1.txt');
$names->getName();
$names->save();


class getName
{
    public $sorce_path;
    public $name_list;
    public $page_max = 100000;

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
            if(mb_strlen($name[1],'utf8') < 6){
                continue;
            }
            preg_match_all("/[\x{4e00}-\x{9fa5}]/u",$name[1],$match);
            if(count($match[0]) < 3){
                continue;
            }
            $count_find ++ ;
            echo "get : id ".$count_find.', name '.$name['1']." \n";
            $this->name_list[] = $name[1];
            unset($match);
        }
    }

    public function save(){
        //save to file
        $count = count($this->name_list);
        echo "total : $count \n";
        for($i=0;$i<$count;$i++){
            if($i != 0 && $i%$this->page_max === 0){
                if(!empty($save)){
                    foreach ($save as $v){
                        $str .= $v."\r\n";
                    }
                    echo 'the '.($i/$this->page_max).' is save'."\n";
                    saveFile('./name-'.time().'-'.ceil($i/$this->page_max).'.txt',$str);
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
            echo 'the '.ceil($i/$this->page_max).' is save'."\n";
            saveFile('./name-'.time().'-'.ceil($i/$this->page_max).'.txt',$str);

        }
    }
}


