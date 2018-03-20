<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/20
 * Time: 17:24
 */




class Ocr
{
    public $min_grey;//最小灰度阈值
    public $max_grey;//最大灰度阈值
    private $pic_path;//图片路径
    private $pic_wid;//图片宽度
    private $pic_hid;//图片高度
    private $pic_rgb;//图片rgb数组


    /**
     * use for:保存图片
     * auth: Joql
     * @param $data
     * @param $des
     * @param $filename
     * @return bool|string
     * date:2018-03-20 17:32
     */
    public function savePicByBase54($data,$des,$filename){
        if (preg_match('/^(data:\s*image\/(\w+);charset=utf-8;base64,)/', $data, $result)) {
            $type = $result[2];
            $new_file = $des.$filename.".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $data)))) {
                return $new_file;
            }
        }
        return false;
    }


    public function recognition($path){
        $this->pic_path = $path;
    }

    private function init(){
        //缺一个判断文件是否存在
        $im = imagecreatefrompng($this->pic_path);
        $size = getimagesize($this->pic_path);
        $this->pic_wid = $size['0'];
        $this->pic_hid = $size['1'];

        for ($i = 0; $i < $this->pic_hid; ++ $i) {
            for ($j = 0; $j < $this->pic_wid; ++ $j) {
                $rgb = imagecolorat($im, $j, $i);
                $this->pic_rgb[$i][$j] = imagecolorsforindex($im, $rgb);
            }
        }
    }
}