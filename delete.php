<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
</head>
<body bgcolor="#FFFFFF" text="#000000">
<?
include "env.php";
include "option_data.php";
include "mtype_plugin/db_admin.php";

//����� �Խ��� ���
if($mem_login=='on'){
  if($memberlogin == $cfg_member_passwd);
  else{
    gourl("./admin.php?member=1");
    exit;
  }
}

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}

function print_member($num,$name,$time)
{
echo "
<table width=640 border=0 cellspacing=0 cellpadding=0 align=center height=100%>
  <tr>
    <td>
      <div align=center>
        <p>�н����带 �Է��ϼ���.</p>
        <form name=formdel method=get action=delete_proc.php>
          <input type=password name=dpasswd>
          <input type=submit name=Submit value='����'>
          <input type=hidden name=dnum  value='$num'>
          <input type=hidden name=dauth value='$name'>
          <input type=hidden name=dtime value='$time'>
        </form>
        <p>&nbsp;</p>
      </div>
    </td>
  </tr>
</table>
";
}//������ ���� �н����� �Է�

if($restrict_del=='on'){
  if($action != 'login'){
  echo "
  <table width=640 border=0 cellspacing=0 cellpadding=0 align=center height=100%>
    <tr>
      <td>
        <div align=center>
          <p>ȸ�� �н����带 �Է��ϼ���.</p>
          <form name=memdel method=get action=delete.php>
            <input type=password name=mpasswd>
            <input type=submit name=Submit value='����'>
            <input type=hidden name=action value=login>
            <input type=hidden name=num  value='$num'>
            <input type=hidden name=name value='$name'>
            <input type=hidden name=time value='$time'>
          </form>
          <p>&nbsp;</p>
        </div>
      </td>
    </tr>
  </table>
  ";
  }
  else if($cfg_member_passwd != $mpasswd){
    echo "ȸ�� �н����尡 Ʋ�Ƚ��ϴ�.\n";
  }
  else print_member($num,$name,$time);
}
else print_member($num,$name,$time);

?>
</body>
</html>