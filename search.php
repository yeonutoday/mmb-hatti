<?
include "env.php";
include "config_data.php";
include "option_data.php";
include "mtype_plugin/extend_lib.php";
include "mtype_plugin/db_admin.php";
include "KDM_skin_data.php";
include "KDM_fontcol_data.php";
include "KDM_tb_data.php";

header ("Pragma: no-cache");

//비공개 게시판 모드
if($mem_login=='on')
	{
		if($memberlogin == $cfg_member_passwd);
		else
			{
			gourl("./admin.php?member=1");
			exit;
			}
	}

if($memberpasswd === $cfg_member_passwd)
{
  setcookie ("memberlogin",$memberpasswd,0);
  $isMember = 1;
}
else $isMember = 0;
	// 관리자 패스워드쿠키가 있으면서 관리자암호와 같으면 관리자모드임
if($ckadminpasswd == $cfg_admin_passwd && $ckadminpasswd !="")
{
	$isAdmin = 1;
}

if($cfg_admin_passwd=="")
{
	print "관리자 패스워드가 설정되어있지 않거나 'env.php' 파일이 읽어지지 않았습니다.";
	exit();
}

$pagcnt = $logcnt = $findnum = 0;
$prev_pgnum = $next_pgnum = 0;

$num = intval($num);

$cp = fopen("option_list.php", "r");
while(!feof($cp)) {
  $first_arg = trim(fgets($cp, 4096));
  $second_arg = trim(fgets($cp, 4096));
  $option_list[$first_arg] = $second_arg;
}
fclose($cp);

reset($option_list);
  while($option_onff = each($option_list)){
  ${"img_".$option_onff["key"]} = $$option_onff["key"];
}

?>

<html>
<head>
<title><?=$title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link href="css/lightbox.css" rel="stylesheet" />
<link rel=StyleSheet HREF=./style.css type=text/css title=style>
<link rel="shortcut icon" href="./favicon.ico">
<link rel="shortcut icon" href="./favicon2.ico" type="image/x-icon">
<link rel="icon" href="./favicon2" type="image/x-icon"/>
<link rel="apple-touch-icon" href="./favicon2.ico">
<link rel="apple-touch-icon-precomposed" href="./favicon2.ico" />
<!-- 봇 검색 금지 -->
<META NAME="HATENA" CONTENT="NODIFF,NOINDEX">
<META NAME="HATENA" CONTENT="NODIFF">
<META name="ROBOTS" content="NOARCHIVE">
<meta name="robots" content="noindex, nofollow">
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW">
<style>
A:link    {color:<?=$li_fo?>;text-decoration:none;}
A:visited {color:<?=$li_fo?>;text-decoration:none;}
A:active  {color:<?=$li_fo?>;text-decoration:none;}
A:hover  {color:<?=$vi_fo?>;font-weight:bold; text-decoration:none;background-color:<?=$ac_fo?>;}

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


.button {font-family:Tahoma; font-size:7pt; letter-spacing: 0px; font-weight:bold;}

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

