<?
header ("Pragma: no-cache");
include "env.php";



if($ckadminpasswd != $cfg_admin_passwd or $ckadminpasswd =="") {
  print "<script>alert('불법 호출입니다');";
  print "self.close();</script>";
  exit;
}

if($choose=="reset"){
  confirm('정말 게시판을 초기화 하시겠습니까?\n다소 시간이 걸릴 수 있습니다.', 1);
}

else if($choose=="restore"){
  confirm('정말 게시판을 복구 하시겠습니까?\n완전히 복구되지 않을 수 있습니다.', 2);
}

else if($choose=="emo"){
  require "config_data.php";
  $emowidth = $cfg_emolist*72; //사용하시는 이모티콘의 가로 사이즈가 클 경우 곱셈 값을 올리세요.

echo <<<END
<html><body>
<script language="javascript">

function back()
{
	history.go(-1);
}//여기까지 back 버튼 추가함수
</script>

<br><p><font color="blue">*이모티콘 추가*</font><br><br>
<form name="form1" method=post action="emo_upload.php" enctype="multipart/form-data">
<input type='file' name='userfile'> <br>
<font size='2'>이모티콘 명령어 <input type='text' name='emocommt' size='10'></font>
<input type='submit' name='submit' value='업로드'>
<input type="radio" name="ra" value="0">일반 / <input type="radio" name="ra" value="1">비공개
</form><font size='2'>** 이모티콘의 크기는 그다지 크지 않은것을 권합니다.<br>
이모티콘의 용량과 사용도 트래픽에 약간의 영향을 줍니다. **</font></p>

<br><p><font color="blue">*이모티콘 삭제*</font>&nbsp;&nbsp;
<a href = '#' onclick="window.open('emo_list.php', '이모티콘', 'width=$emowidth,height=650,menubar=no,status=no,scrollbars=yes,resizeable=yes,left=50,top=50')">[이모티콘 목록]</a><br><br>
<form name="form2" method=post action="emo_upload.php" enctype="multipart/form-data">
<font size='2'>삭제할 이모티콘 명령어 <input type='text' name='del_emocommt' size='10'></font>
<input type=hidden name=del_emo value=del><input type='submit' name='submit' value='삭제'>
</form><font size='2'>** 삭제하실 이모티콘의 명령어를 슬래쉬(/)를 포함해서 써 주세요.</font></p>
<input type='button' onClick='back()' name='Back' value=' 뒤로 '>
</body></html>
END;
}//이모티콘 추가 삭제

else if($choose=="cut"){
  $blkdb = "$datafo/blockw_data.txt";
  $fp = fopen("$blkdb","r");
  while(!feof($fp))
  {
    $blklist[] = chop(fgets($fp, 4096));
  }
  fclose($fp);
  reset($blklist);

  $blk_area = implode("\n", $blklist);

echo <<<END
<html><body>
<script language="javascript">

function back()
{
	history.go(-1);
}//여기까지 back 버튼 추가함수
</script>
<form name="form_cut" method=post action="$PHP_SELF?conok=3">
<br><p><font color="blue">** 제한 단어 목록 **</font>
 <input type='button' onClick='back()' name='Back' value=' 뒤로 '> <input type='submit' name='submit' value=' 적용 '><br><br>
<textarea name="blk_list" cols="40" rows="6">$blk_area</textarea>
</form>
END;
}//제한단어 추가 삭제


//-----------------------
if($conok==1){
  bbs_reset();
  gourl("./index.php");
}//게시판 초기화

else if($conok==2){
  bbs_restore();
  gourl("./index.php");
}//게시판 복구

else if($conok==3){
  block_list($blk_list);
  gourl("./admin_config.php");
}//제한 단어 갱신

//-------------------------

function alt($msg='') {
echo "<script language='javascript'>";
if($msg) echo 'alert("'.$msg.'");';
echo "</script>";
}

function confirm($msg='',$conok) {
echo "<script>";
echo 'con=confirm("'.$msg.'");';
echo "if(con==true) location.href=\"./admin_config2.php?conok=$conok\";
else  location.href='admin_config.php';
</script>
";
}

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}

//------------------------- 게시판 초기화 함수
function bbs_reset(){
  global $dbindex;
  $result = proclock();
  if($result==0)
  {
	  print"락에 실패했습니다.";
  	exit();
  }

  @unlink("$datafo/recent.txt");
  $fp = fopen($dbindex,"r");
  $buffer = intval(fgets($fp, 4096))+1;
  fclose($fp);

  $allow = explode(",",$cfg_allowExt);
  for($j=1;$j<$buffer;$j++){
    $k=count($allow);
    for($cnt=0;$cnt<count($allow);$cnt++){
      @unlink("$datafo/$j.".$allow[$cnt]);
    }
  }
  for($cnt=0;$cnt<100;$cnt++){
    @unlink("$datafo/$cnt.dat");
  }
  if(@unlink($dbindex)) alt("초기화가 완료되었습니다.");
  procunlock();
}

//------------------------- 게시판 복구 함수
function bbs_restore(){
  include "config_data.php";
  
  global $dbindex;
  $topnum=0;
  $allow = explode(",",$cfg_allowExt);
  $rec_cnt=ceil($cfg_piclimit/100); //한 파일당 array에 넣을 갯수.

  for($cnt=0;$cnt<100;$cnt++){
    $dbfile = "$datafo/$cnt.dat";
    if(!file_exists($dbfile)) continue;

    $fp = fopen($dbfile,"r");
    $arr_cnt=0;

   	while(!feof($fp)){
     	if($rec_cnt==$arr_cnt)  break;
      $buffer = chop(fgets($fp, 4096));
      
    	if(substr($buffer,0,1)==">"){ // 라인의 제일 앞에 '>'가 있으면 그림임
        $buffer = substr($buffer,1);
        $data = explode("|", $buffer);
        list($picno,$picfn,$others) = $data;
        if(file_exists("./$datafo/$picfn")){
          $rec_arr[]=$picno;  //로그번호는 key, 파일명은 value
          $arr_cnt++;
        }
      }
    }
    fclose($fp);
  }
  
  rsort($rec_arr);
  if($cfg_piclimit>count($rec_arr)) $arr_cnt=count($rec_arr);
  else  $arr_cnt=$cfg_piclimit;
  
 	$fp = fopen("$dbindex","w");
  for($cnt=0;$arr_cnt>$cnt;$cnt++) fputs($fp,"$rec_arr[$cnt]\n");
  fclose($fp);
}
//------------------------- 제한단어 적용 함수
function block_list($blk_list){
  $blkdb = "$datafo/blockw_data.txt";
  $fp = fopen($blkdb,"w");
  if($fp){
    fputs($fp,$blk_list);
    fclose($fp);
    alt('제한단어 목록의 수정이\n완료되었습니다.');
  }
  else alt("제한단어 목록 갱신 실패.");
}

?>