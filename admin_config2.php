<?
header ("Pragma: no-cache");
include "env.php";



if($ckadminpasswd != $cfg_admin_passwd or $ckadminpasswd =="") {
  print "<script>alert('�ҹ� ȣ���Դϴ�');";
  print "self.close();</script>";
  exit;
}

if($choose=="reset"){
  confirm('���� �Խ����� �ʱ�ȭ �Ͻðڽ��ϱ�?\n�ټ� �ð��� �ɸ� �� �ֽ��ϴ�.', 1);
}

else if($choose=="restore"){
  confirm('���� �Խ����� ���� �Ͻðڽ��ϱ�?\n������ �������� ���� �� �ֽ��ϴ�.', 2);
}

else if($choose=="emo"){
  require "config_data.php";
  $emowidth = $cfg_emolist*72; //����Ͻô� �̸�Ƽ���� ���� ����� Ŭ ��� ���� ���� �ø�����.

echo <<<END
<html><body>
<script language="javascript">

function back()
{
	history.go(-1);
}//������� back ��ư �߰��Լ�
</script>

<br><p><font color="blue">*�̸�Ƽ�� �߰�*</font><br><br>
<form name="form1" method=post action="emo_upload.php" enctype="multipart/form-data">
<input type='file' name='userfile'> <br>
<font size='2'>�̸�Ƽ�� ��ɾ� <input type='text' name='emocommt' size='10'></font>
<input type='submit' name='submit' value='���ε�'>
<input type="radio" name="ra" value="0">�Ϲ� / <input type="radio" name="ra" value="1">�����
</form><font size='2'>** �̸�Ƽ���� ũ��� �״��� ũ�� �������� ���մϴ�.<br>
�̸�Ƽ���� �뷮�� ��뵵 Ʈ���ȿ� �ణ�� ������ �ݴϴ�. **</font></p>

<br><p><font color="blue">*�̸�Ƽ�� ����*</font>&nbsp;&nbsp;
<a href = '#' onclick="window.open('emo_list.php', '�̸�Ƽ��', 'width=$emowidth,height=650,menubar=no,status=no,scrollbars=yes,resizeable=yes,left=50,top=50')">[�̸�Ƽ�� ���]</a><br><br>
<form name="form2" method=post action="emo_upload.php" enctype="multipart/form-data">
<font size='2'>������ �̸�Ƽ�� ��ɾ� <input type='text' name='del_emocommt' size='10'></font>
<input type=hidden name=del_emo value=del><input type='submit' name='submit' value='����'>
</form><font size='2'>** �����Ͻ� �̸�Ƽ���� ��ɾ ������(/)�� �����ؼ� �� �ּ���.</font></p>
<input type='button' onClick='back()' name='Back' value=' �ڷ� '>
</body></html>
END;
}//�̸�Ƽ�� �߰� ����

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
}//������� back ��ư �߰��Լ�
</script>
<form name="form_cut" method=post action="$PHP_SELF?conok=3">
<br><p><font color="blue">** ���� �ܾ� ��� **</font>
 <input type='button' onClick='back()' name='Back' value=' �ڷ� '> <input type='submit' name='submit' value=' ���� '><br><br>
<textarea name="blk_list" cols="40" rows="6">$blk_area</textarea>
</form>
END;
}//���Ѵܾ� �߰� ����


//-----------------------
if($conok==1){
  bbs_reset();
  gourl("./index.php");
}//�Խ��� �ʱ�ȭ

else if($conok==2){
  bbs_restore();
  gourl("./index.php");
}//�Խ��� ����

else if($conok==3){
  block_list($blk_list);
  gourl("./admin_config.php");
}//���� �ܾ� ����

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

//------------------------- �Խ��� �ʱ�ȭ �Լ�
function bbs_reset(){
  global $dbindex;
  $result = proclock();
  if($result==0)
  {
	  print"���� �����߽��ϴ�.";
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
  if(@unlink($dbindex)) alt("�ʱ�ȭ�� �Ϸ�Ǿ����ϴ�.");
  procunlock();
}

//------------------------- �Խ��� ���� �Լ�
function bbs_restore(){
  include "config_data.php";
  
  global $dbindex;
  $topnum=0;
  $allow = explode(",",$cfg_allowExt);
  $rec_cnt=ceil($cfg_piclimit/100); //�� ���ϴ� array�� ���� ����.

  for($cnt=0;$cnt<100;$cnt++){
    $dbfile = "$datafo/$cnt.dat";
    if(!file_exists($dbfile)) continue;

    $fp = fopen($dbfile,"r");
    $arr_cnt=0;

   	while(!feof($fp)){
     	if($rec_cnt==$arr_cnt)  break;
      $buffer = chop(fgets($fp, 4096));
      
    	if(substr($buffer,0,1)==">"){ // ������ ���� �տ� '>'�� ������ �׸���
        $buffer = substr($buffer,1);
        $data = explode("|", $buffer);
        list($picno,$picfn,$others) = $data;
        if(file_exists("./$datafo/$picfn")){
          $rec_arr[]=$picno;  //�α׹�ȣ�� key, ���ϸ��� value
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
//------------------------- ���Ѵܾ� ���� �Լ�
function block_list($blk_list){
  $blkdb = "$datafo/blockw_data.txt";
  $fp = fopen($blkdb,"w");
  if($fp){
    fputs($fp,$blk_list);
    fclose($fp);
    alt('���Ѵܾ� ����� ������\n�Ϸ�Ǿ����ϴ�.');
  }
  else alt("���Ѵܾ� ��� ���� ����.");
}

?>