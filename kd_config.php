<?
include "env.php";


$method = "post";
$php_url = "kd_config.php";
$errcnt = 0;

if($ckadminpasswd != $cfg_admin_passwd or $ckadminpasswd =="") {
  print "<script>alert('불법 호출입니다');";
  print "self.close();</script>";
  exit;
}

$cp = fopen("KDM_skin_list.php", "r");
while(!feof($cp)) {
  $first_arg = trim(fgets($cp, 4096));
  $second_arg = trim(fgets($cp, 4096));
  $KDM_skin_list[$first_arg] = $second_arg;
}
fclose($cp);

$cp = fopen("KDM_fontcol_list.php", "r");
while(!feof($cp)) {
  $first_arg = trim(fgets($cp, 4096));
  $second_arg = trim(fgets($cp, 4096));
  $KDM_fontcol_list[$first_arg] = $second_arg;
}
fclose($cp);

$cp = fopen("KDM_tb_list.php", "r");
while(!feof($cp)) {
  $first_arg = trim(fgets($cp, 4096));
  $second_arg = trim(fgets($cp, 4096));
  $KDM_tb_list[$first_arg] = $second_arg;
}
fclose($cp);



if ($action == "cfgsav") {
  if (ereg("[^[:digit:]]", $cfg_piclimit)) {
    print "<br><br><br><center>\n";
    print "페이지 최대 저장한도에는 0-9 사이의 숫자만 사용할수 있습니다";
    print "</center>\n";
    exit;
  }

  while($temp = each($KDM_skin_list)) {
    if($temp[key]=='cfg_piclimit' && $$temp[key]>1000) exit("$temp[value] 수치는 1000 이하로만 설정하실 수 있습니다.");
    else if($temp[key]=='cfg_allowSize' && $$temp[key]>5120)  exit("$temp[value] 수치는 5120 이하로만 설정하실 수 있습니다.");
    $mentecc[] = "\$".$temp[key]."='".$$temp[key]."'";
  }
  while($temp = each($KDM_fontcol_list)) {
    $mentecc2[] = "\$".$temp[key]."='".$$temp[key]."'";
  }
  while($temp = each($KDM_tb_list)) {
    $mentecc3[] = "\$".$temp[key]."='".$$temp[key]."'";
  }


//--------data 기록
  if($cp = fopen("KDM_skin_data.php", "w")){
    fwrite($cp, "<?\n");
    while($save_var = each($mentecc)) {
      $save_vars = $save_var["value"];
      fwrite($cp, "$save_vars;\n");
    }
    fwrite($cp, "?>\n");
  } else $errcnt++;
  fclose($cp);

  if($cp = fopen("KDM_fontcol_data.php", "w")){
    fwrite($cp, "<?\n");
    while($save_var = each($mentecc2)) {
      $save_vars = $save_var["value"];
      fwrite($cp, "$save_vars;\n");
    }
    fwrite($cp, "?>\n");
  } else $errcnt++;
  fclose($cp);

  if($cp = fopen("KDM_tb_data.php", "w")){
    fwrite($cp, "<?\n");
    while($save_var = each($mentecc3)) {
      $save_vars = $save_var["value"];
      fwrite($cp, "$save_vars;\n");
    }
    fwrite($cp, "?>\n");
  } else $errcnt++;
  fclose($cp);


//---------alt
  if($errcnt > 0)   alt("설정저장 실패");
  else{
    alt("설정저장 성공");
    echo('<script>history.go(-1);</script>');
  }
}

