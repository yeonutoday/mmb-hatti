<html>
<head>
<title>�̸�Ƽ�� ���</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel=StyleSheet HREF=./style.css type=text/css title=style>
<style>
A:link    {color:666666;text-decoration:none;}
A:visited {color:666666;text-decoration:none;}
A:active  {color:666666;text-decoration:none;}
A:hover  {color:666666;text-decoration:none;}
</style>
</head><body  bgcolor=#ffffff>
<center><p><b>�̸�Ƽ�� ���</b></p>
<TABLE BORDER='1'  bordercolor='#FFFFFF' width='90%' CELLSPACING='2' CELLPADDING='3' align='center' valign='top'>
<?
include "config_data.php";
include "env.php";

$cp = fopen("data/emote_data.txt", "r");
$exc_icon=0;
$cnt=0;
$isAdmin=0;

// ������ �н�������Ű�� �����鼭 �����ھ�ȣ�� ������ �����ڸ����
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
</TABLE><br><a href="#" onClick='self.close()'>�ݱ�</a></font></center>
</body></html>