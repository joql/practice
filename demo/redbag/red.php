<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/2/9
 * Time: 11:06
 */

//生成随机红包记录

$arr_red = red_bag(300,100);

var_dump($arr_red);
echo array_sum($arr_red);
function red_bag($amount,$num){
    $result = array();
    while ($num){
        $num--;
        //计算红包金额 start
        $min=0.01;//每个人最少能收到0.01元
        $safe_total=($amount-$num*$min)/$num;//随机安全上限
        $my_money=mt_rand($min*100,$safe_total*100)/100;
        if(0==$num){$my_money=$amount-$my_money;$my_money=sprintf("%.2f",$my_money);}
        $amount -= $my_money;
        // end
        //$db->query("insert into {$tablepre}redbag_tmp_list (redbag_id,money)values('$id','$my_money')");
        $result[] = $my_money;
    }
    return $result;
}
