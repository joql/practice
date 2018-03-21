<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/20
 * Time: 17:24
 */

//验证码识别类
//$data = 'data:image/png;charset=utf-8;base64,iVBORw0KGgoAAAANSUhEUgAAADUAAAAWCAYAAABg3tToAAAEQUlEQVRYw+2Xa1BUZRjHf4c9Liyrqx90BaYhJUxLXcQugjBGXGS5CIJG6nQzGy80eUkxTRoKCSbHWzWjDaYyEVZqcmvboXEUF5abQVBYaqaAXAIBZxDdARfsQ9OxIzSe7UszxvPtfc5z/v/3N+/7vO85wpP+oXd4wMKJBzBGoEag/sMQ71eg041hlCjS1X192OcJi2NZ8lwcev0EGhqbOHAomzOWsiEaG9auJnDuHAYHBymxVpCxYy8DA4O8tWkti+MXDNEtNBWR+v5OxR4OQS19Pp7YaCNRC5dx5478oIyJNrJxfSKZn35G/c/nCQ4KZEd6CisTN1L3Yz0AKpUTH+/JQKVSsT19FyqVCk/PhxgYGATg8yPHMBedlDTVajUf7U6nsalZsYdDUKIoEh8bRV6BeQgQQFREGBWV33MwKweAyqpq5vo9hXF+sGQYHRnOFG8vYha9SGdn1xCNltY2WlrbpHFEeAiC4MQ3piLFHg71VHjYs+h0Oo6dKBj+ZSeB/v7bslxf/23sdrs0jjSGUmKtGBZouFgUtwBreaW03ZV4OAS1JCGOYov1Hyd0qriUwAA/oiPnIwgC8wL9magfT17Bt1LN9MencflKI1s3r6P0tIlTRbksf3npsHqTJ3niY5hObp7JIQ/F28/HMINpU6ewc88+ANJTt3Hbbicl9QOp5ouvTuA1+WFSkpNIXLUcnU7H+k3b+O1yAwCjR2txdlazMCYSa1klb2zYwuxZBhJXvUpLSxvfnSyWecYvjKa94xplFWcVezi0UksS4rhw8ZK0b11cXIbUBPg/TdC8ANIydpOV/SVd3d1kbE9m6qPeALhqNABUna0hLWM3P9T+xMGsHOrPnScm2ijTUqtHERURRqGpSNa/9/NQDKWfMJ6geQHk5psQRRFRFNFqXXESBETxz8XVurqSlvo2J/JM5BeaOXo8n4Rlr3Gts4uU5CQAbtlsANTWyRv6SkMjHu5uslxYSBBarSt5BWYpp8RDMZSvrwFRVLElaR3lFjPlFjOzfQ0Yw0Mot5gRRRFvby9Ga7XU/u0E6uvro/iMlSneXjg7O9Pbe5MbN3pxm6iX6Ws0Ggn4r4iLjaLqbA3t7R1STomH4p6yllXywitrZLl339nM7+0dfJKZhd1up6enBwAP94nyVdaP59YtG/39/QBU19ThN+cJ9mceBkAQBGbOeAxLyd3L8xGvSfgYprN563syLaUeiqB6e29y4eIlWc5ms9HTc0PKX2loorKqmjUrlyMITjRdbcZ31kwWRIVz8HCO1BfZR45yYP8ekre+yeniUiKMoYzVjSE759jdu8kYSnf3dSylZfdsU2Ue94ag9H/qUOaHXG1ulZ1+Go0Lr69eQXBQIOPGjeVqcytf5xZy9Hi+7N3QkGdYteIl3N3d+PXSZXbt3Uf9uV/uTkIQ8HB3k13Cjnr8K6iRr/QRqBGo/y/UH1fM+kQoiGILAAAAAElFTkSuQmCC';



