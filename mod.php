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
 	die("�� $datafo ���� ��ϺҰ� ����.<br />�۹̼��� 777�� �����Ͻñ� �ٶ��ϴ�.<br /><br />");
}
if(!file_exists($dbindex)){
  die("MMB $BBS_VERSION �ű� ��ġ�� Ȯ���մϴ�. ������ �α��� ��, ȯ�漳���� ���� ������ �ּ���.");
}
if(!is_writable($dbindex)) {
  die("�� $dbfile ���� �� ���ų� �۹̼��� 666�� �ƴմϴ�.  FTP�� Ȯ���Ͻñ� �ٶ��ϴ�.<br /><br />");
}

//����� �Խ��� ���
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
}//���� �������� ���
*/

if(($ckadminpasswd != $cfg_admin_passwd || $ckadminpasswd =="") && $reple_mode=='on'){
  gourl("./admin.php");
  exit;
}//���� �������� ���

function del_html($str)
{
	$str = str_replace( ">", "&gt;",$str );
	$str = str_replace( "<", "&lt;",$str );
	$str = str_replace( "\"", "&quot;",$str );
	$str = str_replace( "&lt;br&gt;","<br>",$str); //br���ǰ���
	return $str;
}

function autolink($str)
{
	// URL ġȯ
	$homepage_pattern = "/([^\"\=\>])(mms|http|HTTP|ftp|FTP|telnet|TELNET|https|HTTPS)\:\/\/(.[^ \n\<\"]+)/";
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
	echo "<body bgcolor=\"#FFFFFF\" text=\"#333333\" link=\"#ffffff\">\n";
	echo $msg."\n";
	echo "</body><html>";
}


$ckname = stripslashes($ckname);
$emowidth = $cfg_emolist*72; //����Ͻô� �̸�Ƽ���� ���� ����� Ŭ ��� ���� ���� �ø�����.
$dbnum = $num%100;
$dbfile = "$datafo/$dbnum.dat";

$fp = fopen("$dbfile","r");
//dbfile ����

while(!feof($fp))
{
  if(!file_exists($dbfile)) break;

  $buffer = fgets($fp, 4096);
 	$buffer = chop($buffer);

	if(substr($buffer,0,1)==">"){ // ������ ���� �տ� '>'�� ������ �׸���
		$buffer = substr($buffer,1);
		$data = explode("|", $buffer);
		list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;
		// �׸���ȣ, ff���ϸ�, �۾��ð�(��), ��ȣȭ���н�����, ������, ��Ͻð�, ȣ��Ʈ����, IP

  }
  else{ //���϶�
    if($picno==$num){
      if(substr($buffer,0,1)!=">") // ������ ���� �տ� '>'�� ������ �׸���
      {
        $data = explode("|", $buffer);
        list($autname,$comment,$rtime,$ip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt) = $data;
        if($comment=="")continue;
        // �ۼ��ڸ�,�۳���,�̸�,Ȩ�ּ�,��Ͻð�,IP,�н�����
        
        // ������ ���� �� ���
        if($time==$rtime) { // mod.php���� ���� �ۼ� �ð�
        $mdata = $buffer;
        $mdata = explode("|", $buffer);
        list($mname,$mcom,$mtime,$mip,$mpasswd,$mkd_s,$mkd_m,$mkd_memo,$mkd_col,$mkd_replt) = $mdata;
        $mcom = str_replace("<br>","\n",$mcom);
        }
        // �������
        // ��б� ó��
        if($mkd_s == "on" && $isAdmin != "1") {
        showmsg("��б��� �����ڸ� ������ �� �ֽ��ϴ�.");
        exit();
        }
        
         

      }
    }
    else continue;
  }
}
fclose($fp);

?>

<html>
<head>
<title><?=$title?> ;MODIFY reply</title>
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

.cmtb {width:500px;filter: Alpha(Opacity=<?=$tbopacity1?>);opacity:<?=$tbopacity2?>;}


