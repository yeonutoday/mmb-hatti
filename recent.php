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

//����� �Խ��� ���
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
	// ������ �н�������Ű�� �����鼭 �����ھ�ȣ�� ������ �����ڸ����
if($ckadminpasswd == $cfg_admin_passwd && $ckadminpasswd !="")
{
	$isAdmin = 1;
}

if($cfg_admin_passwd=="")
{
	print "������ �н����尡 �����Ǿ����� �ʰų� 'env.php' ������ �о����� �ʾҽ��ϴ�.";
	exit();
}



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

$i = 0;
// ����Ʈ�ε�
$fp = @fopen ("$datafo/recent.txt", "r") or die("�����Ͱ� �������� �ʽ��ϴ�.");
while(!feof($fp))
{
	$buffer = chop(fgets($fp, 4096));
	if($buffer!="")$olist[$i++] = $buffer;
}
fclose($fp);

$ototal = count($olist); //ǥ���� �Խù� ����
$usedline; // �׸��� ����� ���ڵ尳���� �����ҹ迭

$lastnum = $olist[0];
for($i=0;$i<$ototal;$i++)
{
	if($lastnum > $olist[$i] && $olist[$i]!="")$lastnum = $olist[$i];
}

?>
<html>
<head>
<title>Load �Խ���</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel=StyleSheet HREF=./style.css type=text/css title=style>
</head>
<script type="text/javascript" src="js/js_input.js" charset='utf-8'></script>
<body background="<?=$bgurl?>" bgcolor="<?=$bgcol?>" text="<?=$b_fo?>" link="<?=$li_fo?>" vlink="<?=$vi_fo?>" alink="<?=$ac_fo?>">

<div align = "center">
<a href = "./index.php">[�׸������ε��ư�]</a>
<a href = "recent.php">[��ۼ��ֱ�<?=$cfg_recent?>��]</a>
</div>

<?
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
	$homepage_pattern = "/([^\"\=\>])(mms|http|HTTP|ftp|FTP|telnet|TELNET)\:\/\/(.[^ \n\<\"]+)/";
	$str = preg_replace($homepage_pattern,"\\1<a href=\\2://\\3 target=_blank>\\2://\\3</a>", " ".$str);
	return $str;
}

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}

for($logcnt=0;$logcnt<$ototal;$logcnt++){
  $dbnum = $olist[$logcnt]%100;
  $dbfile = "$datafo/$dbnum.dat";

  if(!file_exists($dbfile)) die("$dbfile �� �����ϴ�.");
  $fp = fopen("$dbfile","r");
  while(!feof($fp))
  {
	  $buffer = fgets($fp, 4096);
  	$oribuf = $buffer = chop($buffer);

  	if(substr($buffer,0,1)==">") // ������ ���� �տ� '>'�� ������ �׸���
	  {
		  $nownum = -1;
  		$buffer = substr($buffer,1);

	  	$data = explode("|", $buffer);
		  list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;
  		// �׸���ȣ, ���ϸ�, �۾��ð�(��), ��ȣȭ���н�����, ������, ��Ͻð�, ȣ��Ʈ����, IP

	  	if($picno<$lastnum)break; // ������ �Ѿ�� �׸��ΰ�����

		  for($cnt=0;$cnt<$ototal;$cnt++)
  		if($picno==$olist[$cnt])
	  	{
		  	$subbuf[$cnt][0]=$oribuf;
			  $usedline[$cnt]=1;
  			$nownum = $cnt;
	  	}
  	}
  	else if($nownum!=-1)//����
	  {
		  $subbuf[$nownum][$usedline[$nownum]++]=$oribuf;
  	}
  }
  fclose($fp);
}

$tl = 0;
for($i=0;$i<$ototal;$i++)
{
	for($j=0;$j<$usedline[$i];$j++)
	{
		$outbuffers[$tl++] = $subbuf[$i][$j];
	}
}

$intbl = 0; // ���̺��� �����ִ��� ����

$totalline=$tl;

