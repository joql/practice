<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/22
 * Time: 15:55
 */

//1.安装ffmpeg
//2.设置path变量
echo exec('/usr/bin/ffmpeg -i b1.mov b1.mp4',$input);
var_dump($input);
/*require '../../init.php';
set_time_limit(0);
$ffmpeg = FFMpeg\FFMpeg::create(array(
    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
    'ffprobe.binaries' => '/usr/bin/ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
));
$video  = $ffmpeg->open('b1.mov');

$video
    ->filters()
    ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
    ->synchronize();
$video
    ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
    ->save('frame.jpg');
$video
    ->save(new FFMpeg\Format\Video\X264(), 'exp.mp4');*/