/*$ocr = new Ocr();
$data = 'data:image/png;charset=utf-8;base64,iVBORw0KGgoAAAANSUhEUgAAADUAAAAWCAYAAABg3tToAAAEQUlEQVRYw+2Xa1BUZRjHf4c9Liyrqx90BaYhJUxLXcQugjBGXGS5CIJG6nQzGy80eUkxTRoKCSbHWzWjDaYyEVZqcmvboXEUF5abQVBYaqaAXAIBZxDdARfsQ9OxIzSe7UszxvPtfc5z/v/3N+/7vO85wpP+oXd4wMKJBzBGoEag/sMQ71eg041hlCjS1X192OcJi2NZ8lwcev0EGhqbOHAomzOWsiEaG9auJnDuHAYHBymxVpCxYy8DA4O8tWkti+MXDNEtNBWR+v5OxR4OQS19Pp7YaCNRC5dx5478oIyJNrJxfSKZn35G/c/nCQ4KZEd6CisTN1L3Yz0AKpUTH+/JQKVSsT19FyqVCk/PhxgYGATg8yPHMBedlDTVajUf7U6nsalZsYdDUKIoEh8bRV6BeQgQQFREGBWV33MwKweAyqpq5vo9hXF+sGQYHRnOFG8vYha9SGdn1xCNltY2WlrbpHFEeAiC4MQ3piLFHg71VHjYs+h0Oo6dKBj+ZSeB/v7bslxf/23sdrs0jjSGUmKtGBZouFgUtwBreaW03ZV4OAS1JCGOYov1Hyd0qriUwAA/oiPnIwgC8wL9magfT17Bt1LN9MencflKI1s3r6P0tIlTRbksf3npsHqTJ3niY5hObp7JIQ/F28/HMINpU6ewc88+ANJTt3Hbbicl9QOp5ouvTuA1+WFSkpNIXLUcnU7H+k3b+O1yAwCjR2txdlazMCYSa1klb2zYwuxZBhJXvUpLSxvfnSyWecYvjKa94xplFWcVezi0UksS4rhw8ZK0b11cXIbUBPg/TdC8ANIydpOV/SVd3d1kbE9m6qPeALhqNABUna0hLWM3P9T+xMGsHOrPnScm2ijTUqtHERURRqGpSNa/9/NQDKWfMJ6geQHk5psQRRFRFNFqXXESBETxz8XVurqSlvo2J/JM5BeaOXo8n4Rlr3Gts4uU5CQAbtlsANTWyRv6SkMjHu5uslxYSBBarSt5BWYpp8RDMZSvrwFRVLElaR3lFjPlFjOzfQ0Yw0Mot5gRRRFvby9Ga7XU/u0E6uvro/iMlSneXjg7O9Pbe5MbN3pxm6iX6Ws0Ggn4r4iLjaLqbA3t7R1STomH4p6yllXywitrZLl339nM7+0dfJKZhd1up6enBwAP94nyVdaP59YtG/39/QBU19ThN+cJ9mceBkAQBGbOeAxLyd3L8xGvSfgYprN563syLaUeiqB6e29y4eIlWc5ms9HTc0PKX2loorKqmjUrlyMITjRdbcZ31kwWRIVz8HCO1BfZR45yYP8ekre+yeniUiKMoYzVjSE759jdu8kYSnf3dSylZfdsU2Ue94ag9H/qUOaHXG1ulZ1+Go0Lr69eQXBQIOPGjeVqcytf5xZy9Hi+7N3QkGdYteIl3N3d+PXSZXbt3Uf9uV/uTkIQ8HB3k13Cjnr8K6iRr/QRqBGo/y/UH1fM+kQoiGILAAAAAElFTkSuQmCC';

$ocr->init($ocr->savePicByBase54($data,'./','test.png'),[5,6,5,6,2]);
//$ocr->test();
echo 'result: '.$ocr->recognition().'<br>';*/


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
    private $feature = [
                    '001110001001100100010100001110000011000001100000110000011000011010001001001100011100',
                    '001100111011010000100001000010000100001000010000100001000010',
                    '011100100010000011000011000011000010000110000100001000010000110000111111',
                    '011100100010000011000010000010001100000010000001000001000011100010011100',
                    '000000000010000010000001000001000000100000010010010001001111111000011000001000000100',
                    '111111100000100000100000100000111110000011000001000001000001100010011100',
                    '000110001100000100000100000010011001100010100001110000011000001110001001000100011100',
                    '111110000100001000110001000010001000010001100010000100000000',
                    '001110001000101100010110001001000100011100010011010000111000001100000111000100011100',
                    '001110010011100001100001100001110001011101000001000010000110011000000000'
                ];//特征码


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

    /**
     * use for:识别
     * auth: Joql
     * @return string
     * date:2018-03-21 11:51
     */
    public function recognition(){
        echo '<img src="'.$this->pic_path.'" /><br>';

        $start=$this->fill_left;
        $result ='';
        for ($i = 0;$i < count($this->pic_count);$i++){
            $str = '';
            for ($j = 0; $j < $this->pic_hid - $this->fill_top - $this->fill_bottom; $j++){
                for ($k = $start;$k <= $this->pic_count[$i];$k++){
                    if ($this->pic_rgb[$j+$this->fill_top][$k]['red'] <= $this->max_grey && $this->pic_rgb[$j+$this->fill_top][$k]['red'] >= $this->min_grey) {
                        $str .= '0';
                    } else {
                        $str .= '1';
                    }
                }
            }
            $result .= $this->findsimilarKey($str,$this->feature);
            $start = $this->pic_count[$i]+1+$this->fill_space;
        }
        return $result;

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


    /**
     * use for:测试
     * auth: Joql
     * date:2018-03-21 11:03
     */
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
                        echo '0';
                    } else {
                        echo '1';
                    }
                }
                //echo "<br>";
            }
            $start = $this->pic_count[$i]+1+$this->fill_space;
            echo "<br>";
        }
    }

    /**
     * use for:统计字符串不同之处
     * auth: Joql
     * @param $str1
     * @param $str2
     * @return int
     * date:2018-03-21 11:53
     */
    private function compareStr($str1,$str2){
        $count = 0;
        $arr1 = str_split($str1, 1);
        $arr2 = str_split($str2, 1);
        foreach ($arr1 as $k=>$v){
            if($v != $arr2[$k]){
                $count++;
            }
        }
        return $count;
    }

    /**
     * use for:返回相似特征码键值
     * auth: Joql
     * @param $str
     * @param $arr
     * @return string
     * date:2018-03-21 13:27
     */
    private function findsimilarKey($str, $arr){
        $min =100;
        $back ='';
        foreach ($arr as $k=>$v){
            $count_diff = $this->compareStr($str, $v);
            if($count_diff <= $min){
                $min = $count_diff;
                $back = $k;
            }
        }
        return $back;
    }
}