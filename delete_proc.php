<?
include "env.php";



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
?>

<html>
<head>
<title>Load 게시판</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<?
if($dpasswd==""){showmsg("패스워드를 적으세요.");exit;}

$passtmp = $dpasswd;
$passtmp = substr($passtmp,-2);
if(strlen($passtmp)==0)  $passtmp=$dpasswd;//비밀번호가 한자리면 교체
$enc_pw = crypt($dpasswd,$passtmp);



$foundit = 0; // 해당자료를 찾았는지여부
$nownum = 0;  // 글삭제시 현재 글이 몇번그림의 리플인지 알기위해
$cnt = 0;     // 작업중에는 현재라인번호. 작업처리후 총레코드수
$delmode = 0; // 그림삭제시 리플삭제를 위해 켜는 모드
$dlistcnt = 0;// dbindex 삭제위한 cnt
$napasswd = 0;

$comment = str_replace("|","%7C",$comment);
$comment = str_replace("\\\"","\"",$comment);

$ret = proclock();

if($ret==0)
{
	showmsg("락 에러입니다. (".$ret.")");
	exit();
}

$dbnum = $dnum%100;
$dbfile = "$datafo/$dbnum.dat";
$fp = fopen("$dbfile","r");

//---------------그림삭제
if($dauth=="")
{
  while(!feof($fp))
  {
	  $record[$cnt++] = $buffer = fgets($fp, 4096);
  	$buffer = chop($buffer);

  	if(substr($buffer,0,1)==">"){ // 라인의 제일 앞에 '>'가 있으면 그림임
	  	$delmode = 0;
		  $buffer = substr($buffer,1);
  		$data = explode("|", $buffer);
  		list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;
  		if($picno==$dnum)
  		{
	  		if($pass=="")$napasswd = 1;
		  	$foundit = 1;
  			if($pass==$enc_pw)
	  		{
		  		$cnt--;
			  	$delit = 1;
				  $delmode = 1;
  				unlink("$picfo/".$picfn);
	  		}
		  }
  	}
	  else if($delmode==1)$cnt--;
  }
  fclose($fp);

  if($delit==1){
  	$fp = fopen("$dbindex","r");
	  while(!feof($fp))
  	{
      $dblist[$dlistcnt++] = $buffer =fgets($fp, 4096);
   	  $buffer = chop($buffer);
      if($dnum==$buffer){
          $dlistcnt--;
      }
    }
  	fclose($fp);

    $totalrec = $dlistcnt;
    $dlistcnt = 0;

    $fp = fopen("$dbindex","w");
    while($dlistcnt<$totalrec)
    {
 	    fputs($fp, $dblist[$dlistcnt++]);
    }
    fclose($fp);//dbindex 쓰기
  }
}//그림 삭제

//-------------글삭제
else
{
  while(!feof($fp))
  {
    if(!file_exists($dbfile)) break;
	  $record[$cnt++] = $buffer = fgets($fp, 4096);
  	$buffer = chop($buffer);

	  if(substr($buffer,0,1)==">"){ // 라인의 제일 앞에 '>'가 있으면 그림임
		  $buffer = substr($buffer,1);
  		$data = explode("|", $buffer);
  		$nownum = $data[0];
	  }
  	else
  	{
	  	$data = explode("|", $buffer);
		  list($autname,$comment,$rtime,$ip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col) = $data;

  		if($comment=="")continue;

  		if($nownum==$dnum && $autname==$dauth && $rtime==$dtime)
	  	{
		  	if($passwd=="")$napasswd = 1;
			  $foundit = 1;
	  		if($passwd==$enc_pw)
		  	{
				  $cnt--;
			  	$delit = 1;
  			}
	  	}
  	}
  }
  fclose($fp);
}//글삭제

if($foundit==0){showmsg("게시물을 찾을 수 없습니다.");procunlock();exit;}
if($napasswd==1){showmsg("작성시 패스워드를 지정하지 않아서 삭제할 수 없습니다.");procunlock();exit;}
if($delit==0){showmsg("패스워드가 틀립니다.");procunlock();exit;}

$totalrec = $cnt;
$cnt = 0;

$fp = fopen("$dbfile","w");
while($cnt<$totalrec)
{
	fputs($fp, $record[$cnt++]);
}
fclose($fp);//dbfile 쓰기

procunlock();
gourl("./index.php");

?>