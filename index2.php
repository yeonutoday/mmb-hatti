<?
include "env.php";
include "lib.php";
include "config_data.php";
include "option_data.php";
include "mtype_plugin/extend_lib2.php";
include "mtype_plugin/db_admin.php";
include "KDM_skin_data.php";
include "KDM_fontcol_data.php";
include "KDM_tb_data.php";


header ("Pragma: no-cache");

$ad_ico = "<img src='$ad_icon' border='0' onerror=\"this.style.display='none';\">";
$maxleng_w = strlen($max_width);
$maxleng_h = strlen($max_height);
$emowidth = $cfg_emolist*72; //사용하시는 이모티콘의 가로 사이즈가 클 경우 곱셈 값을 올리세요.

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

print "\n";

if(file_exists($dbindex)){
//------M타입 페이지바 계산

  $res=readlock(); //env. 현재 쓰기중이 아닌 상태에서만 read.
  if($res != 1)
  {
	  print"락 해제에 실패하였습니다.";
  	exit();
  }

  if($pagebar_type=="on"){
    $temp_to = 0;
    $fp = fopen ("$dbindex", "r");
    if($fp){
      while(!feof($fp)) {
        $buffer = trim(fgets($fp, 4096));
        if ($buffer!=""&&(!($temp_to%$cfg_pic_per_page) || $temp_to==0)) { $page_arr[]=$buffer; }
        $temp_to++;
      }
      $total = $page_arr[0];
      fclose($fp);
    }// 언제든지삭제될수있음
  
    $num = intval($num);
    if($num==0)$num=$total;
    $topnum = 0;
  
    $page_bar = mmb_page_bar($num, $cfg_bar_per_page, $page_arr);
  }
//------mmb1 페이지바 계산
  else{
    $fp = fopen ("$dbindex", "r");
    if($fp){
      $buffer = chop(fgets($fp, 4096));
      $total = $buffer;
      fclose($fp);
    }// 언제든지삭제될수있음

    $num = intval($num);
    if($num==0)$num=$total;
    $topnum = 0;
  }
}
?>

<html>
<head>
<title><?=$title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link href="css/lightbox.css" rel="stylesheet" />
<link rel=StyleSheet HREF=./style.css type=text/css title=style>

<link rel="stylesheet" media="only screen and (-webkit-device-pixel-ratio: 0.75)" type="text/css" href="style.css"  />
<link rel="stylesheet" media="only screen and (-webkit-min-device-pixel-ratio:1.5)" type="text/css" href="style.css"/>
<link rel="stylesheet" media="only screen and (-webkit-min-device-pixel-ratio: 2)" type="text/css" href="style.css"/>
<meta name="HandheldFriendly" content="True" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=yes" />

<style>
* {-webkit-text-size-adjust:none;} 

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

.button{font-family:Tahoma; font-size:7pt;letter-spacing: 1px; font-weight:bold;}

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

.menub {text-align:center;font-weight:bold;font-size:7pt;
color:<?=$button_col?>;background-color:<?=$button_bg?>;border:1px solid <?=$button_border?>;
height:16px; padding:2 5 2 5;
}

.menub a:link    {color:<?=$button_col?>;text-decoration:none;}
.menub a:visited {color:<?=$button_col?>;text-decoration:none;}
.menub a:active  {color:<?=$button_col?>;text-decoration:none;}
.menub a:hover  {color:<?=$button_col?>;letter-spacing:2px;}

.pagebar {line-height:50%;letter-spacing:0px;text-align:center;padding:2 6 2 6px; border-radius:50px;margin-right:5px;
color:<?=$button_bg?>;background-color:<?=$button_col?>;border:none; 
}

.pagebar a:link    {color:<?=$button_bg?>;text-decoration:none;}
.pagebar a:visited {color:<?=$button_bg?>;text-decoration:none;}
.pagebar a:active  {color:<?=$button_bg?>;text-decoration:none;}
.pagebar a:hover  {color:<?=$button_bg?>;letter-spacing:1px;}

.pagebar2 {line-height:50%;letter-spacing:0px;text-align:center;padding:2 6 2 6px; border-radius:50px;margin-right:5px;
color:<?=$button_col?>;background-color:<?=$button_bg?>;border:none;
}

