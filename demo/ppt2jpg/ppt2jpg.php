<html>
<head>
    <title>ShotDev.Com Tutorial</title>
</head>
<body>
<?
error_reporting(E_ALL);
$ppApp = new COM("PowerPoint.Application");
$ppApp->Visible = True;

$strPath = realpath(basename(getenv($_SERVER["SCRIPT_NAME"]))); // C:/AppServ/www/myphp

$ppName = "test.ppt";
$FileName = "MyPP";

$powerpnt = new COM("powerpoint.application") or die("Unable to instantiate Powerpoint");
$presentation = $ppApp->Presentations->Open(realpath($ppName), false, false, false) or die("Unable to open the slide");
$i=1;

foreach($presentation->Slides as $slide)
{
    $slideName =  $i."_SS_";
    $exportFolder = realpath('./MyPP/');
    $slide->Export($exportFolder.$slideName.".jpg", "jpg");
    $ppApp->Presentations[1]->Close();
    $ppApp->quit();
}
?>
PowerPoint Created to Folder <b><?=$FileName?></b>
</body>
</html>