.menubar {text-align:center;font-weight:bold;font-size:7pt;
color:<?=$button_col?>;background-color:<?=$button_bg?>;border:1px solid <?=$button_border?>;
padding:0 4 0 4;border-radius:3px;
filter: Alpha(Opacity=<?=$tbopacity1?>);opacity:<?=$tbopacity2?>; 

.pagebar {line-height:130%;letter-spacing:0px;text-align:center;padding:0px; border-radius:30px;width:16px; height:16px;margin-bottom:5px;
color:<?=$button_bg?>;background-color:<?=$button_col?>;border:none;
position:relative; top:50px; left:-30px;filter: Alpha(Opacity=<?=$tbopacity1?>);opacity:<?=$tbopacity2?>; 
}

.pagebar a:link    {color:<?=$button_bg?>;text-decoration:none;}
.pagebar a:visited {color:<?=$button_bg?>;text-decoration:none;}
.pagebar a:active  {color:<?=$button_bg?>;text-decoration:none;}
.pagebar a:hover  {color:<?=$button_bg?>;letter-spacing:1px;}

.pagebar2 {line-height:130%;letter-spacing:0px;text-align:center;padding:0px; border-radius:30px;width:16px; height:16px;margin-bottom:5px;
color:<?=$button_col?>;background-color:<?=$button_bg?>;border:none;
position:relative; top:50px; left:-30px;filter: Alpha(Opacity=<?=$tbopacity1?>);opacity:<?=$tbopacity2?>; 
}

.pagebar2 a:link    {color:<?=$button_col?>;text-decoration:none;}
.pagebar2 a:visited {color:<?=$button_col?>;text-decoration:none;}
.pagebar2 a:active  {color:<?=$button_col?>;text-decoration:none;}
.pagebar2 a:hover  {color:<?=$button_col?>;letter-spacing:1px;}

.pagebar3 {line-height:100%;text-align:center;padding:0px; border-radius:0px;width:12px; height:12px;margin-bottom:5px;
color:<?=$button_bg?>;border:none;
position:relative; top:50px; left:-28px;filter: Alpha(Opacity=<?=$tbopacity1?>);opacity:<?=$tbopacity2?>; 
}

.pagebar3 a:link    {color:<?=$button_bg?>;text-decoration:none;}
.pagebar3 a:visited {color:<?=$button_bg?>;text-decoration:none;}
.pagebar3 a:active  {color:<?=$button_bg?>;text-decoration:none;}
.pagebar3 a:hover  {color:<?=$button_bg?>;letter-spacing:1px;}

.topbutt{text-align:center;font-weight:bold;font-size:7pt;
color:<?=$button_col?>;background-color:<?=$button_bg?>;border:1px solid <?=$button_border?>;
position:fixed!important;  width: 77px; height:16px; padding:1 0 1 0;
filter: Alpha(Opacity=<?=$tbopacity1?>);opacity:<?=$tbopacity2?>;display:none;}  
.topbutt a:link    {color:<?=$button_col?>;text-decoration:none;}
.topbutt a:visited {color:<?=$button_col?>;text-decoration:none;}
.topbutt a:active  {color:<?=$button_col?>;text-decoration:none;}
.topbutt a:hover  {color:<?=$button_bg?>;letter-spacing:1px;}
.topbutt:hover{background-color:<?=$button_col?>;color:<?=$button_bg?>;}





.max{max-width:300px;  width:expression(this.clientWidth  > 300 ? "300px" : (this.style.width.trim()  == "auto" ? "auto" : this.style.width));}

</style>
<script src="http://code.jquery.com/jquery-latest.js"></script>

 <script type="text/javascript">
   $(document).ready(function() {
    $('#pop_bt').click(function() {
     $('#pop').show();
    });
    $('#close').click(function() {
     $('#pop').hide();
    });
   });
 </script>
 
</head>

<script type="text/javascript" src="js/js_input.js" charset='utf-8'></script>
<script type="text/javascript" src="./js/FileButton.js"></script>

<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/lightbox.js"></script>

<script type="text/javascript">
var myFileButton = new FileButton("imageswap", "imagesrc");
window.onload = function () {
    //myFileButton.run();
}
</script>
<script language="javascript">
</script>
<body bgcolor="<?=$bgcol?>" style="background-image:url(<?=$bgurl?>); background-repeat:repeat; background-attachment:fixed;margin-top:10;" text="<?=$b_fo?>">

<div align = "center"><a href='./index.php'><font class=button><　BACK</font></a><br><br>
<?
print"<span class=memo><font class=non>SEARCH FOR</font>&nbsp;&nbsp;'$keyw'</span>";
?>

</div>

<?
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
	$homepage_pattern = "/([^\"\=\>])(mms|http|HTTP|ftp|FTP|telnet|TELNET|https|HTTPS)\:\/\/(.[^ \n\<\"]+)/";
	$str = preg_replace($homepage_pattern,"\\1<a href=\\2://\\3 target=_blank>\\2://\\3</a>", " ".$str);
	return $str;
}

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}

function result($fp)
{ global $isAdmin;
  global $option_ip; 
  global $kd_memocol;  global $kd_seccol;  global $kd_morecol; global $comm_ad_datecol; global $comm_cu_namebg; global $tbopacity1;
  global $kd_ipcol; global $tbopacity2;
	


  $buffer = fgets($fp, 4096);
  $buffer = chop($buffer);

  while(substr($buffer,0,1)!=">" && $buffer != ""){    // 답글 출력
    $reply = explode("|", $buffer);
    list($autname,$comment,$rtime,$repip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt) = $reply;
    // 작성자명,글내용,이멜,홈주소,등록시간,IP,패스워드

    if($comment=="")continue;
    print "<span class='adname'>$autname</span>&nbsp;";

    $comment = str_replace("%7C","|",$comment);
//    $comment = del_html($comment);
    $comment = autolink($comment);
			print "<span style='color:$comm_ad_datecol; font-family:tahoma; font-size:7pt;'>";
	print date("mdy (H:i)",$rtime)."</font></span><br>\n";

	if($kd_memo) print "<span class=memo>$kd_memo</span><br>";
	if($kd_s == 'on'){
		if($isAdmin==1)	print "<span style='color:$kd_seccol;'>$comment</span>";
		else print "<span class=gname>&nbsp;<img src='image/sc4.png' border=0>&nbsp;<font style='font-size:7pt;'>SECRET</font>&nbsp;</span>";
		print "<br><br>";
		}
	else{
		if($kd_m =='on'){
			print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=button>< CLOSE</font>': '<font class=button>MORE ></font>&nbsp;';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><font class=button>MORE ></font></a><span style=\"display: none;\">";
		}
		
		print $comment."<br>";
		if($kd_m =='on') 	print "</span><br>";

		if($option_ip == "on" && $kd_replt !='on') print "<div align=right style=font-family:Tahoma;font-size:7pt;color:$kd_ipcol;>$repip</div>\n";
	  	else print "<br>\n";
		}

    $buffer = fgets($fp, 4096);
  }

  if(!feof($fp)){
    $back = strlen($buffer);
    fseek ($fp,-$back,SEEK_CUR);
  }
  return $fp;
}

