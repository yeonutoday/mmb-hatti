<?
include "env.php";
include "option_data.php";
include "config_data.php";
include "mtype_plugin/extend_lib.php";
include "mtype_plugin/db_admin.php";
include "KDM_skin_data.php";
include "KDM_fontcol_data.php";
include "KDM_tb_data.php";

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


if($ckadminpasswd == $cfg_admin_passwd && $ckadminpasswd !="")
{
	$isAdmin = 1;
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

function showmsg($msg)
{
	echo "</head>\n";
	echo "<body bgcolor=\"#FFFFFF\" text=\"#333333\">\n";
	echo $msg."\n";
	echo "</body><html>";
}


$ckname = stripslashes($ckname);
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
    if($picno==$num) { $nowpic=$picfn;}
  }
  else{ //글일때
    if($picno==$num){
      if(substr($buffer,0,1)!=">") // 라인의 제일 앞에 '>'가 있으면 그림임
      {
        $data = explode("|", $buffer);
        list($autname,$comment,$rtime,$ip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt) = $data;
        if($comment=="")continue;
        // 작성자명,글내용,이멜,홈주소,등록시간,IP,패스워드
        
        // 수정할 리플 값 취득
        if($time==$rtime) { // mod.php에서 받은 작성 시간
        $mdata = $buffer;
        $mdata = explode("|", $buffer);
        list($mname,$mcom,$mtime,$mip,$mpasswd,$mkd_s,$mkd_m,$mkd_memo,$mkd_col,$mkd_replt) = $mdata;
        $mcom = str_replace("<br>","\n",$mcom);
        }
        // 여기까지
        // 비밀글 처리
        if($mkd_s == "on" && $isAdmin != "1") {
        showmsg("비밀글은 관리자만 답글을 달 수 있습니다.");
        exit();
        }
        
        
		if($kd_col == 'on')
			$old_name[] = "$ad_ico<font style='color:$comm_ad_namecol;'><b>$autname&nbsp;</b></font\n"; 
		else 
			$old_name[] = "<font style='color:$comm_cu_namecol;'><b>$autname</b></font>\n";

		print "</font>";
		
        $comment = str_replace("%7C","|",$comment);
        $comment = autolink($comment);
        if($wreply_emo == 'on') $comment = emote_ev($comment, $emote_table);
        else  $comment = emote_invi($comment, $emote_table);


		if($kd_col == 'on'){ 
			$old_comment = "<font style='background-color:$comm_ad_datebgcol; color:$comm_ad_datecol; font-face:굴림체; font-size:7pt;'>";
     	    $old_comment .= date(" Y.m.d",$rtime)."&nbsp;</font>\n<br>";
				}
					
			else {
				$old_comment = "<font style='font-family:Tahoma; font-size:7pt; filter; letter-spacing:0; color:$comm_cu_datecol;'>";
        $old_comment .= date("m.d",$rtime)."&nbsp;</font>\n<br>";

			}
		if($kd_s == 'on' && $isAdmin!=1 ){
		$old_comment .= "<span style='color:$kd_seccol;'>Secret</span>";
		}
		else if($kd_m =='on'){

		$old_comment .= "<a class=\"more\" onclick=\"this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'>More ▼</span></b></a><div style=\"display: none;\">$comment<br></div>";
		}




		else{	
		if($kd_s == 'on' && $isAdmin ==1 ) 
		$old_comment .= "<span style='color:$kd_seccol;'>$comment</span>";
		else if($kd_col == 'on') 
		$old_comment .= "<span style='color:$comm_ad_fontcol;'>$comment</span>";	
		else
		$old_comment .= $comment."\n";
		if($kd_s == 'on') print "</span>";
		}


        if($option_ip == "on") {
          $old_comment .= "<div align=right style=font-family:Tahoma;font-size:7pt;>$ip</div>\n";
        }
        $old_comment .= "\n";
        $old_comments[] = $old_comment;
        if($isAdmin==1) $old_comment .= "<br>".$admin_com."n";
      }
    }
    else continue;
  }
}
fclose($fp);

?>

