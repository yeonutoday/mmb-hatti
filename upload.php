<?
header ("Pragma: no-cache");
include "config_data.php";
include "env.php";
include "mtype_plugin/db_admin.php";



$dir = "./$picfo/"; //저장될 폴더 경로(끝에 '/'슬래시 꼭 붙여주세요.)
$varName = "userfile"; //이전 페이지에서 설정된 file 변수명
$allowSize = $cfg_allowSize*1024;	//kb를 byte로 변환하고 제한.
$limit_num = 0; //dbindex에서 그림파일의 존재 판단.

function goBack($msg='', $url='') {
   echo "<script>";
   if($msg) echo 'alert("'.$msg.'");';
   if($url) echo 'location.replace("'.$url.'");';
   else echo 'history.go(-1);';
   echo "</script>";
}

function lock_ok($msg='') {
     echo "<script>";
     if($msg) echo 'alert("'.$msg.'");';
     echo 'history.go(-1);';
	 echo "</script>";
}

if($_FILES[$varName][name] && $_FILES[$varName][error] == 0) {
// $dir 폴더가 지정됐고, 사용가능 한지 검사
  if(!$dir) {
    goBack("업로드 폴더가 지정되지 않았습니다.");
    exit();
  }

  if(!is_writable($dir)) {
    goBack("업로드 폴더 권한을 확인해 주세요.");
    exit();
  }

  if($allowSize < $_FILES[$varName][size]) {
    goBack("파일 용량이 허용된 용량을 초과했습니다.");
    exit();
  }

// 정상적인 방법으로 업로드 된 파일인지 검사 후 정상이면 파일 업로드 처리

  if(is_uploaded_file($_FILES[$varName][tmp_name])) {
  // 확장자 검사
    $ext = substr(strrchr($_FILES[$varName][name],"."),1);
    $tname = $_FILES[$varName][tmp_name];
    $wsize = getImagesize($tname);

    if($ext) {
      $ext = strtolower($ext);
      $cfg_allowExt = strtolower($cfg_allowExt);
      // 대문자가 섞여있으면 소문자로 만듬.

      $allow = explode(",",$cfg_allowExt);
      if(is_array($allow)) $check = in_array($ext,$allow);
      else $check = ($ext == $allow) ? true : false;
    }

    if(!$ext || !$check) {
      goBack("업로드 불가능한 확장자 입니다.");
      exit();
    }

    if($wsize[0] > $max_width){
      echo("이미지 가로 크기가 너무 큽니다. 원본 사이즈 : ".$wsize[0]." pixel");
      exit();
    }

    $ret = proclock();

    if($ret==0)
    {
      showmsg("락 에러입니다. (".$ret.")");
      exit();
    }

///////////////////////////////////////////////////////////
    // 이하는 패스워드 설정 및 data쓰기
  	$piclimit = $cfg_piclimit;

	  $input = $HTTP_RAW_POST_DATA;
  	$spos = strpos($input, "f\r\n");

   	$passtmp = $passwd;
    $passtmp = substr($passtmp,-2);
    if(strlen($passtmp)==0)  $passtmp=$passwd;//비밀번호가 한자리면 교체
    $pw = crypt($passwd,$passtmp);
    if($passwd=="")$pw = "";

//--------dbindex에서 pixcnt 추출
  	$fp = fopen($dbindex,"r");
    $buffer = fgets($fp, 4096);
  	fclose($fp);

    $pixcount = intval($buffer)+1;
//-------그림 작성------------

    $nowtime = time();
	$newfile = "$nowtime.$ext";	  	     // 파일명 생성

print "<input type='hidden' name='upcheck_s' value='upcheck_s'>";
print "<input type='hidden' name='upcheck_m' value='upcheck_m'>";
print "<input type='hidden' name='upcheck_cs' value='upcheck_cs'>";

//-------dbfile에 쓰기 시작
  	$outdata = array(">$pixcount","$newfile",$pw,time(),$REMOTE_ADDR,$upcheck_s,$upcheck_m,$upcheck_cs,$mov);
    $nowdata = join("|",$outdata);

    $dbnum = $pixcount%100;
    $dbfile = "$datafo/$dbnum.dat";

 	  if(!file_exists($dbfile)){
     	$fp = fopen($dbfile,"w");
	    fclose($fp);
 		  chmod ($dbfile, 0666);
     	$fp = fopen("$dbfile","r");
 	  }
    else $fp = fopen("$dbfile","r");

   	$cnt = 0;
    $delmode=0;
    while(!feof($fp))
 	  {
  	  $buffer =$data[$cnt++] = fgets($fp,4096);
    	if(substr($buffer,0,1)==">"){ // 라인의 제일 앞에 '>'가 있으면 그림임
  		  $delmode=0;
      	$buffer = substr($buffer,1);
        $data_arr = explode("|", $buffer);
  	  	list($picno,$picfn,$pass,$rtime,$ip,$upcheck_s,$upcheck_m,$upcheck_cs,$mov) = $data_arr;
  		  if($picno==$pixcount){
          $delmode=1;
          $cnt--;
    		}
    	}
    	else if($delmode==1)  $cnt--;
   	}
    fclose($fp);

   	$totalrec = $cnt;
    $cnt = 0;

   	$fp = fopen("$dbfile","w");
    fputs($fp,$nowdata."\n");
   	while($cnt<$totalrec)
    {
  	  fputs($fp,$data[$cnt++]);
 	  }
    fclose($fp);

//--------dbindex 쓰기 시작-----------

   	$cnt = 0;
    $fp = fopen("$dbindex","r");
   	while(!feof($fp))
    {
  	  $data[$cnt++] = fgets($fp,4096);
 	  }
    fclose($fp);

    $cnt = 0;
   	$fp = fopen("$dbindex","w");
    fputs($fp,$pixcount."\n");
   	while($cnt<$piclimit)
    {
	    fputs($fp,$data[$cnt++]);
   	}
    fclose($fp);
 	  $cnt = 0;

    if(!move_uploaded_file($_FILES[$varName][tmp_name], $dir.$newfile)) {
      goBack("파일 업로드에 실패했습니다.");
      exit();
    }//파일명 변경하며 저장

    if(!chmod($dir.$newfile,0707)) {
      goBack("퍼미션변경에 실패했습니다.");
      exit();
    }
  }
//--------간단log 정리----------------
  $allow = explode(",",$cfg_allowExt);
  if(is_array($allow)) $check = in_array($ext,$allow);

  if($pixcount>$piclimit){
    $delnum = $pixcount-$piclimit;
    for($cnt=0;count($allow)>$cnt;$cnt++){
      @unlink("$picfo/$delnum.".$allow[$cnt]);
    }
  }
}//여기까지.
////////////////////////////////////
procunlock();
echo "<meta http-equiv='refresh' content='0; url=index.php'>";
?>