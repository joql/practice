<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/20
 * Time: 17:24
 */




$ocr = new Ocr();
$data = 'data:image/png;charset=utf-8;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAWCAYAAACCAs+RAAAEEElEQVRYw+2XeVCUdRjHP9LKrGyscsWAIWgZhAg4HLpgqKOGAo0c4xHKpBI1OggkgswaSs5EeJJpaMUQgYGggVlESqWBhEE6cV8eRXgAC84sLJuwQH80bawLI8vkP8b3v+ec+Ty/93l/7zvJVbJsiCdAejwhmgCZAHlMEowWyMtJx8pquobv5q3fWbv+dQ2fWGzIZIGAzq77I/YxMppGwtsxeEjcWRMcyq3fWgDw83mZ4HVB2Fhb0SHr5FRuPtk5eeq6hZ7zCdscwnOzZtIhk5FxMpf8Lwt0BwE4X3SR3DNn1XZ3d49WzqtrA1nltwJf/2CGhjRfgPPdXUiIj8XYaJqG/0W7F4ja9iYZJ3NobL6Bm+s8tkduQS7vpqCwCK+FEg4kJZCVk8eHJ9KQLHBDujMKhULBhe8u6Q7S3t5BVXXd6FMQCAhc5cvZc4VaEI5z7fngcCLFJWWUlF4hXhqtjtU3NLEqKARFby8AP5dfxc3FmWVLF1FQWMSV8l+Ilb7Dj8U/AVBecQ03F2cC/H3HB/IoeS9fglgs5nTeOa1YVXUdMXEJFF8uw1PirhX/B0K9rHp6KHuVAPT19ash/h2qDAsL88ez7OvWBHCpuBSZrHPEePHlskf2EBkYsHa1PzbWVmQN25GHNXPmDFpb74xvR7xe8sB35XJEIhG/VlbzblIyd++1AeDk6ICd7WwOJqcAkLh3F/0qFXv27hvzIFxdnDl+9AAPHvRx6P0UamrrR817drolx1JSdQf56pvzDKgGqKyuw/wZUyLC32D/e3sI2bRVfRqNTdeprKoBQCgU0t/To9OJNjQ2syksgjn2tkRHbcXwaREZn+dq5OjrT2Zn9DYqq2r5/mKJ7iCffpatYQuFQuKl0VhbW6HsVbLYy5ODyccQCP5uIRIZoFAoEAgEqFSqMYH09Cioqa2nprYeG+sZhIWGaIHEbA/HxMSYiO3S8d0jD+teWzsAZqYmmJgYIxA8RVxMJHExkRp5K7yXIvFaOWaY4f2FQiFisSFyeTcAQQF++Pl4ExktVT/SOoNMmSJEqfxTbc+xt2VwcJCWllYaGpvZsHGLRn5CfCz32to58XH6IyH09SdjZmrK7Tt31T4Hezu6uu6rIVzmObHjrXD2HzpKecW18d3snhJ3du/aQWbWaZqv32T287PY/Fow3174gfYOGQCNTdc1apRKJXJ5t5Z/JIVu3ECgvy8fpWbQ8kcrkgVuLF7kyeEjxwGYbmnBvsTdlFdc5cbNWzjOtVfX1tY1MDAwODaQ0rJy0jNPERTwCpYW5nR2dnEy+wypaZn/yXfRJ2mZDA4NErJ+NWamJty+fZekA0f4Iv9rAJydHJg6VYyHxB2PYXeQSjXAwiU+I/acNPFjNQEyAfL/APkLjaiFzNcigzQAAAAASUVORK5CYII=';

$ocr->init($ocr->savePicByBase54($data,'./','test.png'),[5,6,5,6,2]);
$ocr->test();