.pagebar2 a:link    {color:<?=$button_col?>;text-decoration:none;}
.pagebar2 a:visited {color:<?=$button_col?>;text-decoration:none;}
.pagebar2 a:active  {color:<?=$button_col?>;text-decoration:none;}
.pagebar2 a:hover  {color:<?=$button_col?>;letter-spacing:1px;}
</style>
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
<body bgcolor="<?=$alltb_bgc?>" text="<?=$b_fo?>">

<!---------전체 테이블 생성-->
<table width='100%' border="0" cellpadding="0" cellspacing="0" align="center" valign="top" bgcolor="<?=$alltb_bgc?>">
<!---------↑↑↑↑↑얘가 전체테이블 임시로 보더1줌 아래는 상단테이블소스-->

	<tr><td align="center" valign="bottom" height="1">
		<table width="100%" valign="bottom" cellpadding="0" cellspacing="0">
			<tr><td width="20%" valign="top">
				
			</td></div>
			<td height="1" width="80%" valign="top" align="center">
				<table width="100%" valign="top">
					<tr><td align="right" valign="top">
						
					</td>
					<td height="1" width="130" align="center" valign="top" style="padding-center:0;">
						
					</td></tr>
				</table>
			</td></tr>
		</table>

<!----↓↓↓공지~동영상 업로드 부분은 아래의 소스를 수정함으로서 따로 모양을 낼 수 있습니다. 현재는 메뉴td와 같은 속성 적용중 :)-->
		

<table style="text-align:center;"><tr><td>
<div align="center">
<? if($img_mode=="off" || $isAdmin==1) { ?>
<form name="form1" method=post action="upload.php" enctype="multipart/form-data" style="margin:0; padding:0;">
					<? { ?>
						<input type='file' size='4' name='userfile' style="height:18px; width:130px; font-size:7pt; color:<?=$beu_bt_fontcolor?>; border:1px solid <?=$beu_bt_bodercol?>; text-align:center; background-color:<?=$beu_bt_col?>;">
						<?
						if($isAdmin==1) print "<input type='password' name='passwd' value='$ckadminpasswd' style='display:none;' >";
						else print "<input type='password' name='passwd' size='8' style='font-size:7pt; color:<?=$beu_bt_fontcolor?>; border-style:none; text-align:center; background-color:<?=$beu_bt_col?>; display:none;' value='$ckpass'>"; ?>
<input type='submit' name='submit' size=5  value="UPLOAD" style="height:18px; font-family:Tahoma; font-size:7pt;  letter-spacing:-1px; color:<?=$beu_bt_fontcolor?>; background-color:<?=$beu_bt_col?>; border:1px solid <?=$beu_bt_bodercol?>;">
					<? } ?> <br>
					<input type='checkbox' name='upcheck_m' class='checkbox3'>&nbsp;<font class=checkbox>▼</font>
						<input type='checkbox' name='upcheck_s' class='checkbox3'>&nbsp;<img src='image/sc3.png' border=0>
						<input type='checkbox' name='upcheck_cs' class='checkbox3'>&nbsp;<img src='image/sc2.gif' border=0>	

					</form>
									<? if($isAdmin==1){ ?>
					<form name="dong" method=post action="mupload.php" enctype="multipart/form-data" style="margin:0; padding:0;">
						
<input type="text" name="mov" style=" width:100px;height:17px; color:<?=$beu_bt_fontcolor?>; text-align:center;font-family:Tahoma; font-size:7pt; background-color:<?=$beu_bt_col?>; border:1px solid <?=$beu_bt_bodercol?>;">
						<?
						if($isAdmin==1) print "<input type='password' name='passwd' value='$ckadminpasswd' style='display:none;'>";
						else print "<input type='password' name='passwd' size='10' style='font-size:7pt; color:<?=$beu_bt_fontcolor?>; border:none; text-align:center; background-color:<?=$beu_bt_col?>;display:none;' value='$ckpass'>";
						?><input type='submit' size=4 name='submit' value="tag" style="height:17px; padding: 0 5 1 5; font-family:Tahoma; font-size:7pt; color:<?=$beu_bt_fontcolor?>; background-color:<?=$beu_bt_col?>; border:1px solid <?=$beu_bt_bodercol?>;">
					</form>
<br>
				<? } ?>

	<? } else print "\n" ?> 