</style>
</head>
<script type="text/javascript" src="js/js_input.js" charset='utf-8'></script>
<body bgcolor="<?=$bgcol?>" style="background-image:url(<?=$bgurl?>); background-repeat:repeat; background-attachment:fixed;" text="<?=$b_fo?>">
<h4 align="center"><a href="./index.php"><font class="button"><��BACK</font></a></h4>
<form name="write" method="post" action="mod_proc.php">
  <p align="center"><?=$thisisfake?></p>

<TABLE class='cmtb' style='border:none;' CELLSPACING='0' CELLPADDING='5' align='center'>



<?
$wreply = "on";


if($wreply == 'on' ){
  $loop_max = sizeof($old_name);

   for($loop = 0; $loop < $loop_max; $loop++) {
    print "<tr><TD BORDER=1 bgcolor='$comm_bgcol' CELLSPACING=0 CELLPADDING=0 width=100%>";

    print $old_name[$loop];
	print "<font style='color:$comm_cu_fontcol; '>";
    print $old_comments[$loop];
	print "</font>";
    print "</td></tr>";
  }
}
?>


</font>
</table><br>


<table class='cmtb' cellspacing='0' cellpadding='0'  style='background-color:<?=$co_w_textbox?>;padding:10;border:1px solid <?=$co_w_textborder?>;' align='center'>
<tr><td>
<table width="100%" border="0"   bgcolor="<?=$co_w_textbox?>">


	  <? if($isAdmin!=0){ ?>
  <tr>
<td border='0'width="85%">
         <input type="text" name="kd_memo" size="15" value="<?=$mkd_memo?>" style="color:<?=$co_w_txfontcol?>; border:none; background-color:<?=$co_m_textbox?>;border-bottom:2px solid <?=$co_m_textborder?>; width:100%;">
       </td>
<td  align="left" valign="bottom" width="15%"><span style="font-size:7pt; color:<?=$b_fo?>;"></span></td>

    </tr>
	<? } ?>





<tr>
            <td border='0' width="85%" >
 <textarea name="comment" cols="20" rows="6"  style=" color:<?=$co_w_txfontcol?>; background-color:<?=$co_w_textbox?>; border:none; width:100%; overflow:visible;" ><?=$mcom?></textarea>

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

        <td border='0' width="100%" bgcolor="<?=$co_w_tbcol?>"><font style="color:<?=$b_fo?>;">
<? if ($mkd_col =='on'){
print "$ad_ico";
print "<input type='checkbox' name='kd_col' checked style='display:none'>";
}
?>
<b><?=$mname?></b>&nbsp;

<?
if($isAdmin==1) { print "<input type='password' name='dpasswd' value='$ckadminpasswd' size='6' style='color:$co_w_namefcol; background-color:$co_w_namebox; border:none;'>";

}
else print "<input type='password' name='passwd' size='6' style='color:$co_w_namefcol; background-color:$co_w_namebox; border:none;'>&nbsp;";

?>

<input type="checkbox" class="checkbox2" name="kd_replt" <? if($mkd_replt == "on") echo "checked"; ?> style="display:none;"> 
<input type="checkbox" class="checkbox2" name="usecookiepw" value="on" <?if($ckpass!="")echo checked;?>><font class='checkbox'>&nbsp;pw</font>
<input type="checkbox" class="checkbox2" name="usecookie" value="on" <?if($ckuse=="on")echo checked;?>><font class='checkbox'>&nbsp;cookie</font>
<input type="checkbox" class="checkbox2" name="kd_m" <? if($mkd_m == "on") echo "checked"; ?>><font class='checkbox'>&nbsp;more</font>
</font>
<input type="checkbox" class="checkbox2" name="kd_s" <? if($mkd_s == "on") echo "checked"; ?>><font class='checkbox'>&nbsp;<img src='image/sc3.png' border='0'></font>

</td>
    </tr>

</table>

    <p>&nbsp;</p>

</form>
</body>
</html>