<html>
<head>
<title><?=$title?> ;RE reply</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel=StyleSheet HREF=./style.css type=text/css title=style>
<link rel="stylesheet" media="only screen and (-webkit-device-pixel-ratio: 0.75)" type="text/css" href="mstyle.css"  />
<link rel="stylesheet" media="only screen and (-webkit-min-device-pixel-ratio:1.5)" type="text/css" href="mstyle.css"/>
<link rel="stylesheet" media="only screen and (-webkit-min-device-pixel-ratio: 2)" type="text/css" href="mstyle.css"/>
<meta name="HandheldFriendly" content="True" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=yes" />
<style>
A:link    {color:<?=$li_fo?>;text-decoration:none;}
A:visited {color:<?=$li_fo?>;text-decoration:none;}
A:active  {color:<?=$li_fo?>;text-decoration:none;}
A:hover  {color:<?=$vi_fo?>;font-weight:bold; background-color:<?=$ac_fo?>;}

::selection { background:<?=$select_bg?>; color:<?=$select_col?>; text-shadow: none; }
::-moz-selection { background:<?=$select_bg?>; color:<?=$select_col?>; text-shadow: none; }

::-webkit-scrollbar {
width: 5px;height: 5px;}
::-webkit-scrollbar-button:start:decrement,
::-webkit-scrollbar-button:end:increment {
height: 6px;display: block;background-color:<?=$scroll_bar?>;}
::-webkit-scrollbar-track-piece {
background-color:<?=$scroll_shadow?>;}
::-webkit-scrollbar-thumb:vertical {
height: 9px;background-color:<?=$scroll_bar?>;border-top:1px solid <?=$scroll_shadow?>;border-bottom:1px solid <?=$scroll_shadow?>;}

.memo {
font-size:9pt;font-family:Nanum Gothic;
color:<?=$kd_memocol?>;background-color:<?=$kd_memobg?>;font-weight:bold;
padding:1 2 1 2;border-radius:2px;}


.adname {
font-size:9pt;font-family:Nanum Gothic;
color:<?=$comm_ad_namecol?>;background-color:<?=$comm_ad_namebg?>;font-weight:bold;letter-spacing:-1px;
padding:1 2 1 2;border-radius:2px;}


.gname {
font-size:9pt;font-family:Nanum Gothic;
color:<?=$comm_cu_namecol?>;background-color:<?=$comm_cu_namebg?>;font-weight:bold;letter-spacing:-1px;
padding:1 2 1 2;border-radius:2px;}


.cmtb {width:500px;filter: Alpha(Opacity=<?=$tbopacity1?>);opacity:<?=$tbopacity2?>;}

</style>
</head>
<script type="text/javascript" src="js/js_input.js" charset='utf-8'></script>
<body bgcolor="<?=$bgcol?>" style="background-image:url(<?=$bgurl?>); background-repeat:repeat; background-attachment:fixed;" text="<?=$b_fo?>">
<div align="center"><a href="./index.php"><font class="button"><　BACK</font></a></div>
<form name="write" method="post" action="reply_proc.php">
  <p align="center">
<? 
if($upcheck_s == 'on') {
				
				if($isAdmin==1)	{print "\n";

if($upcheck_m =='on') print "</div>";}
				else print "\n";
				print "\n";
				}

		else if($upcheck_cs == 'on')
			{
				if($isAdmin==1 || $logout=="on" || $isMember == 1){	print "\n";

if($upcheck_m =='on') print "</div>";}
				else print "\n";
				print "\n";
}


 ?>
</p>

<TABLE class='cmtb' style='border:1px solid <?=$co_w_textborder?>;padding:5px;color:<?=$comm_ad_fontcol?>; background-color:<?=$co_w_textbox?>;' CELLSPACING='0' CELLPADDING='5' align='center'>




<tr><TD BORDER=0 CELLSPACING=0 CELLPADDING=0>
<?
if($mkd_col == 'on')	print "<font class='adname'>";
else print "<font class='gname'>";
print "$mname</font>&nbsp;";

if($mkd_col == 'on'){ 
			print "<font size='1' title='$altdate'>";
			print "<span style='color:$comm_ad_datecol; font-family:tahoma;'>";
			print date("mdy (H:i)",$mtime)."&nbsp;</span></font><br>";
		} else {
			print "<font size='1' title='$altdate'>";
			print "<span style='color:$comm_cu_datecol; font-family:tahoma;'>";
			print date("mdy (H:i)",$mtime)."&nbsp;</span></font><br>";
		}
