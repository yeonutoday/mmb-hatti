<html>
<head>
<title>이모티콘 목록</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel=StyleSheet HREF=./style.css type=text/css title=style>
<style>
A:link    {color:666666;text-decoration:none;}
A:visited {color:666666;text-decoration:none;}
A:active  {color:666666;text-decoration:none;}
A:hover  {color:666666;text-decoration:none;}
</style>
</head><body  bgcolor=#ffffff>
<center><p><b>이모티콘 목록</b></p>
<TABLE BORDER='1'  bordercolor='#FFFFFF' width='90%' CELLSPACING='2' CELLPADDING='3' align='center' valign='top'>
<?
include "config_data.php";
include "env.php";

$cp = fopen("data/emote_data.txt", "r");
$exc_icon=0;
$cnt=0;
$isAdmin=0;

// 관리자 패스워드쿠키가 있으면서 관리자암호와 같으면 관리자모드임
if($ckadminpasswd == $cfg_admin_passwd && $ckadminpasswd !="")
{
	$isAdmin = 1;
}

while(!feof($cp)) {
  for($cnt=0;$cnt<$cfg_emolist && !feof($cp);$cnt++){
    $first_arg[$cnt] = chop(fgets($cp, 4096));
    $second_arg[$cnt] = chop(fgets($cp, 4096));
    if(substr($first_arg[$cnt],0,3)=="---"){
      if($isAdmin==0){
        $exc_icon=1;
        break;
      }
      else{
        $cnt--;
        continue;
      }
    }
  }
  echo "<tr>\n";

  for($prt=0;$prt<$cnt;$prt++)
  	echo "<td align=center valign=bottom BGCOLOR=#FFFFFF CELLSPACING=0 CELLPADDING=0><img src='image/$second_arg[$prt]' border=0></td>\n";
  echo  "</tr><tr>\n";
  for($prt=0;$prt<$cnt;$prt++)
   	echo  "<td align=center valign=bottom BGCOLOR=#EAEAEA CELLSPACING=0 CELLPADDING=0><center>$first_arg[$prt]</center></td>\n";
  echo  "</tr>\n";

  if($exc_icon==1) break;
}
?>
</TABLE><br><a href="#" onClick='self.close()'>닫기</a></font></center>
</body></html>