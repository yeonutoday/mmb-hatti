<?
include "env.php";
include "option_data.php";
include "config_data.php";
include "mtype_plugin/extend_lib.php";
include "mtype_plugin/db_admin.php";



header ("Pragma: no-cache");

if(!is_writable("$datafo")) {
 	die("◈ $datafo 폴더 기록불가 상태.<br />퍼미션을 777로 변경하시기 바랍니다.<br /><br />");
}
if(!file_exists($dbindex)){
  die("MMB $BBS_VERSION 신규 설치를 확인합니다. 관리자 로그인 뒤, 환경설정을 먼저 끝마쳐 주세요.");
}
if(!is_writable($dbindex)) {
  die("◈ $dbfile 파일 이 없거나 퍼미션이 666이 아닙니다.  FTP로 확인하시기 바랍니다.<br /><br />");
}

//비공개 게시판 모드
if($mem_login=='on'){
  if($memberlogin == $cfg_member_passwd);
  else{
    gourl("./admin.php?member=1");
    exit;
  }
}

/*
if($reple_mode=='on'){
  gourl("./admin.php");
  exit;
}//리플 권한제어 모드
*/

if(($ckadminpasswd != $cfg_admin_passwd || $ckadminpasswd =="") && $reple_mode=='on'){
  gourl("./admin.php");
  exit;
}//리플 권한제어 모드

function del_html($str)
{
	$str = str_replace( ">", "&gt;",$str );
	$str = str_replace( "<", "&lt;",$str );
	$str = str_replace( "\"", "&quot;",$str );
	$str = str_replace( "&lt;br&gt;","<br>",$str); //br은되게함
	return $str;
}

function autolink($str)
{
	// URL 치환
	$homepage_pattern = "/([^\"\=\>])(mms|http|HTTP|ftp|FTP|telnet|TELNET)\:\/\/(.[^ \n\<\"]+)/";
	$str = preg_replace($homepage_pattern,"\\1<a href=\\2://\\3 target=_blank>\\2://\\3</a>", " ".$str);
	return $str;
}

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}

$ckname = stripslashes($ckname);
$icn_hp =  "<img src='image/homepage.gif' border='0'>";
$emowidth = $cfg_emolist*72; //사용하시는 이모티콘의 가로 사이즈가 클 경우 곱셈 값을 올리세요.
$dbnum = $num%100;
$dbfile = "$datafo/$dbnum.dat";

$fp = fopen("$dbfile","r");
//dbfile 선정

while(!feof($fp))
{
  if(!file_exists($dbfile)) break;

  $buffer = fgets($fp, 4096);
 	$buffer = chop($buffer);

	if(substr($buffer,0,1)==">"){ // 라인의 제일 앞에 '>'가 있으면 그림임
		$buffer = substr($buffer,1);
		$data = explode("|", $buffer);
		list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;
		// 그림번호, ff파일명, 작업시간(초), 암호화된패스워드, 툴버젼, 등록시간, 호스트네임, IP
    if($picno==$num)  $nowpic=$picfn;
  }
  else{ //글일때
    if($picno==$num){
      if(substr($buffer,0,1)!=">") // 라인의 제일 앞에 '>'가 있으면 그림임
      {
        $data = explode("|", $buffer);
        list($autname,$comment,$rtime,$ip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt) = $data;
        if($comment=="")continue;

        // 작성자명,글내용,이멜,홈주소,등록시간,IP,패스워드
        if($email!="") $old_name[] = "<a href=\"mailto:$email\" target=\"_blank\">$autname</a>\n";
        else $old_name[] = "$autname\n";
        if($hpurl!=""){
        if(stristr($hpurl,"http://")==false)$hpurl = "http://".$hpurl;
        $old_hp[] = "<a href=\"$hpurl\" target=\"_blank\">$icn_hp</a>\n";
        } else $old_hp[] = "";

        $comment = str_replace("%7C","|",$comment);
        $comment = del_html($comment);
        $comment = autolink($comment);
        if($wreply_emo == 'on') $comment = emote_ev($comment, $emote_table);
        else  $comment = emote_invi($comment, $emote_table);

        $old_comment = "<font color=\"669999\" size=\"2\">";
        $old_comment .= date("Y/m/d(D) H:i:s",$rtime)."</font>\n<br><br>";
        $old_comment .= $comment."\n";
        if($option_ip == "on") {
          $old_comment .= "<div align=right style=font-family:Tahoma;font-size:7pt;color:#888888;>$ip</div>\n";
        }
        $old_comment .= "<br>\n";
        $old_comments[] = $old_comment;
      }
    }
    else continue;
  }
}
fclose($fp);

?>

<html>
<head>
<title>글작성하기</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel=StyleSheet HREF=./style.css type=text/css title=style>
</head>

<body background="" bgcolor="#ffffff" text="#333333" link="#6699aa" vlink="#6699aa" alink="#6699aa">

</body>
</html>


<?
gourl("./index.php");
?>