class Ocr
{
    public $min_grey=10;//最小灰度阈值
    public $max_grey=190;//最大灰度阈值
    private $pic_path;//图片路径
    private $pic_wid;//图片宽度
    private $pic_hid;//图片高度
    private $pic_rgb;//图片rgb数组
    private $pic_count;//字母数
    private $fill_top;//上边距
    private $fill_left;//左边距
    private $fill_bottom;//下边距
    private $fill_right;//右边距
    private $fill_space;//字母间隔


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

    }

    /**
     * use for:初始化
     * auth: Joql
     * @param $path
     * @param $parm [5,6,5,6,2]
     * @return bool
     * date:2018-03-20 22:08
     */
    public function init($path, $parm){
        if(count($parm) !=5 || !is_writable($path)){
            return false;
        }
        $this->fill_top = $parm[0];
        $this->fill_right = $parm[1];
        $this->fill_bottom = $parm[2];
        $this->fill_left = $parm[3];
        $this->fill_space = $parm[4];
        $this->pic_path = $path;

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

        for($i=$this->pic_wid-$this->fill_left;$i>$this->fill_right-1;$i--){
            for($j=$this->fill_top,$count=0;$j<$this->pic_hid-$this->fill_bottom;$j++){
                if($this->pic_rgb[$j][$i]['red'] <= $this->max_grey && $this->pic_rgb[$j][$i]['red'] >= $this->min_grey && $this->pic_rgb[$j][$i-1]['red'] <= $this->max_grey && $this->pic_rgb[$j][$i-1]['red'] >=$this->min_grey){
                    $count++;
                }
                if($count > $this->pic_hid -$this->fill_top - $this->fill_bottom -1){
                    $this->pic_count[] = $i- $this->fill_space;
                    $i = $i -2;
                }
            }
        }
        $this->pic_count = array_reverse($this->pic_count);

        unset($count);
        $count =0;
        for ($j= $this->fill_top;$j<$this->pic_hid-$this->fill_bottom;$j++ ){
            if($this->pic_rgb[$j][$this->pic_wid - $this->fill_right]['red'] <= $this->max_grey && $this->pic_rgb[$j][$this->pic_wid - $this->fill_right]['red'] >= $this->min_grey){
                $count++;
            }
        }
        if($count >$this->pic_hid -$this->fill_top - $this->fill_bottom -1){
            $this->pic_count[] = $this->pic_wid - $this->fill_right -1;
        }else{
            $this->pic_count[] = $this->pic_wid - $this->fill_right;
        }
        return true;
    }


    public function test(){

        echo '<img src="'.$this->pic_path.'" /><br>';
        echo 'h: '.$this->pic_hid.', w: '.$this->pic_wid.'<br>';

        for ($i = 0; $i < $this->pic_hid; $i ++) {
            for ($j = 0; $j < $this->pic_wid; $j ++) {
                if ($this->pic_rgb[$i][$j]['red'] <= $this->max_grey && $this->pic_rgb[$i][$j]['red'] >= $this->min_grey) {
                    echo '□';
                } else {
                    echo '■';
                }
            }
            echo "<br>";
        }
        echo '-----------------------------------------------------------------<br>';
        for ($i = $this->fill_top; $i < $this->pic_hid-$this->fill_bottom; $i ++) {
            for ($j = $this->fill_right; $j < $this->pic_wid-$this->fill_right+1; $j ++) {
                if ($this->pic_rgb[$i][$j]['red'] <= $this->max_grey && $this->pic_rgb[$i][$j]['red'] >= $this->min_grey) {
                    echo '□';
                } else {
                    echo '■';
                }
            }
            echo "<br>";
        }
        echo '------------------------------------------------------------------<br>';
        $start=$this->fill_left;

        for ($i = 0;$i < count($this->pic_count);$i++){
            echo "第".($i+1)."个字<br>";
            for ($j = 0; $j < $this->pic_hid - $this->fill_top - $this->fill_bottom; $j++){
                for ($k = $start;$k <= $this->pic_count[$i];$k++){
                    if ($this->pic_rgb[$j+$this->fill_top][$k]['red'] <= $this->max_grey && $this->pic_rgb[$j+$this->fill_top][$k]['red'] >= $this->min_grey) {
                        echo '□';
                    } else {
                        echo '■';
                    }
                }
                echo "<br>";
            }
            $start = $this->pic_count[$i]+1+$this->fill_space;
            echo "<br>";
        }
    }
}