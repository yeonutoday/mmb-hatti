
<?
if($isAdmin == '1'){
print "<form name='write1' method='post' action='write_proc.php' style='margin:0; padding:0;'>";
if($vhchoice[0] < $max_width_comment){
	print "<table width=100%' cellspacing='1' cellpadding='1' style='margin:0;'>";
} else {
	print "<table width='100%' cellspacing='1' cellpadding='1'  style='margin:0;'>";
}
?>
  <tr>
    <td width="40%" border="0"><input type="text" name="name" size="6" value="<?=$ckname?>" style="background-color:<?=$co_w_namebox?>; color:<?=$co_w_namefcol?>; border:none;">
<input type="checkbox" class="checkbox2" name="usecookie" value="on" <?if($ckuse=="on")echo checked;?>> <font class=checkbox>cookie</font>
<input type="checkbox" class="checkbox2" name="kd_m"> <font class=checkbox>more</font>
<input type="checkbox" class="checkbox2" name="kd_s"> <font class=checkbox><img src="image/sc3.png"></font></td>
    <td width="45%" border="0" align="right"><input type="text" name="kd_memo" style="color:<?=$co_w_txfontcol?>; border-right:none; border-left:none; border-top:none; border-bottom:<?=$co_m_textborder?> 2px solid; background:<?=

$co_m_textbox?>;" width="100%"></td>
    <td width="15%" border="0" align="right"><input type="submit" name="Submit" value="     COMMENT     " style="width:100%;  font-family:tahoma; font-size:7pt; letter-spacing:2px; color:<?=$co_w_submit_fontcol?>; border:<?=$co_w_submit_border?> 1px solid; background-color:<?=$co_w_submit?>;">
          <input type="hidden" name="number" value="<?=$num2?>">
          <input type="hidden" name="chk_w" value="whoareyou">
<input type="checkbox" name="kd_col" checked style="display:none"></font></td>
  </tr>
  <tr>
    <td colspan="3" width="100%" border="0"><textarea name="comment" cols="20" rows="2"  style=" color:<?=$co_w_txfontcol?>; background:<?=$co_w_textbox?>; border:1px solid <?=$co_w_textborder?>; width:100%; overflow:visible;" 

></textarea></td>
  </tr>
</table>

<?
print "</form>";
}




else{

print "<form name='write2' method='post' action='write_proc.php' style='margin:0; padding:0;'>";

print "<table width='100%' cellspacing='1' cellpadding='1'  style='margin:0;'>";

?>
  <tr>
    <td width="45%" border="0"><input type="text" name="name" size="6" value="<?=$ckname?>" style="background-color:<?=$co_w_namebox?>; color:<?=$co_w_namefcol?>; border:none;"><input type="password" name="passwd" size="6" value="<?=$ckpass?>" style="color:<?=$co_w_namefcol?>; background-color:<?=$co_w_namebox?>; border:none;" >
<input type="checkbox" class="checkbox2" name="usecookiepw" value="on" <?if($ckpass!="")echo checked;?>> <font class=checkbox>pw</font>
<input type="checkbox" class="checkbox2" name="usecookie" value="on" <?if($ckuse=="on")echo checked;?>> <font class=checkbox>cookie</font>
<input type="checkbox" class="checkbox2" name="kd_m"> <font class=checkbox>more</font>
<input type="checkbox" class="checkbox2" name="kd_s"> <font class=checkbox><img src="image/sc3.png"></font></td>
    <td width="30%" border="0" align="right"></td>
    <td width="25%" border="0" align="right"><input type="submit" name="Submit" value="     COMMENT    " style="width:100%;  font-family:tahoma; font-size:7pt; letter-spacing:2px; color:<?=$co_w_submit_fontcol?>; border:<?=$co_w_submit_border?> 1px solid; background-color:<?=$co_w_submit?>;">
          <input type="hidden" name="number" value="<?=$num2?>">
          <input type="hidden" name="chk_w" value="whoareyou">
</font></td>
  </tr>
  <tr>
    <td colspan="3" width="100%" border="0"><textarea name="comment" cols="20" rows="2"  style=" color:<?=$co_w_txfontcol?>; background:<?=$co_w_textbox?>; border:1px solid <?=$co_w_textborder?>; width:100%; overflow:visible;" 

></textarea></td>
  </tr>
</table>



<?
print "</form>";
}
?>
