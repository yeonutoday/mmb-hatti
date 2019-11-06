<?
include "env.php";



$method = "post";
$php_url = "admin_config.php";
$errcnt = 0;

if($ckadminpasswd != $cfg_admin_passwd or $ckadminpasswd =="") {
  print "<script>alert('불법 호출입니다');";
  print "self.close();</script>";
  exit;
}

$cp = fopen("config_list.php", "r");
while(!feof($cp)) {
  $first_arg = trim(fgets($cp, 4096));
  $second_arg = trim(fgets($cp, 4096));
  $config_list[$first_arg] = $second_arg;
}
fclose($cp);

$cp = fopen("option_list.php", "r");
while(!feof($cp)) {
  $first_arg = trim(fgets($cp, 4096));
  $second_arg = trim(fgets($cp, 4096));
  $option_list[$first_arg] = $second_arg;
}
fclose($cp);

if ($action == "cfgsav") {
  if (ereg("[^[:digit:]]", $cfg_piclimit)) {
    print "<br><br><br><center>\n";
    print "페이지 최대 저장한도에는 0-9 사이의 숫자만 사용할수 있습니다";
    print "</center>\n";
    exit;
  }

  while($temp = each($config_list)) {
    if($temp[key]=='cfg_piclimit' && $$temp[key]>1000) exit("$temp[value] 수치는 1000 이하로만 설정하실 수 있습니다.");
    else if($temp[key]=='cfg_allowSize' && $$temp[key]>5120)  exit("$temp[value] 수치는 5120 이하로만 설정하실 수 있습니다.");
    $mentecc[] = "\$".$temp[key]."='".$$temp[key]."'";
  }
  while($temp = each($option_list)) {
    $mentecc2[] = "\$".$temp[key]."='".$$temp[key]."'";
  }

//--------data 기록
  if($cp = fopen("config_data.php", "w")){
    fwrite($cp, "<?\n");
    while($save_var = each($mentecc)) {
      $save_vars = $save_var["value"];
      fwrite($cp, "$save_vars;\n");
    }
    fwrite($cp, "?>\n");
  } else $errcnt++;
  fclose($cp);

  if($cp = fopen("option_data.php", "w")){
    fwrite($cp, "<?\n");
    while($save_var = each($mentecc2)) {
      $save_vars = $save_var["value"];
      fwrite($cp, "$save_vars;\n");
    }
    fwrite($cp, "?>\n");
  } else $errcnt++;
  fclose($cp);

//--------piclimit 설정으로 인덱스와 그림 삭제
  $ret = proclock();//락 시작
  if($ret==0){
	  alt('락 에러입니다.');
  	exit();
  }

  $cnt=0;
  $fp =fopen("$dbindex", "r");
	while(!feof($fp)){
 	  $data[$cnt++] = trim(fgets($fp,4096));
	}
  fclose($fp);

  $topnum=$data[0];
  $old_minnum=$data[--$cnt];
  $allow = explode(",",$cfg_allowExt);
  if(is_array($allow)) $check = in_array($ext,$allow);

  if($fp = fopen("$dbindex","w")){
    for($cnt=0;$cnt<$cfg_piclimit;$cnt++){
    	if($data[$cnt]!="") fputs($fp, $data[$cnt]."\n");
    }
  }else $errcnt++;
  fclose($fp);

  $minnum=$data[--$cnt];
  for(;$old_minnum<$minnum;$old_minnum++){
    for($j=0;count($allow)>$j;$j++){
      @unlink("$datafo/$old_minnum.".$allow[$j]);
    }
  }
  procunlock();

//---------alt
  if($errcnt > 0)   alt("설정저장 실패");
  else{
    alt("설정저장 성공");
    echo('<script>history.go(-1);</script>');
  }
}

else {
  include "config_data.php";
  include "option_data.php";
  print "<html><head>\n";
  print "<META HTTP-EQUIV=Content-type CONTENT=text/html; charset=euc-kr>\n";
  print "<title>MMB 환경설정</title>\n";
  print "</head>\n";
  print "<body bgcolor=\"$bgcolor\" text=\"$textcolor\">\n";
  print "<br><br><center>\n";
  print "<font size=6>MMB 환경설정</font>\n";
  print "</center>\n";
  print"<div align='center' style='background-color:white;text-align:center; '><span style='font-size:small; color=white; '>◈ 환경설정 Create by Madoka, Edit by Mic ◈</span></div>";
  print "<DIV ALIGN=center>";
  print "<input type=button value=\"메인으로 돌아감\" onclick=\"back()\" style=\"width=180;height=18; color:white;background-color=#606080; padding-top:1px; border-width:0px; border-color:rgb(74,75,131); border-style:dotted;\">\n\n";
  print "<HR><DIV ALIGN=CENTER>\n";
  print "<form method=\"$method\" action=\"$php_url\">\n";
  print "<input type=hidden name=action value=cfgsav>\n";
  print "<b>설정</b><br><br>\n";
  print "<table border=0><tr><td valign=top><table border=0>\n";

  $data_file = array("config_data.php", "option_data.php", "$datafo/dbindex.dat", "$datafo/recent.txt");
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

  while($temp = each($config_list)) {
    $name = $temp[key];
    $value = $$temp[key];
    print "<tr><td>◈ $temp[value]</td><td>：</td><td><input type=text name=$name size=10 value=\"$value\">$space</td></tr>\n";
  }

  print "</table></td><td valign=top><table border=0>\n";
  print "<tr><td>◈ 표시 옵션 ON/OFF</td></tr>\n";
  $roop = 0;
  $onoffary = array("on","off");
  while($choice_option_list = each($option_list)) {
    $option_select = connect_chk($$choice_option_list["key"], $onoffary);
    print "<tr><td> - ".$choice_option_list["value"]."</td><td>：</td><td><select name=".$choice_option_list["key"].">$option_select$space</td></tr>\n";
  }
  print "</table></td></tr><table><br>\n";
  print "<input type=submit value=Submit style=\"width=60;height=18; color:white;background-color=#606080; padding-top:1px; border-width:0px; border-color:rgb(74,75,131); border-style:dotted;\" target=new></td></tr>\n";
  print "</form><br>\n";

  print "<table border=0><tr><td valign=top><form name=\"form_button\" method=\"$method\" action=\"admin_config2.php\">\n";
  print "<input type=\"button\" value=\"이모티콘 추가/삭제\" onClick=\"select_order('emo')\" style=\"width=130;height=18; color:white;background-color=#606080; padding-top:1px; border-width:0px; border-color:rgb(74,75,131); border-style:dotted;\">\n";
  print "<input type=\"button\" value=\"단어 제한 목록\" onClick=\"select_order('cut')\" style=\"width=130;height=18; color:white;background-color=#606080; padding-top:1px; border-width:0px; border-color:rgb(74,75,131); border-style:dotted;\">\n";
  print "<input type=\"button\" value=\"reset\" onClick=\"select_order('reset')\" style=\"width=100;height=18; color:white;background-color=#606080; padding-top:1px; border-width:0px; border-color:rgb(74,75,131); border-style:dotted;\">\n";
  print "<input type=\"button\" value=\"restore\" onClick=\"select_order('restore')\" style=\"width=100;height=18; color:white;background-color=#606080; padding-top:1px; border-width:0px; border-color:rgb(74,75,131); border-style:dotted;\">\n";
  print "<input type=\"hidden\" name=\"choose\">\n";
  print "</form>\n";
  print "</td></tr></table></DIV>";
  print "<div style='text-align:center;'>";
  print "  <table><tr><td></td></tr></table>";
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