<?
header ("Pragma: no-cache");
include "config_data.php";
include "env.php";
include "mtype_plugin/db_admin.php";



$dir = "./$picfo/"; //����� ���� ���(���� '/'������ �� �ٿ��ּ���.)
$varName = "userfile"; //���� ���������� ������ file ������
$allowSize = $cfg_allowSize*1024;	//kb�� byte�� ��ȯ�ϰ� ����.
$limit_num = 0; //dbindex���� �׸������� ���� �Ǵ�.

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
// $dir ������ �����ư�, ��밡�� ���� �˻�
  if(!$dir) {
    goBack("���ε� ������ �������� �ʾҽ��ϴ�.");
    exit();
  }

  if(!is_writable($dir)) {
    goBack("���ε� ���� ������ Ȯ���� �ּ���.");
    exit();
  }

  if($allowSize < $_FILES[$varName][size]) {
    goBack("���� �뷮�� ���� �뷮�� �ʰ��߽��ϴ�.");
    exit();
  }

// �������� ������� ���ε� �� �������� �˻� �� �����̸� ���� ���ε� ó��

  if(is_uploaded_file($_FILES[$varName][tmp_name])) {
  // Ȯ���� �˻�
    $ext = substr(strrchr($_FILES[$varName][name],"."),1);
    $tname = $_FILES[$varName][tmp_name];
    $wsize = getImagesize($tname);

    if($ext) {
      $ext = strtolower($ext);
      $cfg_allowExt = strtolower($cfg_allowExt);
      // �빮�ڰ� ���������� �ҹ��ڷ� ����.

      $allow = explode(",",$cfg_allowExt);
      if(is_array($allow)) $check = in_array($ext,$allow);
      else $check = ($ext == $allow) ? true : false;
    }

    if(!$ext || !$check) {
      goBack("���ε� �Ұ����� Ȯ���� �Դϴ�.");
      exit();
    }

    if($wsize[0] > $max_width){
      echo("�̹��� ���� ũ�Ⱑ �ʹ� Ů�ϴ�. ���� ������ : ".$wsize[0]." pixel");
      exit();
    }

    $ret = proclock();

    if($ret==0)
    {
      showmsg("�� �����Դϴ�. (".$ret.")");
      exit();
    }

///////////////////////////////////////////////////////////
    // ���ϴ� �н����� ���� �� data����
  	$piclimit = $cfg_piclimit;

	  $input = $HTTP_RAW_POST_DATA;
  	$spos = strpos($input, "f\r\n");

   	$passtmp = $passwd;
    $passtmp = substr($passtmp,-2);
    if(strlen($passtmp)==0)  $passtmp=$passwd;//��й�ȣ�� ���ڸ��� ��ü
    $pw = crypt($passwd,$passtmp);
    if($passwd=="")$pw = "";

//--------dbindex���� pixcnt ����
  	$fp = fopen($dbindex,"r");
    $buffer = fgets($fp, 4096);
  	fclose($fp);

    $pixcount = intval($buffer)+1;
//-------�׸� �ۼ�------------

    $nowtime = time();
	$newfile = "$nowtime.$ext";	  	     // ���ϸ� ����

print "<input type='hidden' name='upcheck_s' value='upcheck_s'>";
print "<input type='hidden' name='upcheck_m' value='upcheck_m'>";
print "<input type='hidden' name='upcheck_cs' value='upcheck_cs'>";

//-------dbfile�� ���� ����
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
    	if(substr($buffer,0,1)==">"){ // ������ ���� �տ� '>'�� ������ �׸���
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

//--------dbindex ���� ����-----------

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
      goBack("���� ���ε忡 �����߽��ϴ�.");
      exit();
    }//���ϸ� �����ϸ� ����

    if(!chmod($dir.$newfile,0707)) {
      goBack("�۹̼Ǻ��濡 �����߽��ϴ�.");
      exit();
    }
  }
//--------����log ����----------------
  $allow = explode(",",$cfg_allowExt);
  if(is_array($allow)) $check = in_array($ext,$allow);

  if($pixcount>$piclimit){
    $delnum = $pixcount-$piclimit;
    for($cnt=0;count($allow)>$cnt;$cnt++){
      @unlink("$picfo/$delnum.".$allow[$cnt]);
    }
  }
}//�������.
////////////////////////////////////
procunlock();
echo "<meta http-equiv='refresh' content='0; url=index.php'>";
?>