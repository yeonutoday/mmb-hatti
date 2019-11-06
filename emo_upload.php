<?
include "ad_set.php";
$input = $HTTP_RAW_POST_DATA;
$spos = strpos($input, "f\r\n");
$varName = "userfile"; //이전 페이지에서 설정된 file 변수명
$newfile =  $_FILES[$varName][name]; //이전 페이지에서 설정된 file 변수명
$dir = "./image/"; //저장될 폴더 경로(끝에 '/'슬래시 꼭 붙여주세요.)
$del_find=0; //삭제할 이모티콘 찾기


function goBack($msg='', $url='') {
   echo "<script>";
   if($msg) echo 'alert("'.$msg.'");';
   if($url) echo 'location.replace("'.$url.'");';
   else echo 'history.go(-1);';
   echo "</script>";
}

function alt($msg='') {
echo "<script language='javascript'>";
if($msg) echo 'alert("'.$msg.'");';
echo "</script>";
}


if($newfile!="" && file_exists($dir.$newfile)) {
  goBack('같은이름의 화일이 있습니다.\n화일명을 변경하고 업로드 하시기 바랍니다.');
  exit();
}

if(!is_writable($dir)) {
  goBack("$dir 폴더 권한을 확인해 주세요.");
  exit();
}
if($newfile!="" && strlen($newfile)>14){
  goBack('파일 이름이 너무 깁니다.\n10자 이하로 줄여주세요.');
  exit();
}

if($del_emo=="del") $del_emo=1;

//---목록 읽기
$emodb = "$datafo/emote_data.txt";
$fp = fopen("$emodb","r");
$cnt = 0;
while(!feof($fp))
{
  $data[$cnt] = fgets($fp, 4096);
  $buffer = trim($data[$cnt]);
  if($del_emo==1){
    if($del_emocommt==$buffer)  $del_find=1;
  }
  else if($emocommt!="" && $emocommt==$buffer){
    goBack('같은 명령어가 있습니다.\n명령어를 변경하고 업로드 하시기 바랍니다.');
    exit();
  }
  $cnt++;
}
fclose($fp);

if($del_emo==1 && $del_find==0){
  goBack('해당 명령어가 없습니다.\n목록을 다시 확인 해 주세요.');
  exit();
}

//---이모티콘 삭제
if($del_emo==1){
  $totalrec = $cnt;
  $cnt = 0;
 	$fp = fopen("$emodb","w");
  while($cnt<$totalrec)
  {
    $buffer = trim($data[$cnt]);
    if($buffer==$del_emocommt){
      $cnt++;
      $del_fname = trim($data[$cnt++]);
    }
    fputs($fp,$data[$cnt++]);
  }
	fclose($fp);
	@unlink($dir.$del_fname);
  alt('이모티콘 ['.$del_emocommt.']\n삭제 완료');
}

//---그림 업로드
if(is_uploaded_file($_FILES[$varName][tmp_name])) {
//---이모티콘 추가 : 비공개
  if($ra==1){
    $fp = fopen("$emodb","a");
    fputs($fp, "\n".$emocommt."\n");
    fputs($fp, $newfile."\n");
    fclose($fp);
    alt('이모티콘 ['.$emocommt.']\n추가 완료');
  }

//---이모티콘 추가 : 일반
  else{
    $totalrec = $cnt;
    $cnt = 0;
 	  $fp = fopen("$emodb","w");
    fputs($fp, $emocommt."\n");
    fputs($fp, $newfile."\n");
    while($cnt<$totalrec)
    {
      fputs($fp,$data[$cnt++]);
    }
	  fclose($fp);
    alt('이모티콘 ['.$emocommt.']\n추가 완료');
  }

  if(!move_uploaded_file($_FILES[$varName][tmp_name], $dir.$newfile)) {
    goBack("파일 업로드에 실패했습니다.");
    exit();
  }
}
echo '<script>history.go(-1);</script>';
?>