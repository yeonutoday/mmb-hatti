<?
include "env.php";
include "mtype_plugin/db_admin.php";
include "config_data.php";
include "option_data.php";

if($chk_w != "whoareyou" || $number== "")
{
  exit("������ ��Get out");  //�ҹ�����
}
else{
  $dbnum_chk = $number%100;
  $dbfile_chk = "$datafo/$dbnum_chk.dat";
  if(!file_exists($dbfile_chk))  exit("�ش� �α��� DB ������ �����ϴ�.");  //���� db ������ �ƿ� ����.
}

// �����������
$comment = stripslashes($comment);
$name    = stripslashes($name);
$spos = strpos($comment, "http:");

if($link_http=="off"){
  if($spos === false) {}
  else {
	  print "�� �Խ����� �ڸ�Ʈ�� http �ּ� ��ũ�� �����Ǿ����ϴ�.";
  	exit;
  }
}
if($en_num=="on"){
  $temp = eregi_replace("[[:alnum:]]+", "", $comment);
  $temp = str_replace(" ", "", $temp);
  $temp = str_replace("!", "", $temp);
  $temp = str_replace(".", "", $temp);
  $temp = str_replace("/", "", $temp);
  $temp = str_replace("=", "", $temp);
  $temp = str_replace(",", "", $temp);
  $temp = str_replace("~", "", $temp);
  $temp = str_replace(";", "", $temp);
  $temp = str_replace("\n", "", $temp);
  $temp = str_replace("\r", "", $temp);
  $temp = str_replace("\t", "", $temp);
  if(strlen($temp) == 0) {  die("�� �Խ��ǿ� ���� �� ���ڸ��� �ڸ�Ʈ�� ���� �����Ǿ����ϴ�."); }
}//http ���ܰ� ����,���� ���� ����

$blkdb = "$datafo/blockw_data.txt";
$fp = fopen("$blkdb","r");
while(!feof($fp))
{
  $blklist[] = chop(fgets($fp, 4096));
}
fclose($fp);
reset($blklist);

if($blklist[0]!=""){
  while(list ($key, $val) = each($blklist)){
    if(strstr($comment,$val)){
      showmsg("���� �ܾ ���ԵǾ� �ֽ��ϴ�.: $val");
      exit();
    }
  }
}
//Ư�� �ܾ� ���͸�

$cookiesexpire = 30*24*3600; // 30���� ����

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head><body link=\"#ffffff\"></html>";
}

function showmsg($msg)
{
	echo "</head>\n";
	echo "<body bgcolor=\"#FFFFFF\" text=\"#333333\" link=\"#ffffff\">\n";
	echo $msg."\n";
	echo "</body><html>";
}
if($name!="" && $usecookie=="on"){
	setcookie ("ckname", $name,time()+$cookiesexpire);
	setcookie ("ckuse", $usecookie,time()+$cookiesexpire);
	if($usecookiepw=="on")setcookie ("ckpass", $passwd,time()+$cookiesexpire);
	else setcookie ("ckpass", "",time()-3600);
}
if($usecookie!="on")
{
	setcookie ("ckname", "",time()-3600);
	setcookie ("ckuse", "",time()-3600);
	setcookie ("ckpass", "",time()-3600);
}

?>

<html>
<head>
<title>Load �Խ���</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<font color="#ffffff">
<?

$foundit = 0;
$complete = 0;
$cnt = 0;     // �۾��߿��� ������ι�ȣ. �۾�ó���� �ѷ��ڵ��

$passtmp = $passwd;
$passtmp = substr($passtmp,-2);
if(strlen($passtmp)==0)  $passtmp=$passwd;//��й�ȣ�� ���ڸ��� ��ü
$enc_pw = crypt($passwd,$passtmp);
if($passwd=="")$enc_pw = "";

$comment = str_replace("|","%7C",$comment);


if($comment=="")
{
	showmsg("�� ������ �����ּ���.");
	exit();
}


$ret = proclock();//�� ����
if($ret==0)
{
	showmsg("�� �����Դϴ�. (".$ret.")");
	exit();
}

$dbnum = $number%100;
$dbfile = "$datafo/$dbnum.dat";
//dbfile ����

$fp = fopen("$dbfile","r");
while(!feof($fp))
{
	$record[$cnt++] = $buffer = fgets($fp, 4096);

	if(substr($buffer,0,1)!=">"){ // ������ ���� �տ� '>'�� ������ �׸���
		$buffer = substr($buffer,1);
		$data = explode("|", $buffer);

		$outdata = array($name,$comment,time(),$REMOTE_ADDR,$enc_pw,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt); // �� �ڸ�Ʈ ������ ����, �ð��� ���� �ð��� �ƴϰ� mod.php���� ���� ������

		if($data[2] == $time) { // �̸��� �ð��� ��ġ�ϸ�

		  $data[3] = rtrim($data[3]);

	
			
		   $strtmp = join("|",$outdata);
           $strtmp = str_replace("\n","<br>",$strtmp);
           $strtmp = str_replace("\r","",$strtmp);
           $record[$cnt] = $strtmp."\n";
           if(strlen($strtmp)>4000)
           {
           	showmsg("��� : �Էµ����Ͱ� 4000����Ʈ�� �Ѿ����ϴ�.");
           	procunlock();
           	exit();
           }
           
           $cnt++;
		}

	}

}

if($foundit == 1 && $complete == 0){
	$strtmp = join("|",$outdata);
	$strtmp = str_replace("\n","<br>",$strtmp);
	$strtmp = str_replace("\r","",$strtmp);
	$record[$cnt] = $strtmp."\n";
	$cnt++;
}

fclose($fp);
$totalrec = $cnt;
$cnt = 0;

$fp = fopen("$dbfile","w");
while($cnt<$totalrec )
{
	fputs($fp, $record[$cnt++]);
}
fclose($fp);


procunlock();//�� ����

gourl("./index.php?num=$number");

?>