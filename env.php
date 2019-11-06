<?
@extract($_COOKIE);
include "ad_set.php";

if(sizeof($_GET)) { $result = $_GET; $result_gp = "g"; }
else { $result = $_POST; $result_gp = "p"; }

reset($result);
while($temp = each($result)) {
  if($result_gp == "g") {
    $$temp["key"] = $_GET[$temp["key"]];
  }

  else if($result_gp == "p") {
    $$temp["key"] = $_POST[$temp["key"]];
  }
}
////////////////////////////////////////////////////////////////////////////////
$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
$REMOTE_HOST = $_SERVER['REMOTE_HOST'];
$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
////////////////////////////////////////////////////////////////////////////////

//--------------------------------------------------------------------------
$cfg_admin_passwd = $adpass;  //관리자 패스워드
$cfg_member_passwd = $mempass;  //회원 인증 패스워드
$REQUIRED_OCX_VERSION = "1,2,0,2";
//--------------------------------------------------------------------------

$lockfile = "$datafo/btool.lock";
$dbindex = "$datafo/dbindex.dat";

function proclock()
{
	global $lockfile;
	for($lock_count=0;$lock_count<10;$lock_count++){
	  usleep(1);
    if(file_exists($lockfile) and (time() - filemtime($lockfile)) > 5) procunlock();
		if(file_exists($lockfile))
		{
	 	  flush();
			sleep(1);
		}
		else
		{
			$fp = fopen($lockfile,"w");
			fclose($fp);
			chmod ($lockfile, 0666);
			return 1;
		}
	}
	return 0; //락실패
}
function procunlock()
{
	global $lockfile;
	unlink($lockfile);
}

function readlock(){
	global $lockfile;
	for($lock_count=0;$lock_count<10;$lock_count++){
	  usleep(1);
	  
    if(file_exists($lockfile) and (time() - filemtime($lockfile)) > 5){
      procunlock();
    }
	 	if(file_exists($lockfile)){
	 	  flush();
	 	  sleep(1);
	 	}
	 	else return 1;
  }
	return 0; //언락실패
}
?>