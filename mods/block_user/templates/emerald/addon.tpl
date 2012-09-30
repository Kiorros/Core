

<div class="nav">
  <a class="icon icon-folder" href="{URL->INDEX}">Forum List</a>
  <a class="icon icon-list" href="{URL->LIST}">Message List</a>
    
</div>

<div id="profile">
{IF ERROR}<div class="attention">{ERROR}</div>{/IF}
{IF OKMSG}<div class="information">{OKMSG}</div>{/IF}
{IF SHOW_FORM}
    <div class="generic">
	{LANG->mod_block_user->Introduction}<br /><br />
	<form action="{URL->ACTION}" method="POST">
	{POST_VARS}
	{LANG->mod_block_user->BlockAction}<br />
	<select name="block_action">
	<option value="nothing">{LANG->mod_block_user->LeaveUserAlone}</option>
	<option value="block_in_forum">{LANG->mod_block_user->BlockUserInForum}</option>
	<option value="block_in_phorum">{LANG->mod_block_user->BlockUserInPhorum}</option>
	<option value="deactivate_user">{LANG->mod_block_user->DeactivateUser}</option>
	</select><br /><br />
	{IF TOO_MANY}
	{LANG->mod_block_user->TooManyPosts}<br />
	{ELSE}
	<input type="checkbox" value="1" name="delete_posts" /> {LANG->mod_block_user->DeletePosts}<br /><br />
	{/IF}
	<input type="submit" name="action" value="{LANG->mod_block_user->Submit}" />
	</form>
	</div>
{/IF}	
</div>	