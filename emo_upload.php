<?
include "ad_set.php";
$input = $HTTP_RAW_POST_DATA;
$spos = strpos($input, "f\r\n");
$varName = "userfile"; //���� ���������� ������ file ������
$newfile =  $_FILES[$varName][name]; //���� ���������� ������ file ������
$dir = "./image/"; //����� ���� ���(���� '/'������ �� �ٿ��ּ���.)
$del_find=0; //������ �̸�Ƽ�� ã��


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
  goBack('�����̸��� ȭ���� �ֽ��ϴ�.\nȭ�ϸ��� �����ϰ� ���ε� �Ͻñ� �ٶ��ϴ�.');
  exit();
}

if(!is_writable($dir)) {
  goBack("$dir ���� ������ Ȯ���� �ּ���.");
  exit();
}
if($newfile!="" && strlen($newfile)>14){
  goBack('���� �̸��� �ʹ� ��ϴ�.\n10�� ���Ϸ� �ٿ��ּ���.');
  exit();
}

if($del_emo=="del") $del_emo=1;

//---��� �б�
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
    goBack('���� ��ɾ �ֽ��ϴ�.\n��ɾ �����ϰ� ���ε� �Ͻñ� �ٶ��ϴ�.');
    exit();
  }
  $cnt++;
}
fclose($fp);

if($del_emo==1 && $del_find==0){
  goBack('�ش� ��ɾ �����ϴ�.\n����� �ٽ� Ȯ�� �� �ּ���.');
  exit();
}

//---�̸�Ƽ�� ����
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
  alt('�̸�Ƽ�� ['.$del_emocommt.']\n���� �Ϸ�');
}

//---�׸� ���ε�
if(is_uploaded_file($_FILES[$varName][tmp_name])) {
//---�̸�Ƽ�� �߰� : �����
  if($ra==1){
    $fp = fopen("$emodb","a");
    fputs($fp, "\n".$emocommt."\n");
    fputs($fp, $newfile."\n");
    fclose($fp);
    alt('�̸�Ƽ�� ['.$emocommt.']\n�߰� �Ϸ�');
  }

//---�̸�Ƽ�� �߰� : �Ϲ�
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
    alt('�̸�Ƽ�� ['.$emocommt.']\n�߰� �Ϸ�');
  }

  if(!move_uploaded_file($_FILES[$varName][tmp_name], $dir.$newfile)) {
    goBack("���� ���ε忡 �����߽��ϴ�.");
    exit();
  }
}
echo '<script>history.go(-1);</script>';
?>