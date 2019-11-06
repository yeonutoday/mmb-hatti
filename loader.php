<?
//header("Content-Type: image/png");
include_once "env.php";

$dbnum = $num%100;
$dbfile = "$datafo/$dbnum.dat";

$fp = fopen("$dbfile","r");
while(!feof($fp))
{
  if(!file_exists($dbfile)) break;
	$buffer = fgets($fp, 4096);
	$buffer = chop($buffer);

	if(substr($buffer,0,1)==">") // 라인의 제일 앞에 '>'가 있으면 그림임
	{
		$buffer = substr($buffer,1);
		$data = explode("|", $buffer);
		list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;

		if($picno==$num)
		{
			$fn = "./$picfo/$picfn";
			break;
		}
	}
}
fclose($fp);

//$fp = fopen ($fn, "r");
//fre
header("Location: $fn");
?>