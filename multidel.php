<?
include "env.php";
include "config_data.php";


function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}

function showmsg($msg)
{
	echo "</head>\n";
	echo "<body bgcolor=\"#FFFFFF\" text=\"#333333\">\n";
	echo $msg."\n";
	echo "</body><html>";
}

function alt($msg='') {
echo "<script language='javascript'>";
if($msg) echo 'alert("'.$msg.'");';
echo "</script>";
}

if($ckadminpasswd == $cfg_admin_passwd && $ckadminpasswd !="")
{
	$isAdmin = 1;
}

if($isAdmin!=1){
	echo("
	<html>
	<head>
	<title>Load 게시판</title>
	<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
	<meta http-equiv='refresh' content='0; url=admin.php'>
	</head></html>
	");
	exit;
}// 관리자 패스워드쿠키가 있으면서 관리자암호와 같으면 관리자모드임

////////////////////////////////// 여기부터 mutidel
$ret = proclock();  //lock
if($ret==0)
{
	showmsg("락 에러입니다. (".$ret.")");
	exit();
}
//----dbindex에서 삭제,정렬 후 기록

$dlistcnt=0;
$cnt=0;

for($j=0;$j<count($delpic);$j++){
  $fp = fopen("$dbindex","r");
  while(!feof($fp))
  {
    $dblist[$dlistcnt++] = $buffer =fgets($fp, 4096);
   	$buffer = chop($buffer);
    if($buffer==$delpic[$j])  $dlistcnt--; //dbindex에서 삭제
  }
  fclose($fp);

  $fp = fopen("$dbindex","w");
  while($cnt<$dlistcnt)
  {
	  fputs($fp, $dblist[$cnt++]);
  }
  fclose($fp);
}

//----dbfile에서 그림 삭제,정렬 후 기록

for($j=0;$j<count($delpic);$j++){
  $totalrec=0;
  $cnt=0;
  $dbnum = $delpic[$j]%100;
  $dbfile = "$datafo/$dbnum.dat";

  echo "dbfile = $dbfile<br>\n";

  if(!file_exists($dbfile)) break;

  $fp = fopen("$dbfile","r");
  while(!feof($fp))
  {
	  $record[$totalrec++] = $buffer = fgets($fp, 4096);
  	$buffer = chop($buffer);

  	if(substr($buffer,0,1)==">"){ // 라인의 제일 앞에 '>'가 있으면 그림임
	  	$delmode = 0;
		  $buffer = substr($buffer,1);
  		$data = explode("|", $buffer);
  		list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;

		  if($picno==$delpic[$j])
  		{
	  		@unlink("$picfo/".$picfn);
//		  	$record[$i]="";
        $totalrec--;
			  $delmode = 1;
  		}
  	}
	  else if($delmode==1)$totalrec--;
  }
  fclose($fp);

  $fp = fopen("$dbfile","w");
  while($cnt<$totalrec)
  {
	  fputs($fp, $record[$cnt++]);
  }
  fclose($fp);
}

//----dbfile에서 리플 삭제,정렬 후 기록

for($j=0;$j<count($delreply);$j++){
  $totalrec=0;
  $cnt=0;

 	$delexp = explode("|", $delreply[$j]);
 	list($delnum,$deldat) = $delexp;

  $dbnum = $delnum%100;
  $dbfile = "$datafo/$dbnum.dat";
  if(!file_exists($dbfile)) break;

  $fp = fopen("$dbfile","r");
  while(!feof($fp))
  {
	  $record[$totalrec++] = $buffer = fgets($fp, 4096);
  	$buffer = chop($buffer);

	  if(substr($buffer,0,1)!=">"){ // 라인의 제일 앞에 '>'가 있으면 그림임
	  	$data = explode("|", $buffer);
		  list($autname,$comment,$rtime,$ip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col) = $data;
  		if($comment=="")continue;

  		if($ip.$rtime==$deldat)
	  	{
		  	$totalrec--;
  		}
  	}
  }
  fclose($fp);

  $fp = fopen("$dbfile","w");
  while($cnt<$totalrec)
  {
	  fputs($fp, $record[$cnt++]);
  }
  fclose($fp);
}

echo "그림 : ".count($delpic)."개<br>\n";
for($i=0;$i<count($delpic);$i++)echo"$delpic[$i]<br>\n";
echo "<br>\n";

echo "글 : ".count($delreply)."개<br>\n";
for($i=0;$i<count($delreply);$i++)echo"$delreply[$i]<br>\n";

procunlock();
gourl("./index.php");
?>