<?
						if ($isAdmin==1) {
							print"<span class='menub'><a href='./index2.php'>REFRESH</a></span>&nbsp;
<span class='menub'><a href = './admin_config.php'>ADMIN</a></span>&nbsp;
<span class='menub'><a href = './kd_config.php'>SKIN OPT</a></span>&nbsp;
<span class='menub'><a href='./admin.php?logout=on'>LOGOUT</a></span>&nbsp;";
						} else {
							echo "<br>
<span class='menub'><a href='./index2.php'>REFRESH</a></span>&nbsp;
<span class='menub'><a href='./admin.php'>LOGIN</a></span>&nbsp;";
						}


{


$fp = fopen("$dbindex","r");
if($fp)

$page_count = 0;
$dbbrk=0;
$cnt=0;

while(!feof($fp))
{
  $buffer = fgets($fp, 4096);
  $lognum[$cnt++] = $buffer= chop($buffer);
  
  if($num==0) $num=$buffer;
  if($topnum==0)$topnum=$buffer;
	if($buffer > $num){
    $cnt--;
    continue;
  } // 아직 번호에 도달하지못하면 스킵

  $page_count++;
  if($page_count > $cfg_pic_per_page) break;
}



if($dbbrk==0 && $fp) fclose($fp);
else "echo $dbfile 이 서버에 없습니다.\n";

if($pagebar_type=="on"){

  echo "\n";
  if($topnum>$num)
  {
	  $prev=$num+$cfg_pic_per_page;
  	if($prev>$topnum)$prev=$topnum;
	  echo "<span class='menub'><a href=\"./index2.php?num=$prev\" onfocus='this.blur()'><font class=non><</font></a></span>";

  }
  if($num>$cfg_pic_per_page)
  {
	  $next=$num-$cfg_pic_per_page;
  	echo "<span class='menub'><a href=\"./index2.php?num=$next\" onfocus='this.blur()'><font class=non>></font></a></span>";
  } 

  echo "\n";
}
else{
  echo "<div align = 'right' style='margin-right:20px;'>";
  if($topnum>$num)
  {
	  $prev=$num+$cfg_pic_per_page;
  	if($prev>$topnum)$prev=$topnum;
	  echo " <a href=\"./index.php?num=$prev\" onfocus='this.blur()'><font class=non><　</font></a>";
  }
  if($num>$cfg_pic_per_page)
  {
	  $next=$num-$cfg_pic_per_page;
  	echo " <a href=\"./index.php?num=$next\" onfocus='this.blur()'><font class=non>　></font></a> ";
  }
 echo "</div>";}//페이지바
}
?>

			
				
				
		
		
		
		
	</div><!--F5~페이지바까지 div 닫음-->
	</td></tr></table><br>
</td></tr>

<?



//--------------------비툴테이블 시작
print "<td style='padding:0;'>";//메인(로그+코멘트)테이블

if(!file_exists($dbindex)){
  die("MMB $BBS_VERSION 신규 설치를 확인합니다. 관리자 로그인 뒤, 환경설정을 먼저 끝마쳐 주세요.");
}

$intbl = 0; // 테이블이 열려있는지 여부
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

//--------dbindex에서 pixcnt 추출

$fp = fopen("$dbindex","r");
if($fp)

$page_count = 0;
$dbbrk=0;
$cnt=0;

while(!feof($fp))
{
  $buffer = fgets($fp, 4096);
  $lognum[$cnt++] = $buffer= chop($buffer);
  
  if($num==0) $num=$buffer;
  if($topnum==0)$topnum=$buffer;
	if($buffer > $num){
    $cnt--;
    continue;
  } // 아직 번호에 도달하지못하면 스킵

  $page_count++;
  if($page_count > $cfg_pic_per_page) break;
}
fclose($fp);


//--------dbdata 출력 시작

