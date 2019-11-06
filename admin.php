<?
include "env.php";
include "KDM_skin_data.php";
include "KDM_tb_data.php";
include "KDM_fontcol_data.php";


if($logout=="on"){
	setcookie ("ckadminpasswd","",time()-3600);
	$ckadminpasswd="";
}

// 관리자 패스워드쿠키가 있으면서 관리자암호와 같으면 인증됨 표시
if($ckadminpasswd === $cfg_admin_passwd && $ckadminpasswd !="")
{
	$adminpasswd = $ckadminpasswd;
}

if($adminpasswd === $cfg_admin_passwd)
{
	setcookie ("ckadminpasswd", $adminpasswd,time()+30*24*3600);
	$isAdmin = 1;
}

// 비공개 게시판 모드 비밀번호 체크
if($memberpasswd === $cfg_member_passwd)
{
  setcookie ("memberlogin",$memberpasswd,0);
  $isMember = 1;
}
else $isMember = 0;

// 관리자로그인되지 않았을때 보여줄 화면
function print_authscr()
{
	global $bgurl; global $bgcol; global $bg_fo; global $li_fo; global $ac_fo; global $beu_bt_fontcolor; global $beu_bt_col; global $beu_bt_bodercol;
echo "
<html>
<head>
<title>ADMINISTRATOR LOGIN</title>
<link rel=StyleSheet HREF=./style.css type=text/css title=style>
<link rel='stylesheet' media='only screen and (-webkit-device-pixel-ratio: 0.75)' type='text/css' href='style.css'  />
<link rel='stylesheet' media='only screen and (-webkit-min-device-pixel-ratio:1.5)' type='text/css' href='style.css'/>
<link rel='stylesheet' media='only screen and (-webkit-min-device-pixel-ratio: 2)' type='text/css' href='style.css'/>
<meta name='HandheldFriendly' content='True' />
<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=yes' />
<style>
A:link    {color:<?=$li_fo?>;text-decoration:none;}
A:visited {color:<?=$li_fo?>;text-decoration:none;}
A:active  {color:<?=$li_fo?>;text-decoration:none;}
A:hover  {color:<?=$vi_fo?>;text-decoration:none; background-color:<?=$ac_fo?>;}
</style>
</head>

<body bgcolor='$alltb_bgc' text='$b_fo'>
<form name='admin' method='post' action='admin.php'>
  <table width='280px' cellspacing='0' cellpadding='0' height='100%' align='center' valign='middle' style='border:none;'>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height='120' style='border:none;' valign='middle' align='center'>
      <div align='center'>
          
<input type='password' name='adminpasswd' style='width:100px; height:17px;text-align:center; font-size:7pt; color:$beu_bt_fontcolor; background-color:$beu_bt_col; border: 1px solid $beu_bt_bodercol;'><input type='submit' name='Submit' value='  LOGIN  ' style='valign:bottom; width:80px;height:17px; font-family:Tahoma; font-size:7pt; letter-spacing: 2px; font-weight:bold; padding: 0 0 2 0;color:$beu_bt_fontcolor; background-color:$beu_bt_col; border:1px solid $beu_bt_bodercol'>
</p>
      </div>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
";
}

// 멤버 로그인 화면
function member_login()
{
	global $bgurl; global $bgcol; global $bg_fo; global $li_fo; global $ac_fo; global $beu_bt_fontcolor; global $beu_bt_col; global $beu_bt_bodercol;
echo "
<html>
<head>
<title>MEMBER LOGIN</title>
<link rel=StyleSheet HREF=./style.css type=text/css title=style>
<link rel='stylesheet' media='only screen and (-webkit-device-pixel-ratio: 0.75)' type='text/css' href='style.css'  />
<link rel='stylesheet' media='only screen and (-webkit-min-device-pixel-ratio:1.5)' type='text/css' href='style.css'/>
<link rel='stylesheet' media='only screen and (-webkit-min-device-pixel-ratio: 2)' type='text/css' href='style.css'/>
<meta name='HandheldFriendly' content='True' />
<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=yes' />
<style>
A:link    {color:<?=$li_fo?>;text-decoration:none;}
A:visited {color:<?=$li_fo?>;text-decoration:none;}
A:active  {color:<?=$li_fo?>;text-decoration:none;}
A:hover  {color:<?=$vi_fo?>;text-decoration:none; background-color:<?=$ac_fo?>;}
</style>
</head>

<body bgcolor='$alltb_bgc' text='$b_fo'>
<form name='member' method='post' action='admin.php?member=1'>
  <table width='280px' cellspacing='0' cellpadding='0' height='100%' align='center' valign='middle' bgcolor='#FFFFFF' style='border:none;'>
  <tr>
    <td></td>
  </tr>
  <tr>
    <td style='border:none;' valign='middle' align='center'>
      <div align='center' style='line-height:100%;'>

<input type='password' name='memberpasswd' style='width:100px; height:17px; text-align:center; font-size:7pt; color:$beu_bt_fontcolor; background-color:$beu_bt_col; border: 1px solid $beu_bt_bodercol'><input type='submit' name='submit2' value='  ENTER  ' style='width:80px;height:17px;font-family:Tahoma;font-size:7pt;font-weight:bold;letter-spacing:2px;padding:0 0 2 0; color:$beu_bt_fontcolor; background-color:$beu_bt_col; border:1px solid $beu_bt_bodercol'>
            </p>
      </div>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
";
}

if($member == 1 && $isMember == 0){
  member_login();
}
else if($isAdmin==1 || $logout=="on" || $isMember == 1){
	echo("
	<html>
	<head>
	<title><?=$title?></title>
	<meta http-equiv='refresh' content='0; url=./index.php'>
	</head></html>
	");
}
else{
  print_authscr();
}
?>
</body>
</html>