for($i=0;$i<$totalline;$i++)
{
	$buffer = $outbuffers[$i];

	if(substr($buffer,0,1)==">") // ������ ���� �տ� '>'�� ������ �׸���
	{
		if($intbl==1)
		{
			print "</td></tr></table><br>\n\n";
			$intbl = 0;
		}

		$buffer = substr($buffer,1);

		$data = explode("|", $buffer);
		list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data;
		// �׸���ȣ, ���ϸ�, �۾��ð�(��), ��ȣȭ���н�����, ������, ��Ͻð�, ȣ��Ʈ����, IP

		// �۾��ð��� �ú��� ������ ��ȯ
		$strjtime = sprintf("%d�ð� %d�� %d��",$sec/3600,($sec/60)%60,$sec%60);
		if($sec<3600)$strjtime = sprintf("%d�� %d��",($sec/60)%60,$sec%60);
		if($sec<60)$strjtime = sprintf("%d��",$sec%60);
		if($sec<=0)$strjtime = "�� �� ����";

		print "<TABLE bgcolor='$logtb_bgc' border='0' style='border:$logtb_bor solid $logtb_borc;' width='$alltb_w' CELLSPACING='0' CELLPADDING='5' align='center'>";
		print "<tr><TD align='center' valign='top' width='$vhchoice[0]' BGCOLOR='$pictb_bgc' rowspan='2'>\n";
		print "<div align=left ><font style='color:$pic_number; font-family:Verdana, ����ü; font-size:$pic_numsize;'>No.$picno </font>";
		if($isAdmin!=1)print "<a href='delete.php?num=$picno'>D</a>\n";
		  else  print "<input type='checkbox' name='delpic[]' value='$picno'>\n";

    
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
				  print "</a><span style='color:$kd_seccol;'>����� �̹���</span><br>";
				  if($upcheck_m == 'on')
					  print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'': '';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'><img src=image/click.gif border='0'></span></a><div style=\"display: none;\">\n";
				  print "<a href = 'continue.php?num=$picno'><img src='$picfo/$picfn' border='0'></a>";
				  if($upcheck_m =='on') print "</div>";
			  }
			  else print "</a><img src=image/secret.gif border='0'>\n";
		  }
		  else if($upcheck_cs == 'on'){
			  if($isAdmin==1 || $logout=="on" || $isMember == 1){
				  print "</a><span style='color:$kd_seccol;'>������� �̹���</span><br>";
				  if($upcheck_m == 'on') print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'': '';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'><img src=image/click.gif border='0'></span></a><div style=\"display: none;\">\n";
				  print "<a href = 'continue.php?num=$picno'><img src='$picfo/$picfn' border='0'></a>";
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
				  print "</a><a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'': '';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'><img src=image/click.gif border='0'></span></a><div style=\"display: none;\">\n";
			  }
			  print "<a href = 'continue.php?num=$picno'><img src='$picfo/$picfn' border='0'></a>";
			  if($upcheck_m =='on') print "</div>";
		  }
	  }
	  else {
		  print "<div align=center>";
		  if($upcheck_m =='on') {
			  print "<a class=\"more\" onclick=\"this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'><img src=image/click.gif border='0'></a><div style=\"display: none;\"></span>\n";
		  }
		  print "$mov";
		  if($upcheck_m =='on') print "</div>";
		  print "</div>";
	  }
    





		print "</td><TD BORDER=1 CELLSPACING=0 CELLPADDING=0 valign='top' width=100% style='background-color:$comtb_bgc; padding-top:22;'>\n";
		$intbl = 1;
	}
	else //����
	{
		if($intbl!=1)continue;
		$data = explode("|", $buffer);
		list($autname,$comment,$rtime,$ip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt) = $data;

		if($comment=="")continue;

		if($kd_col == 'on'){
			if($kd_replt == 'on') print "<div style='background:$repl_bgcol; margin:0 20 0 20;'><font style='color:$replt_text; font-size:$ad_namesize;'>��Reply </font><font style='color:$comm_ad_namecol; font-size:$ad_namesize;'><b>";//��� ���̺��� �¿� ������ ���ְ� ������ margin�� 20�� 0���� ���ּ���:)
			else print "<div style='background:$comm_adbgcol;'><font style='color:$comm_ad_namecol; font-size:$ad_namesize;'><b>";
		}
		else {
			if($kd_replt == 'on') print "<div style='background:$repl_bgcol; margin:0 20 0 20;'><font style='color:$replt_text; font-size:$ad_namesize;'>��Reply <b></font><font style='color:$comm_cu_namecol; font-size:$cu_namesize;'>";//��� ���̺��� �¿� ������ ���ְ� ������ margin�� 20�� 0���� ���ּ���:)
			else print"<div style='background:$comm_cuscol; padding-left:$cu_textpadding;'><font style='color:$comm_cu_namecol; font-size:$cu_namesize;'><b>";
		}
		print "$autname</b>&nbsp;</font>";
		
  		$comment = str_replace("%7C","|",$comment);
	  	//$comment = del_html($comment);
		$comment = autolink($comment);
  		$comment = emote_ev($comment, $emote_table);
	  	$altdate = date("Y�� m�� d�� H�� i�� s��",$rtime);

		if($kd_col == 'on'){ 
			print "<font size='1' title='$altdate'>";
			print "<span style='background-color:$comm_ad_datebgcol; color:$comm_ad_datecol; font-family:verdana;'>";
			print date("m.d",$rtime)."&nbsp;</span></font>\n";
		} else {
			print "<font size='1' title='$altdate'>";
			print "<span style='color:$comm_cu_datecol; font-family:verdana;'>";
			print date("m.d",$rtime)."&nbsp;</span></font>";
		}

		$autname=urlencode($autname);//�����ڵ� �ذ�

		
		if ($kd_replt != 'on'){
			echo "<a href=\"reply.php?num=$picno&name=$autname&time=$rtime\">";
			if($kd_col == 'on') print "<span style='color:$comm_ad_datecol;'>R&nbsp;</span></a>";
			else print "<span style='color:$comm_cu_fontcol;'>R&nbsp;</span></a>";
		}
		echo "<a href=\"mod.php?num=$picno&name=$autname&time=$rtime\">";
		if($kd_col == 'on') {
			if($isAdmin==1) print "<span style='color:$comm_ad_datecol;'>M&nbsp;</span></a>";
			else print "\n";
		}
		else print "<span style='color:$comm_cu_fontcol;'>M&nbsp;</span></a>";
		if($isAdmin!=1){
			echo "<a href=\"delete.php?num=$picno&name=$autname&time=$rtime\">";
			if($kd_col == 'on') print "</a><br>\n";
			else print "<span style='color:$comm_cu_fontcol;'>D</span></a><br>\n";
		}
		else print "<input type='checkbox' name='delreply[]' value='$picno|$ip$rtime'><br>\n";
		if($kd_memo) print "<font style='font-face:����ü; color:$kd_memocol;'>memo.$kd_memo</font><br>";

		if($kd_s == 'on'){
			if($isAdmin==1)	print "<span style='color:$kd_seccol;'>$comment</span>";
			else print "<span style='color:$kd_seccol;'>Secret</span>";
			print "<br><br>\n";
		}

		else{
			if($kd_m =='on'){
				print "<a class=\"more\" onclick=\"this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'>More ��</a><div style=\"display: none;\"></span>\n";
			}
			if($kd_col == 'on') {
				if($kd_replt == 'on') print "<font style='color:$reply_text;'>";
				else print "<font style='color:$comm_ad_fontcol;'>";
			}
			else print "<font style='color:$comm_cu_fontcol;'>";
			print $comment."\n";
			print "</font>";
			
		if($kd_m =='on') 	print "</div>";


	

		if($kd_replt =='on') print"\n";
		if($option_ip == "on" && $kd_replt !='on') {
	        print "<div align=right style=font-family:Tahoma;font-size:7pt;color:$kd_ipcol;>$ip</div>\n";
  			}
	  	else if($option_ip == "off")  print "<br>\n";

		}
		print "</div>";
	}
}
print "<script type='text/javascript'>js_input_checkboxs_skin_all(null,true);</script>";
if($intbl==1)
{
	print "</td></tr></table><br>";
	$intbl = 0;
}
?>
<div align="right"><a href="http://mahokokr.pooding.com/" target="_blank">MMB <?=$BBS_VERSION ?> &copy;Madoka</a> / <a href="http://blog.naver.com/ion01/" target="_blank"> &copy; Mic</a> / <a href="http://btool.net/" target="_blank">&copy;Bandi</a>
</div>
</body>
</html>
<?
function member_login()
{
echo "
<form name='member' method='post'>
  <table width='200' border='0' cellspacing='0' cellpadding='0' align='center'>
  <tr>
    <td height='24' bgcolor='$pic_bgcol' style='border:1 solid $menu_bordercol;'>
        <div align='center'><font size='2'><b>check your password</font></b></div>
    </td>
  </tr>
  <tr>
    <td  bgcolor='$comm_bgcol' style='border:1 solid $menu_bordercol;' valign='middle' align='center'>
      <div align='center'>
            <input type='password' name='memberpasswd'>
           	<input type='submit' name='submit2' value='Ȯ��'>
    </td>
  </tr>
</table>
</form>";
}?>