// 관리자 패스워드쿠키가 있으면서 관리자암호와 같으면 관리자모드임
if($ckadminpasswd == $cfg_admin_passwd && $ckadminpasswd !="")
{
	$isAdmin = 1;
}

$fp = fopen("$dbindex","r");
$buffer = chop(fgets($fp, 4096));
fclose($fp);
$total=intval($buffer);
if($num==0) $num=$total;

while($pagcnt<$cfg_pic_per_page && $num > 0){
  $dbnum = $num%100;
  $dbfile = "data/$dbnum.dat";

  if(!file_exists($dbfile)) continue;
  $fp = fopen("$dbfile","r");
  while(!feof($fp)){
	  $buffer = trim(fgets($fp, 4096));
   	if(substr($buffer,0,1)==">"){  // 라인의 제일 앞에 '>'가 있으면 그림임
  		$buffer = substr($buffer,1);
	  	$data = explode("|", $buffer);
  		list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;
	  	// 그림번호, 파일명, 작업시간(초), 암호화된패스워드, 툴버젼, 등록시간, 호스트네임, IP

     	if(!file_exists("data/$picfn") && $isAdmin!=1)  continue; //그림이 없고 일반이면 스킵
      $fpsav = ftell($fp);
      $intbl = 0;    
    }

  	else{  //글임
      if($intbl==1) continue;
      
      $pos = strpos($buffer, $keyw);  //search
      if ($pos !== FALSE)  // === 필수!
      {
        if($picno != $num){
        $intbl = 1;
        continue;
        }
      
        if($pagcnt++ == 0) { $prev = $picno;}
                
        $intbl = 1;
        $findnum=$picno;

        $reply = explode("|", $buffer);
        list($autname,$comment,$rtime,$repip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt) = $reply;
        if(strcmp($ip,$repip)==0 && $autname==$keyw) $logcnt++;
        //검색어가 이름이면 ip 대조로 본인그림인지까지 확인.

     	  if($pagcnt==1)	$fsttime=$rtime;
       	else $lsttime=$rtime; //현재 페이지 기간 계산

        // 작업시간을 시분초 단위로 변환
        $strjtime = sprintf("%d시간 %d분 %d초",$sec/3600,($sec/60)%60,$sec%60);
        if($sec<3600)$strjtime = sprintf("%d분 %d초",($sec/60)%60,$sec%60);
        if($sec<60)$strjtime = sprintf("%d초",$sec%60);
        if($sec<=0)$strjtime = "알 수 없음";

       	if(!file_exists("data/$picfn") && $isAdmin!=1)  continue; //그림이 없고 일반이면 스킵
       	else  $vhchoice = @GetImageSize("data/$picfn");

        print "<br><table class='tbstyle' cellpadding='0' cellspacing='0' align='center' valign='top' bgcolor='$alltb_bgc' style='border:1px solid $alltb_borc;width:$fiximgsize !important;'filter: Alpha(Opacity=$tbopacity1);opacity:$tbopacity2;' >";
        print "<tr><TD align='center' valign='top' style='padding:20 10 10 10px;'>\n";


       	if(!file_exists("data/$picfn")) echo "<center>Log<br>delete</center>"; // 관리자면 스킵않고 표시
     	  else{
          $alt = "";
          reset($option_list);
          $crt = "&#13;";
          $optcnt=0;
          while($option_onff = each($option_list)) {
            if ($optcnt>2) break;
	          $optcnt++;
            $option_key = explode("_", $option_onff["key"]);
		      	$alt = ($$option_onff["key"]=="on") ? $alt.$option_onff["value"]." : ".${$option_key[1]} : $alt;
    		    if($optcnt<3)    $alt = ($$option_onff["key"]=="on") ? $alt.$crt : $alt;
          }






      		if(!$mov) {
		  if($upcheck_s == 'on'){
			  if($isAdmin==1){
				  print "</a><div style='margin-bottom:7px;'><span class=memo>!! ADMIN ONLY !!</span></div>";
				  if($upcheck_m == 'on')
					  print "\n"; //접기있던데
				  print "<a href='$picfo/$picfn' rel='lightbox' class='over'><img src='$picfo/$picfn' style='border:none;width:$fiximgsize;' class='max' id='bn'></a>";
				  if($upcheck_m =='on') print "</div>";
			  }
			  else print "</a><br><img src='./image/lock.png' border=0 title='locked'>\n";
		  }
		  else if($upcheck_cs == 'on'){
			  if($isAdmin==1 || $logout=="on" || $isMember == 1){
				  print "</a><div style='margin-bottom:7px;'><span class=memo>!! MEMBER ONLY !!</span></div>";
				  if($upcheck_m == 'on') print "\n"; //접
				  print "<a href='$picfo/$picfn' rel='lightbox' class='over'><img src='$picfo/$picfn' style='border:none;width:$fiximgsize;' class='max' id='bn'></a>";
				  if($upcheck_m =='on') print "</div>";
			  }
			  else {
				  print "</a>";
				  member_login();
				  print "\n";
			  }
		  }
		  else{
			  if($upcheck_m =='on'){
				  print "</a>\n"; //접
			  }
			  print "<a href='$picfo/$picfn' rel='lightbox' class='over'><img src='$picfo/$picfn' style='border:none;width:$fiximgsize;' class='max' id='bn'></a></div>";
			  if($upcheck_m =='on') print "\n";
		  }
	  }
	  else {
		  print "<div align=center>";
		  if($upcheck_m =='on') {
			  print "\n"; //접기있던데
		  }
		  print "$mov";
		  if($upcheck_m =='on') print "</div>";
		  print "</div>";
	  }



        }

        print "</td><TD BORDER=0 CELLSPACING=0 CELLPADDING=0 valign='top' width=100% style='background-color:$comtb_bgc; padding-top:5;'>\n";
    	  // 만일 그림의 가로크기가 지정 크기 이상이면 리플을 그림 밑으로 표시한다.
		if(!$mov){
	  	if($vhchoice[0] < $max_width_comment) print "</td><TD height=100% style='margin:0; padding:0;'><table width=100% height=100%><tr><td valign='top' style='padding-top:22;'>";//그림td 닫고 코멘트table 시작. 코멘트 시작할 때 상단 여백 22px 줌
		  else print "</td></tr><tr><TD BORDER=0 CELLSPACING=0 CELLPADDING=0 width=100% style='padding:5px;'>\n";//이하동문
		} else print "</td></tr><tr><TD BORDER=0 CELLSPACING=0 CELLPADDING=0 width=100% style='padding:5px;'>\n";//이하동문
			
		global $mov2;
		$mov2 = $mov;
  		$intbl = 1;
        //여기까지 그림 표시
        fseek ($fp,$fpsav);
        result($fp);
     	}
    }
    
    if($intbl==1){
      print "</td></tr></table><br>\n\n";
      $intbl = 0;
    }
  }
  $num--;
}



