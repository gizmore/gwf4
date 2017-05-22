<!-- GWF3 BB CODE BAR  -->
<div class="gwf3_bb_code_bar">
<div>
	<img src="<?php echo $imgroot; ?>/bb/b.png"
		alt="[b]"
		title="[b]<?php echo GWF_HTML::lang('bbhelp_b'); ?>[/b]"
		onclick="return bbInsert('<?php echo $key; ?>', '[b]', '[/b]')" />
		
	<img src="<?php echo $imgroot; ?>/bb/i.png"
		alt="[i]"
		title="[i]<?php echo GWF_HTML::lang('bbhelp_i'); ?>[/i]"
		onclick="return bbInsert('<?php echo $key; ?>', '[i]', '[/i]')" />
		
	<img src="<?php echo $imgroot; ?>/bb/u.png"
		alt="[u]"
		title="[u]<?php echo GWF_HTML::lang('bbhelp_u'); ?>[/u]"
		onclick="return bbInsert('<?php echo $key; ?>', '[u]', '[/u]')" />
	
	<img src="<?php echo $imgroot; ?>/bb/code.png"
		alt="[code]"
		title="[code=lang]<?php echo GWF_HTML::lang('bbhelp_code'); ?>[/code]"
		onclick="return bbInsertCode('<?php echo $key; ?>');" />
	
	<img src="<?php echo $imgroot; ?>/bb/quote.png" 
		alt="[quote]" 
		title="[quote=username]<?php echo GWF_HTML::lang('bbhelp_quote'); ?>[/quote]" 
		onclick="return bbInsert('<?php echo $key; ?>', '[quote=Unknown]', '[/quote]')" />

	<img src="<?php echo $imgroot; ?>/bb/url.png" 
		alt="[url]" 
		title="[url=url]<?php echo GWF_HTML::lang('bbhelp_url'); ?>[/url] or [url]url[/url]" 
		onclick="return bbInsertURL('<?php echo $key; ?>')" />

	<img src="<?php echo $imgroot; ?>/bb/email.png"
		alt="[email]" 
		title="[email=email@url]<?php echo GWF_HTML::lang('bbhelp_email'); ?>[/email] or [email]email[/email]" 
		onclick="return bbInsert('<?php echo $key; ?>', '[email]', '[/email]')" />
	
	<img src="<?php echo $imgroot; ?>/bb/noparse.png"
		alt="[noparse]"
		title="[noparse]<?php echo GWF_HTML::lang('bbhelp_noparse'); ?>[/noparse]"
		onclick="return bbInsert('<?php echo $key; ?>', '[noparse]', '[/noparse]')"
	/>
	
	<img src="<?php echo $imgroot; ?>/bb/score.png"
		alt="[score]"
		title="[score=5]<?php echo GWF_HTML::lang('bbhelp_level'); ?>[/score]"
		onclick="return bbInsert('<?php echo $key; ?>', '[score=5]', '[/score]')"
	/>
	
	<img src="<?php echo $imgroot; ?>/bb/spoiler.png" 
		alt="[spoiler]"
		title="[spoiler]<?php echo GWF_HTML::lang('bbhelp_spoiler'); ?>[/spoiler]"
		onclick="return bbInsert('<?php echo $key; ?>', '[spoiler]', '[/spoiler]')"
	/>
	
<!-- 	<img src="<?php echo $imgroot; ?>/bb/img.png"
		alt="[img]"
		title="[img title= alt= w= h=]url <?php echo GWF_HTML::lang('bbhelp_img'); ?>[/img]"
		onclick="return bbInsert('<?php echo $key; ?>', '[img title\'\' alt=\'\']', '[/img]')"
	/>

	<img src="<?php echo $imgroot; ?>/bb/youtube.png"
		alt="[youtube]"
		title="[youtube]id <?php echo GWF_HTML::lang('bbhelp_youtube'); ?>[/youtube]"
		onclick="return bbInsert('<?php echo $key; ?>', '[youtube]', '[/youtube]')"
	/>
	<img src="<?php echo $imgroot; ?>/bb/user.png" 
	/>
 -->
</div>

<div id="bb_code_<?php echo $key; ?>"></div>

<div id="bb_url_<?php echo $key; ?>" class="h">
	<select id="bb_url_prot_<?php echo $key; ?>">
		<option value="/"><?php echo GWF_SITENAME; ?></option>
		<option value="http://">HTTP</option> 
		<option value="https://">HTTPS</option> 
<!-- 		<option value=""><?php echo GWF_HTML::lang('other', array('3', 2, 1)); ?></option>  --> 
	</select>
	<input id="bb_url_href_<?php echo $key; ?>" type="text" value="google.de" />
	<img src="<?php echo $imgroot; ?>/add.png" alt="Add" title="Add" onclick="return bbInsertURL('<?php echo $key; ?>')" />
<!-- <input type="submit" onclick="" value="Add" />  -->
</div>

<!-- /GWF3 BB CODE BAR  -->
</div>