else {
  include "KDM_skin_data.php";
  include "KDM_fontcol_data.php";
  include "KDM_tb_data.php";
  print "<html><head>\n";
  print "<META HTTP-EQUIV=Content-type CONTENT=text/html; charset=euc-kr>\n";
  print "<title>MMB 스킨설정</title>\n";
  print "<script type='text/javascript' src='js/jscolor.js'></script>";
  print "</head>\n";
  print "<body bgcolor=\"$bgcolor\" text=\"$textcolor\">\n";
  print "<center>\n";
  print "<font size=5>MMB 스킨설정</font>\n";
  print "</center>\n";
  print "<DIV ALIGN=center>\n";
  print "<font style='font-size:9pt;'>◈ 환경설정 Create by Madoka, Edit by Mic ◈ Skin edit by kodama02<br></font>";
  print "<input type=button value=\"메인으로 돌아감\" onclick=\"back()\" style=\"width=180;height=18; color:white;background-color=#606080; padding-top:1px; border-width:0px; border-color:rgb(74,75,131); border-style:dotted;\">\n\n";
  print "<HR><DIV ALIGN=CENTER>\n";
  print "<form method=\"$method\" action=\"$php_url\">\n";
  print "<input type=hidden name=action value=cfgsav>\n";
  print "<table border=0><tr><td valign=top><table border=0>\n";

  $data_file = array("KDM_skin_data.php", "KDM_fontcol_data.php" , "KDM_tb_data.php" , "$datafo/dbindex.dat", "$datafo/recent.txt");
  $data_size = sizeof($data_file);

  for($i=0; $i<$data_size; $i++) {
	if(!is_writable($data_file[$i]) && file_exists($data_file[$i])) {
		print "◈ {$data_file[$i]} 파일 기록불가 상태.<br />퍼미션을 666으로 변경하시기 바랍니다.<br /><br />";
	}
  }
  if(!is_writable("$datafo")) {
	print "◈ $datafo 폴더 기록불가 상태.<br />퍼미션을 777로 변경하시기 바랍니다.<br /><br />";
  } elseif(!file_exists($dbindex)) {
	$fp = fopen($dbindex,"w");
	if(!$fp) {
		die("MMB $BBS_VERSION 로그파일 생성에 실패했습니다.");
	} else {
		alt('MMB '.$BBS_VERSION.' 로그 파일이 성공적으로 생성되었습니다.');
		fclose($fp);
		chmod ($dbindex, 0666);
	}
  }


while($temp = each($KDM_skin_list)) {
    $name = $temp[key];
    $value = $$temp[key];
    print "<tr><td><font style='font-size:9pt'>$temp[value]</font></td><td>：</td><td><input type=text size=7 style='font-size:8pt;' name=$name value=\"$value\" class=\"color{pickerPosition:'right', pickerInsetColor:'black',pickerBorderColor:'black', adjust:false}\">$space </td></tr>\n";
  }

  print "</table></td><td valign=top><table border=0>\n";
  $roop = 0;

  while($temp = each($KDM_fontcol_list)) {
    $name = $temp[key];
    $value = $$temp[key];
    print "<tr><td><font style='font-size:9pt'>$temp[value]</font></td><td>：</td><td><input type=text size=7 style='font-size:8pt;' name=$name value=\"$value\" class=\"color{pickerPosition:'right', pickerInsetColor:'black',pickerBorderColor:'black', adjust:false}\">$space</td></tr>\n";
  }

  print "</table></td><td valign=top><table border=0>\n";

  while($temp = each($KDM_tb_list)) {
    $name = $temp[key];
    $value = $$temp[key];
    print "<tr><td><span style='font-size:9pt;'><font face='돋움'>$temp[value]</font></span></td><td>：</td><td><input type=text name=$name size=10 value=\"$value\" style='font-family:돋움; font-size:9pt; color:rgb(51,51,51); background-color:white; border-width:1pt; border-color:rgb(204,204,204); border-style:solid;'>$space</td></tr>\n";
  }


  print "</table></td></tr><table><br>\n";
  print "<input type=submit value=Submit style=\"width=60;height=18; color:white;background-color=#606080; padding-top:1px; border-width:0px; border-color:rgb(74,75,131); border-style:dotted;\" target=new></td></tr>\n";
  print "</form>\n";
  print "</DIV>";
  print "<div style=\"background-color:blue; \">";
  print "</div></body></html>\n";
}

function connect_chk($temp, $connect_list) {
  $temp_cont = "";
  $connect_size = sizeof($connect_list);
  for($ct=0; $ct < $connect_size; $ct++){
    $option_end = ($temp and $temp==$connect_list[$ct]) ? " selected>" : ">";
    $temp_cont = $temp_cont."<option value=".$connect_list[$ct].$option_end.$connect_list[$ct]."</option>";
  }
  return $temp_cont;
}
function alt($msg='') {
echo "<script language='javascript'>";
if($msg) echo 'alert("'.$msg.'");';
echo "</script>";
}
?>
<form name='prev' method='post' action='./index.php' enctype='multipart/form-data'>
</form>
<script language='javascript'>
function back(){
	prev.submit();
}

function select_order(order){
  form_button.choose.value = order;
  form_button.submit();
}
</script>