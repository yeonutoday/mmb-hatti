<?
header ("Pragma: no-cache");
include "config_data.php";
include "env.php";
include "mtype_plugin/db_admin.php";

function goBack($msg='', $url='') {
   echo "<script>";
   if($msg) echo 'alert("'.$msg.'");';
   if($url) echo 'location.replace("'.$url.'");';
   else echo 'history.go(-1);';
   echo "</script>";
}

 if(!$mov)
	{ goBack("�±װ� �����ϴ�.");
    exit(); }


///////////////////////////////////////////////////////////
    // ���ϴ� �н����� ���� �� data����
  	$piclimit = $cfg_piclimit;

	$input = $HTTP_RAW_POST_DATA;
  	$spos = strpos($input, "f\r\n");

	$spos = $spos+3;

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
	
	print "<input type='hidden' name='mov' value='mov'>";
	$mov =stripslashes($mov);
	print "<input type='hidden' name='upcheck_s' value='upcheck_s'>";
	print "<input type='hidden' name='upcheck_m' value='upcheck_m'>";
	print "<input type='hidden' name='upcheck_cs' value='upcheck_cs'>";
	
	$fp = fopen ("$picfo/$pixcount.png","wb");
	fwrite($fp,substr($input, $spos));
	fclose($fp);

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

  
//--------����log ����----------------
  $allow = explode(",",$cfg_allowExt);
  if(is_array($allow)) $check = in_array($ext,$allow);

  if($pixcount>$piclimit){
    $delnum = $pixcount-$piclimit;
    for($cnt=0;count($allow)>$cnt;$cnt++){
      @unlink("$datafo/$delnum.".$allow[$cnt]);
    }
  }
//�������.
////////////////////////////////////
//procunlock();



echo "<meta http-equiv='refresh' content='0; url=index.php'>";



?>