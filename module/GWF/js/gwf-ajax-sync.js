function ajaxUpdate(id, url)
{
	jQuery.ajax(url, {
		success: function(data)
		{
			jQuery('#'+id).html(data);
		}
	});
}

function ajaxUpdateSync(id, url)
{
	var result = ajaxSync(url);
	if (result === false)
	{
		return false;
	}
	jQuery('#'+id).html(result);
	return true;
}

function ajaxSync(url)
{
	var back = false;
	jQuery.ajax(url, {
		async: false,
		success: function(data)
		{
			back = data;
		}
	});
	return back;
}

function ajaxSyncPost(url, data)
{
	var request = jQuery.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: data,
		dataType: 'html'
	});
	var back = false;
	request.done(function(result) { back = result; });
	return back;
}
