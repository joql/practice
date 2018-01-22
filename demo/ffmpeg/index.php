<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/22
 * Time: 15:55
 */

require '../../init.php';


$ffmpeg = FFMpeg\FFMpeg::create();
$video  = $ffmpeg->open('b1.mov');

$video
    ->filters()
    ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
    ->synchronize();
$video
    ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
    ->save('frame.jpg');
$video
    ->save(new FFMpeg\Format\Video\X264(), 'export-x264.mp4')
    ->save(new FFMpeg\Format\Video\WMV(), 'export-wmv.wmv')
    ->save(new FFMpeg\Format\Video\WebM(), 'export-webm.webm');