if($mkd_memo) print "<span class='memo'>$mkd_memo</span>&nbsp;";
if($mkd_s == 'on'){
	if($mkd_m =='on'){
				print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=button>< CLOSE</font>': '<font class=button>MORE ></font>&nbsp;';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><font class=button>MORE ></font></a><span style=\"display: none;\">";
			}
			if($isAdmin==1)	print "<span style='color:$kd_seccol;'>$mcom</span>";
			if($mkd_m =='on') print "</div>";
			print "<br><br>\n";
		}

		else{
			if($mkd_m =='on'){
				print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=button>< CLOSE</font>': '<font class=button>MORE ></font>&nbsp;';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><font class=button>MORE ></font></a><span style=\"display: none;\">";
			}
			if($mkd_col == 'on') {
				if($kd_replt == 'on') print "<font style='color:$reply_text;'>";
				else print "<font style='color:$comm_ad_fontcol;'>";
			}
			else print "<font style='color:$comm_cu_fontcol;'>";
			print " <span style='color:$reply_text;border:none; width:100%;'>$mcom</span>";
			print "</font>";
			
		if($mkd_m =='on') 	print "</div>";
		}
?>

</font>
</td></tr>

</font>
</table><br>


<table class='cmtb' cellspacing='0' cellpadding='0'  style='border:1px solid <?=$co_w_textborder?>;background-color:<?=$co_w_textbox?>;padding:10;' align='center'>
<tr><td>
<table width="100%" border="0"   bgcolor="<?=$co_w_textbox?>">


	  <? if($isAdmin!=0){ ?>
  <tr>
<td border='0'width="85%">
         <input type="text" name="kd_memo" size="15" style="color:<?=$co_w_txfontcol?>; border:none; background-color:<?=$co_m_textbox?>;border-bottom:2px solid <?=$co_m_textborder?>; width:100%;">
       </td>
<td  align="left" valign="bottom" width="15%"><span style="font-size:7pt; color:<?=$b_fo?>;"></span></td>

    </tr>
	<? } ?>





<tr>
            <td border='0' width="85%" >
 <textarea name="comment" cols="20" rows="6"  style=" color:<?=$co_w_txfontcol?>; background-color:<?=$co_w_textbox?>; border:none; width:100%; overflow:visible;" ></textarea>

</td>


<td border='0' width="15%">
<input type="submit" name="Submit" value="WRITE" style="width:100%; height:80px; font-family:tahoma; font-size:7pt; font-weight:bold; letter-spacing: 2px; color:<?=$co_w_submit_fontcol?>; border:none; background-color:<?=$co_w_submit?>;">
          <input type="hidden" name="number" value="<?=$num?>">
          <input type="hidden" name="name" value="<?=$mname?>">
          <input type="hidden" name="time" value="<?=$mtime?>">
		  <? if($isAdmin != "1") echo "<input type=\"hidden\" name=\"f1\" value=\"$mf1\">"; ?>
          <input type="hidden" name="chk_w" value="whoareyou">

</td>
    </tr>
</table>
</td>
</tr>

        <td border='0' width="100%"><font style="color:<?=$b_fo?>;">

<? 
if($isAdmin==1)
{ 
print "<input type='text' name='name' size='6' value='$ckname' style='color:$co_w_namefcol; background-color:$co_w_namebox; border:none;'>&nbsp;";
print "<input type='checkbox' class='checkbox2' name='usecookie' value='on'  if($ckuse==\"on\")echo checked ><font class='checkbox'>&nbsp;cookie</font>";
print "<input type='checkbox' class='checkbox2' name='kd_col' checked style='display:none'>";
}
else {print "<input type='text' name='name' size='6' value='$ckname' style='color:$co_w_namefcol; background-color:$co_w_namebox; border:none;'><input type='password' name='passwd' size='6' style='color:$co_w_namefcol; background-color:$co_w_namebox; border:none;' value='$ckpass' >&nbsp;";
print "<input type='checkbox' class='checkbox2' name='usecookiepw' value='on' if($ckpass!=\"\")echo checked ><font class='checkbox'>&nbsp;pw</font>";
print "<input type='checkbox' class='checkbox2' name='usecookie' value='on'  if($ckuse==\"on\")echo checked ><font class='checkbox'>&nbsp;cookie</font>";
}
?>


<input type="checkbox" class="checkbox2" name="kd_m" <? if($mkd_m == "on") echo "checked"; ?>><font class='checkbox'>&nbsp;more</font>
<input type="checkbox" class="checkbox2" name="kd_s" <? if($mkd_s == "on") echo "checked"; ?>><font class='checkbox'>&nbsp;<img src='image/sc3.png' border='0'></font>
<input type="checkbox" class="checkbox2" name="kd_replt" checked style="display:none">
</font>
</td>
    </tr>

</table>

    <p>&nbsp;</p>

</form>
</body>
</html>