for($cnt=0;$cnt<$cfg_pic_per_page;$cnt++){

  $dbnum = $lognum[$cnt]%100;
  $dbfile = "$datafo/$dbnum.dat";
  //dbfile 선택

  if(!file_exists($dbfile)){  $dbbrk=1; continue;  }
  $fp = fopen("$dbfile","r");
  while(!feof($fp))
  {
	  $buffer = fgets($fp, 4096);
  	$buffer = chop($buffer);

	  if(substr($buffer,0,1)==">") // 라인의 제일 앞에 '>'가 있으면 그림임
  	{
	  	if($intbl==1)
  		{
			$intbl = 0;
		}
  		$buffer = substr($buffer,1);
	  	$data = explode("|", $buffer);
		  list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;
  		// 그림번호, 파일명, 암호화된패스워드, 등록시간, 호스트네임, IP

   		if($picno!=$lognum[$cnt])
		{
   		  continue; //lognum가 dbfile의 picno 와 다르면 같을때까지 스킵
   		}
    	if(!file_exists("$picfo/$picfn"))continue; // 그림이 없으면 스킵

    	// 작업시간을 시분초 단위로 변환
  		$strjtime = sprintf("%d시간 %d분 %d초",$sec/3600,($sec/60)%60,$sec%60);
	  	if($sec<3600)$strjtime = sprintf("%d분 %d초",($sec/60)%60,$sec%60);
		if($sec<60)$strjtime = sprintf("%d초",$sec%60);
  		if($sec<=0)$strjtime = "알 수 없음";
	  	$vhchoice = @GetImageSize("$picfo/$picfn");

		//--------------------------그림테이블

print "<TABLE border='0' style='border:none;' width='100%' align='center' CELLSPACING='0' CELLPADDING='0'>";

if($isAdmin==1)
				{
print "<tr><td align=right style='padding:0px' colspan='2'>";
echo("
					<form name='mdel' method='post' action='multidel.php'>
					<input type='submit' name='Submit' value='del' style='font-size:7pt;color:$comm_morecol;background-color:$comm_morebgcol;border:1px solid $comm_moreborder;' >
</td></tr>
					");
				}
	if(!$mov){
  		if($vhchoice[0] < 1) print "<tr><TD align='center' valign='middle' width='$vhchoice[0]' rowspan='2' style='padding-top:0; padding-bottom:10;'>\n";//그림td임
	  	else print "<tr><TD align='center' width='100%' style='padding-top:0; padding-bottom:10;'>\n";//이하동문
	} 	else print "<tr><TD align='center' width='100%' style='padding-top:0; padding-bottom:10;'><div class='video-container'>";//이하동문

     


  		
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
		  print "<div style='width:100%' >";
		  if($upcheck_s == 'on'){
			  if($isAdmin==1){
				  print "</a><div style='margin-bottom:7px;'><span class=memo>!! ADMIN ONLY !!</span></div>";
				  if($upcheck_m == 'on')
					   print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=menub>CLOSE</font>': '<font class=menub>OPEN</font>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='line-height:130%;'><font class=menub>OPEN</font></span></a><div style=\"display: none;\">";
				  print "<img src='$picfo/$picfn' style='border:none;' class='max2'></a>";
				  if($upcheck_m =='on') print "</div>";
			  }
			  else print "</a><img src='./image/lock.png' border=0 title='locked'>\n";
		  }
		  else if($upcheck_cs == 'on'){
			  if($isAdmin==1 || $logout=="on" || $isMember == 1){
				  print "</a><div style='margin-bottom:7px;'><span class=memo>!! MEMBER ONLY !!</span></div>";
				  if($upcheck_m == 'on')  print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=menub>CLOSE</font>': '<font class=menub>OPEN</font>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='line-height:130%;'><font class=menub>OPEN</font></span></a><div style=\"display: none;\">";
				  print "<img src='$picfo/$picfn' style='border:none;' class='max2'></a>";
				  if($upcheck_m =='on') print "</div>";
			  }
			  else {
				  print "</a>";
				  member_login();
				  print "";
			  }
		  }
		  else{
			  if($upcheck_m =='on'){
				  print "</a><a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=menub>CLOSE</font>': '<font class=menub>OPEN</font>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='line-height:130%;'><font class=menub>OPEN</font></span></a><div style=\"display: none;\">";
			  }
			  print "<img src='$picfo/$picfn' style='border:none;' class='max2'></a>";
			  if($upcheck_m =='on') print "</div>";
		  }
	  }
	  else {
		  print "<div align=center>";
		  if($upcheck_m =='on') {
			   print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=menub>CLOSE</font>': '<font class=menub>OPEN</font>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='line-height:130%;'><font class=menub>OPEN</font></span></a><div style=\"display: none;\">";
		  }
		  print "$mov</div>";
		  if($upcheck_m =='on') print "</div>";
		  print "</div>";
	  }
		
		  print "<div align=right>";
		



  		/* //이건 다음버전 기능 :D
		if($reple_mode=="off" || $isAdmin==1) {
	  		print "<a href='picmod.php?num=$picno'><font size=1>M</font></a> ";
  		}*/
		if($restrict_del != "on")
		print "\n";
		{ 
		if($isAdmin!=1) print "\n";
		else  print "<input type='checkbox' name='delpic[]' class='checkbox3' value='$picno'>\n";
		}

  		// 만일 그림의 가로크기가 지정 크기 이상이면 리플을 그림 밑으로 표시한다.
		if(!$mov){
	  	if($vhchoice[0] < 1) print "</td><TD height=100% style='margin:0; padding:0;'><table width=100% height=100%><tr><td valign='top' style='padding-top:0;'>";//그림td 닫고 코멘트table 시작.
		  else print "</td></tr><tr><TD BORDER=0 CELLSPACING=0 CELLPADDING=0 width=100% align=center>\n";//이하동문
		} else print "</td></tr><tr><TD BORDER=0 CELLSPACING=0 CELLPADDING=0 width=100% align=center >\n";//이하동문
			
		global $mov2;
		$mov2 = $mov;
  		$intbl = 1;
  	}

	  else //글임ym
  	{
	  	if($intbl!=1)continue;
		  $data = explode("|", $buffer);
  		list($autname,$comment,$rtime,$ip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt) = $data;
  		// 작성자명,글내용,등록시간,IP,패스워드
  		if($comment=="")continue;


		if($kd_col == 'on'){
			if($kd_replt == 'on') print "<div align='left' style='background:$repl_bgcol; padding:0 0 0 5; margin-bottom:17;margin-left:10; border-left:5px solid $repl_border;'><span class=adname>";//답글 테이블
			else print "<div align='left' style='background:$comm_adbgcol; padding:0; margin-bottom:17; border:none;'><span class=adname>";

		}
		else {
			if($kd_replt == 'on') print "<div align='left' style='background:$repl_bgcol;padding:0 0 0 5; margin-bottom:17;margin-left:10; border-left:5px solid $repl_border;'><span class=gname>";//답글 테이블의 좌우 여백을 없애고 싶으면 margin의 20을 0으로 해주세요:)
			else print"<div align='left' style='background:$comm_cuscol; padding:0; margin-bottom:17; border:none;'><span class=gname>";
		}
		if($kd_s == 'on'){if($isAdmin==1)	{ 
				print "<img src='image/sc4.png' border=0>&nbsp;";}
				else print "&nbsp;<img src='image/sc4.png' border=0>&nbsp;<span class=hide>";}



		$autname = emote_ev($autname, $emote_table);
		print "$autname</font></span></span>";
		
  		$comment = str_replace("%7C","|",$comment);
	  	//$comment = del_html($comment);
		$comment = autolink($comment);
  		$comment = emote_ev($comment, $emote_table);

		

		$autname=urlencode($autname);//유니코드 해결

		if($kd_memo) print "&nbsp;<span class=memo>$kd_memo</span>";

		
if($kd_col == 'on'){ 

			print "&nbsp;<span style='color:$comm_ad_datecol; font-family:tahoma; font-size:7pt;'>";
			print date("mdy (H:i)",$rtime)."</span></font>&nbsp;";
		} else {
			print "&nbsp;<span style='color:$comm_cu_datecol; font-family:tahoma; font-size:7pt;'>";
			print date("mdy (H:i)",$rtime)."</span></font>&nbsp;";
		}

if($restrict_del == "on" && $isAdmin !="1"){ print "";}
		else{
			if($kdreply_mode != "on" || $isAdmin =="1"){if ($kd_replt != 'on'){
				echo "<span style='color:$b_fo;font-size:7pt;'></span><a href=\"reply1.php?num=$picno&name=$autname&time=$rtime\">";
				if($kd_col == 'on') print "<span style='color:$b_fo;font-size:7pt;'>re</span></a>";
				else print "<span style='color:$b_fo;font-size:7pt;'>re</span></a>";
				}
			}
			else {
			print "\n";	
			}
		
		echo "";
		if($kd_col == 'on') {
			if($isAdmin==1) print "\n<a href=\"mod.php?num=$picno&name=$autname&time=$rtime\"><span style='color:$b_fo;font-size:7pt;'>＋</span></a>\n";
			else print "\n";
		}
		else print "\n<a href=\"mod.php?num=$picno&name=$autname&time=$rtime\"><span style='color:$b_fo;font-size:7pt;'>＋</span></a>\n";
		if($isAdmin!=1){
			echo "<a href=\"delete.php?num=$picno&name=$autname&time=$rtime\">";
			if($kd_col == 'on') print "</a>\n";
			else print "<span style='color:$b_fo;font-size:7pt;'>－</span></a>\n";
		}
		else print "<input type='checkbox' class='checkbox2' name='delreply[]' align='right' value='$picno|$ip$rtime'>\n";
		}

print"<br>";
		if($kd_s == 'on'){
			if($isAdmin==1)	{ 
				print "<span style='color:$kd_seccol;'>";
				if($kd_m =='on') print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=button>< CLOSE</font>': '<font class=button>MORE ></font>&nbsp;';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><font class=button>MORE ></font></a><span style=\"display: none;\">";
				
				print "$comment</span>\n";
			if($kd_m =='on') 	print "</span>";
			}
			else print "\n";
			
		}

		else{
			if($kd_m =='on'){
				print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<font class=button>< CLOSE</font>': '<font class=button>MORE ></font>&nbsp;';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><font class=button>MORE ></font>&nbsp;</a><span style=\"display: none;\">";
			}
			if($kd_col == 'on') {
				if($kd_replt == 'on') print "<span style='color:$reply_text;'>";
				else print "<span style='color:$comm_ad_fontcol;'>";
			}
			else print "<span style='color:$comm_cu_fontcol;'>";
			print "$comment\n";
			print "</span>";
			
		if($kd_m =='on') 	print "</span>";
		if($option_ip == "on" && $kd_replt !='on') print "<div align=left style=font-family:Tahoma;font-size:7pt;color:$kd_ipcol;></div>\n";
		}



					
		
print "</div></div>";



  	}

$num2 = $num;
$num2 = $picno; }

if($isAdmin==1)
{
echo "</form>\n";
}

	
if(!$mov2){
if($vhchoice[0] < 1) print "</td></tr><tr><td valign='bottom' align=right>";//코멘트 밑에 write1삽입부 테이블
else print "</td></tr><tr><td valign='bottom' align=right style='padding-top:5;padding-bottom:20;'>";//코멘트 밑에 write1삽입부 테이블
}
else print "</td></tr><tr><td valign='bottom' align=right style='padding-top:5;padding-bottom:20;'>";//코멘트 밑에 write1삽입부 테이블

if($reple_mode=="off" || $isAdmin==1) {

if($reply_close =='on') print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'': '';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span class='box' style='color:$comm_morecol;background-color:$comm_morebgcol;border:1px solid $comm_moreborder;'>COMMENT</span></a><div style=\"display: none;\">\n";
include ("write2.php");
if($reply_close =='on') print "</div>";

}


if(!$mov2){
if($vhchoice[0] < 1) print "</td></tr></table>";
}
print "</td></tr></table>";
print "\n";

}
//------------------요기서 비툴 테이블 끝남


