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
<title>Load �Խ���</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<?
if($dpasswd==""){showmsg("�н����带 ��������.");exit;}

$passtmp = $dpasswd;
$passtmp = substr($passtmp,-2);
if(strlen($passtmp)==0)  $passtmp=$dpasswd;//��й�ȣ�� ���ڸ��� ��ü
$enc_pw = crypt($dpasswd,$passtmp);



$foundit = 0; // �ش��ڷḦ ã�Ҵ�������
$nownum = 0;  // �ۻ����� ���� ���� ����׸��� �������� �˱�����
$cnt = 0;     // �۾��߿��� ������ι�ȣ. �۾�ó���� �ѷ��ڵ��
$delmode = 0; // �׸������� ���û����� ���� �Ѵ� ���
$dlistcnt = 0;// dbindex �������� cnt
$napasswd = 0;

$comment = str_replace("|","%7C",$comment);
$comment = str_replace("\\\"","\"",$comment);

$ret = proclock();

if($ret==0)
{
	showmsg("�� �����Դϴ�. (".$ret.")");
	exit();
}

$dbnum = $dnum%100;
$dbfile = "$datafo/$dbnum.dat";
$fp = fopen("$dbfile","r");

//---------------�׸�����
if($dauth=="")
{
  while(!feof($fp))
  {
	  $record[$cnt++] = $buffer = fgets($fp, 4096);
  	$buffer = chop($buffer);

  	if(substr($buffer,0,1)==">"){ // ������ ���� �տ� '>'�� ������ �׸���
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
    fclose($fp);//dbindex ����
  }
}//�׸� ����

//-------------�ۻ���
else
{
  while(!feof($fp))
  {
    if(!file_exists($dbfile)) break;
	  $record[$cnt++] = $buffer = fgets($fp, 4096);
  	$buffer = chop($buffer);

	  if(substr($buffer,0,1)==">"){ // ������ ���� �տ� '>'�� ������ �׸���
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
}//�ۻ���

if($foundit==0){showmsg("�Խù��� ã�� �� �����ϴ�.");procunlock();exit;}
if($napasswd==1){showmsg("�ۼ��� �н����带 �������� �ʾƼ� ������ �� �����ϴ�.");procunlock();exit;}
if($delit==0){showmsg("�н����尡 Ʋ���ϴ�.");procunlock();exit;}

$totalrec = $cnt;
$cnt = 0;

$fp = fopen("$dbfile","w");
while($cnt<$totalrec)
{
	fputs($fp, $record[$cnt++]);
}
fclose($fp);//dbfile ����

procunlock();
gourl("./index.php");

?>