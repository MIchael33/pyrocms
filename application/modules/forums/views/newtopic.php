<form id='submit_post' name='submit_post' method="post" action="/<?=$forum_controller."/newtopic/".$forumID?>"  enctype="multipart/form-data" >
<input type="hidden" name="forumID" value="<?=$forumID;?>" />
<? if(!empty($error_msg)): ?>
<table width="100%" border="0" cellpadding="4" cellspacing="0">
  <tr>
    <td width="85%" bgcolor="#FF0000"><font color="#FFFFFF"><b><?=$error_msg;?></b></font></td>
  </tr>
</table>
<br />
<? elseif(!empty($preview)): ?>
<table width="100%" border="0" cellpadding="4" cellspacing="0">
  <tr>
    <th align="left" bgcolor="#999999" scope="col"> Preview</th>
  </tr>
  <tr>
    <td width="85%" bgcolor="#CCCCCC"><?=$preview;?></td>
  </tr>
</table>
<br />
<? endif; ?>
<table width="100%" border="0" cellpadding="4" cellspacing="0">
  <tr>
    <th colspan="2" align="left" bgcolor="#999999" scope="col"> Post a New Topic to <?=$forum_name;?></th>
  </tr>
  <tr>
    <td width="15%" valign="top" bgcolor="#CCCCCC"><b>Title:</b></td>
    <td width="85%" bgcolor="#CCCCCC"><input type="text" class="input" name="title" style='width:100%' size="90" value="<?=$title;?>" /></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#CCCCCC"><hr></td>
  </tr> 
  <tr>
    <td width="15%" bgcolor="#CCCCCC"><b>Formatting:</b></td>
    <td width="85%" bgcolor="#CCCCCC"><?=bbcode_menu();?></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#CCCCCC"><hr></td>
  </tr>  
  <tr>
    <td width="15%" valign="top" bgcolor="#CCCCCC"><b>Message:</b></td>
    <td width="85%" bgcolor="#CCCCCC"><textarea name="message" rows="15" cols="70"><?=$message;?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#CCCCCC"><hr></td>
  </tr>  
  <tr>
    <td  width="15%" valign="top" bgcolor="#CCCCCC"><b>Options:</b></td>
    <td width="85%" bgcolor="#CCCCCC">
		<input type="checkbox" class="checkbox" name="smileys" value="1" <? if($smileys == 1) { ?>checked="checked"<? } ?> /> Enable smileys in this post<br />
		<input type="checkbox" class="checkbox" name="notify" value="1" <? if($notify == 1) { ?>checked="checked"<? } ?> /> Notify me via email when someone posts in this thread
	</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#CCCCCC"><hr></td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#CCCCCC"><input type="submit" name="preview" value="Preview Topic" /> | <input type="submit" name="submit" class="submit" value="Submit Topic" /></td>
  </tr>  
</table>
</form>