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



function gwfIsSuccess(response)
{
	console.log(response);
	return response === false ? false : gwfNextToken(response, 0) > 0;
}

function gwfNextToken(response, i)
{
	var index = response.indexOf(":", i);
	if (index === -1)
	{
		alert('GWF Response Error 2');
		return '';
	}
	return response.substring(i, index);
}

function gwfDisplayMessage(response)
{
	if (response === false)
	{
		alert('GWF Response Error 1');
		return;
	}
	var len = response.length;
	var i = 0;
	var message = '';
	while (i < len)
	{
		var code = gwfNextToken(response, i);
		i += code.length + 1;
		
		var dlen = gwfNextToken(response, i);
		i += dlen.length + 1;
		
		message += "\n" + response.substr(i, dlen);
		i += dlen + 1;
	}
	
	alert(message.substr(1));
}