$next = $findnum-1;

fclose($fp);
$fpsav = 0;

if($intbl==1){
  print "</td></tr></table><br>";
  $intbl = 0;
}

//next, prev 버튼
echo "<br><center><TABLE border=0><TR><TD>\n";
if($total>$prev)
{
  echo "<form name='search' method='get' action=./search.php>
  <input type=hidden name='keyw' value=$keyw>
  <input type=hidden name='num' value=$prev>
  <input type=submit name='bprev' value='PREV' class='menubar'></form>";
}

echo "</TD><TD>\n";

if($num > 0)
{
  echo "<form name='search' method='get' action=./search.php>
  <input type=hidden name='keyw' value=$keyw>
  <input type=hidden name='num' value=$next>
  <input type=submit name='bnext' value='NEXT' class='menubar'>
  </form>";
}
echo "</TD></TR></TABLE></center>\n";

?>
<div class=non align='center'>MMB &copy;TOMCAT&nbsp;/&nbsp;SKIN BY &copy;KODAMA&nbsp;&copy;<a href='http://enter421.blog.me/' target='_blank'>ESEM</a></div>

</body>
</html>
<?
function member_login()
{
	global $bgurl; global $bgcol; global $bg_fo; global $li_fo; global $ac_fo; global $beu_bt_fontcolor; global $beu_bt_col; global $beu_bt_bodercol;
echo "
<form name='member' method='post'>
        <div align='center'><br><input type='password' name='memberpasswd' style='width:100px; height:17px; text-align:center; font-size:7pt; color:$beu_bt_fontcolor; background-color:$beu_bt_col; border: 1px solid $beu_bt_bodercol'><input type='submit' name='submit2' value='  ENTER  ' style='width:80px;height:17px;font-family:Tahoma;font-size:7pt;font-weight:bold;letter-spacing:2px;padding:0 0 2 0; color:$beu_bt_fontcolor; background-color:$beu_bt_col; border:1px solid $beu_bt_bodercol'>
</form>";
}?>