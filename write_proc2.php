<?
include "env.php";
include "mtype_plugin/db_admin.php";
include "config_data.php";
include "option_data.php";

if($chk_w != "whoareyou" || $number== "")
{
  exit("누구냐 너Get out");  //불법접근
}
else{
  $dbnum_chk = $number%100;
  $dbfile_chk = "$datafo/$dbnum_chk.dat";
  if(!file_exists($dbfile_chk))  exit("해당 로그의 DB 파일이 없습니다.");  //세부 db 파일이 아예 없다.
}

// 슬래쉬벗기기
$comment = stripslashes($comment);
$name    = stripslashes($name);
$spos = strpos($comment, "http:");

if($link_http=="off"){
  if($spos === false) {}
  else {
	  print "본 게시판의 코멘트에 http 주소 링크는 금지되었습니다.";
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
  if(strlen($temp) == 0) {  die("본 게시판에 영문 및 숫자만의 코멘트는 투고 금지되었습니다."); }
}//http 차단과 영문,숫자 덧글 차단

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
      showmsg("금지 단어가 포함되어 있습니다.: $val");
      exit();
    }
  }
}
//특정 단어 필터링

$cookiesexpire = 30*24*3600; // 30일후 만료

function updatereplyorderlist($mrnum)
{
	global $cfg_recent;
	global $datafo;

	if(file_exists("$datafo/recent.txt"))//data의 이름을 dat파일이 저장되는 폴더 이름으로 바꿔주세요
	{
		$fp = fopen("$datafo/recent.txt","r");//data의 이름을 dat파일이 저장되는 폴더 이름으로 바꿔주세요
	}
	$ocnt=0;
	if($fp)
	{
		while(!feof($fp))
		{
			$olist[$ocnt] = fgets($fp, 4096);
			if($olist[$ocnt]==$mrnum."\n")continue;
			$ocnt++;
		}

		fclose($fp);
	}

	$ototal = $ocnt;
	$ocnt=0;

	$fp = fopen ("$datafo/recent.txt", "w");//data의 이름을 dat파일이 저장되는 폴더 이름으로 바꿔주세요
	fputs($fp, $mrnum."\n");
	while($ocnt<$ototal && $ocnt<($cfg_recent-1))
	{
		fputs($fp, $olist[$ocnt++]);
	}
	fclose($fp);

}

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
if($name!="" && $usecookie=="on"){
	setcookie ("ckname", $name,time()+$cookiesexpire);
	setcookie ("ckurl", $hpurl,time()+$cookiesexpire);
	setcookie ("ckmail", $email,time()+$cookiesexpire);
	setcookie ("ckuse", $usecookie,time()+$cookiesexpire);
	if($usecookiepw=="on")setcookie ("ckpass", $passwd,time()+$cookiesexpire);
	else setcookie ("ckpass", "",time()-3600);
}
if($usecookie!="on")
{
	setcookie ("ckname", "",time()-3600);
	setcookie ("ckurl", "",time()-3600);
	setcookie ("ckmail", "",time()-3600);
	setcookie ("ckuse", "",time()-3600);
	setcookie ("ckpass", "",time()-3600);
}

?>

<html>
<head>
<title>Load 게시판</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<?

$foundit = 0;
$complete = 0;
$cnt = 0;

$passtmp = $passwd;
$passtmp = substr($passtmp,-2);
if(strlen($passtmp)==0)  $passtmp=$passwd;//비밀번호가 한자리면 교체
$enc_pw = crypt($passwd,$passtmp);
if($passwd=="")$enc_pw = "";

$comment = str_replace("|","%7C",$comment);


if($comment=="")
{
	showmsg("글 내용을 적어주세요.");
	exit();
}

if($name=="")
{
	showmsg("이름을 적어주세요.");
	exit();
}

if(strstr($name,"'")||strstr($email,"'")||strstr($hpurl,"'"))
{
	showmsg("이름,메일,홈페이지에 ' 는 사용 금지입니다.");
	exit();
}

if(strstr($name,'"')||strstr($email,'"')||strstr($hpurl,'"'))
{
	showmsg('이름,메일,홈페이지에 " 는 사용 금지입니다.');
	exit();
}


$ret = proclock();//락 시작
if($ret==0)
{
	showmsg("락 에러입니다. (".$ret.")");
	exit();
}

$dbnum = $number%100;
$dbfile = "$datafo/$dbnum.dat";
//dbfile 선정

$fp = fopen("$dbfile","r");
while(!feof($fp))
{
	$record[$cnt++] = $buffer = fgets($fp, 4096);

	if(substr($buffer,0,1)==">"){ // 라인의 제일 앞에 '>'가 있으면 그림임
		$buffer = substr($buffer,1);
		$data = explode("|", $buffer);

		$outdata = array($name,$comment,time(),$REMOTE_ADDR,$enc_pw,$kd_s,$kd_m,$kd_memo,$kd_col);

		if($foundit == 1 && $complete == 0){
			$complete = 1;
			$record[$cnt] = $record[$cnt-1];

			$strtmp = join("|",$outdata);
			$strtmp = str_replace("\n","<br>",$strtmp);
			$strtmp = str_replace("\r","",$strtmp);
			$record[$cnt-1] = $strtmp."\n";
			if(strlen($strtmp)>4000)
			{
				showmsg("경고 : 입력데이터가 4000바이트를 넘었습니다.");
				procunlock();
				exit();
			}

			$cnt++;
		}
		if($data[0]==$number)$foundit = 1;
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


procunlock();//락 해제

updatereplyorderlist($number);
gourl("./index2.php?num=$number");

?>