if($dbbrk==0 && $fp) fclose($fp);
else "echo $dbfile 이 서버에 없습니다.<br>\n";
			if($pagebar_type=="on"){
				print "<div align='center' style='padding-top:10px;'>$page_bar<br /><br></div>";
			}//페이지 바 표시
print "<div align='center'>";
print"<font class='non'>MMB &copy;TOMCAT&nbsp;/&nbsp;SKIN BY &copy;KODAMA&nbsp;&copy;<a href='http://enter421.blog.me/' target='_blank'>ESEM</a></font></div><br>";
print "</td></tr></table>";
//메인 테이블 끝남



print "</td></tr></table></td></tr></table>";//페이지바 끝내고 열었던 테이블 모두 닫음

?>
</body>
</html>
<?
include "KDM_skin_data.php";
include "KDM_fontcol_data.php";
include "KDM_tb_data.php";


function member_login()
{
	global $bgurl; global $bgcol; global $bg_fo; global $li_fo; global $ac_fo; global $beu_bt_fontcolor; global $beu_bt_col; global $beu_bt_bodercol;
echo "
<form name='member' method='post'>
        <div align='center'><br><input type='password' name='memberpasswd' style='width:100px; height:17px; text-align:center; font-size:7pt; color:$beu_bt_fontcolor; background-color:$beu_bt_col; border: 1px solid $beu_bt_bodercol'><input type='submit' name='submit2' value='  ENTER  ' style='width:80px;height:17px;font-family:Tahoma;font-size:7pt;font-weight:bold;letter-spacing:2px;padding:0 0 2 0; color:$beu_bt_fontcolor; background-color:$beu_bt_col; border:1px solid $beu_bt_bodercol'>
</form>";
}

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

function alt($msg='') {
  echo "<script language='javascript'>";
  if($msg) echo 'alert("'.$msg.'");';
  echo "location.reload();</script>\n";
}

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}
?>