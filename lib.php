<?
@extract($_COOKIE);

if(sizeof($_GET)) { $result = $_GET; $result_gp = "g"; }
else { $result = $_POST; $result_gp = "p"; }

reset($result);
while($temp = each($result)) {
  if($result_gp == "g") {
    $$temp["key"] = $_GET[$temp["key"]];
  }

  else if($result_gp == "p") {
    $$temp["key"] = $_POST[$temp["key"]];
  }
}



function getfilename($num)
{
	global $datafo;
	global $picfo;

$dbnum = $num%100;
$dbfile = "$datafo/$dbnum.dat";
$fp = fopen("$dbfile","r");

while(!feof($fp))
{
  if(!file_exists($dbfile)) die("$dbfile 이 없습니다.");
	$buffer = fgets($fp, 4096);
	$buffer = chop($buffer);

	if(substr($buffer,0,1)==">") // 라인의 제일 앞에 '>'가 있으면 그림임
	{
		$buffer = substr($buffer,1);
		$data = explode("|", $buffer);
		list($picno,$picfn,$pass,$rtime,$ip,$loadAdmin,$loadFold,$loadMember,$mov,$loadWidth,$loadHeight,$loadWidthWide) = $data;

		if($picno==$num)
		{
			$fn = "./$picfo/$picfn";
			break;
		}
	}
}
fclose($fp);

return $fn;
}

function getpicsize($imgname)
{
	$fp = fopen ($imgname, "r");
	$buf = fread($fp, filesize($imgname));
	fclose($fp);

	if(substr($buf, 1, 3)=="PNG")
	{
		$whpos = strpos($buf, "IHDR")+4;
		if($whpos>4)
		{
			$tmpint = substr($buf,$whpos,8);
			$wh = ord($tmpint[2])*0x1000000 + ord($tmpint[3])*0x10000;
			$wh += ord($tmpint[6])*0x100 + ord($tmpint[7]);
		}
		return $wh;
	}
	else if(substr ($buf, 6, 4)=="JFIF")
	{
		$whpos = findjpgsize($buf);
		$tmpint = substr($buf,$whpos,4);
		$wh = ord($tmpint[2])*0x1000000 + ord($tmpint[3])*0x10000;
		$wh += ord($tmpint[0])*0x100 + ord($tmpint[1]);
		return $wh;
	}

	return 0;
}

function isSGTFile($imgname)
{
	$fp = fopen ($imgname, "r");
	$buf = fread($fp, filesize($imgname));
	fclose($fp);
//print $buf;
	if(strpos($buf,"Escargot"))return true;
	return false;
}

function isBTFile($imgname)
{
	$fp = fopen ($imgname, "r");
	$buf = fread($fp, filesize($imgname));
	fclose($fp);
//print $buf;
	if(strpos($buf,"iWTM"))return true;
	return false;
}

function findjpgsize($data)
{
	$i=0;

	while(true)
	{
		if(ord($data[$i])==0xff && ord($data[$i+1])==0xc0)return $i+5;
		$i++;
	}
}

?>