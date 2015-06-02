
$('#changeEmailButton').click( function(event)
{
	event.preventDefault();

	var passwordString = $('#password_input').val();
	var originalEmailString = $('#originalemail_input').val();
	var newEmailString = $('#newemail_input').val();
	var confirmEmailString = $('#confirmemail_input').val();
	
	//Check data
	if(passwordString == '')
	{
		$('#errorcontainer').html('Please enter your password'); return false;
	}
	
	//Send request
	$.ajax({type: "POST", url: domain+"/api/account/email/change/",	
	data: {password: passwordString, original_email: originalEmailString, new_email: newEmailString, confirm_new_email: confirmEmailString},
	error: function(xhr, status, error) {
		var response = JSON.parse(xhr.responseText);
		$('#errorcontainer').html(response['data']['reason']);
	},
	success: function(result)
	{
		var response = JSON.parse(result);
		alert(result);
		if(response['meta']['status'] == 1)
		{
			//Account disabled
			location = domain+'/settings/?message=youremailhasbeenchanged';
		}
		else if(response['meta']['status'] == 0)
			$('#errorcontainer').html(response['data']['reason']);	
	}});
	
});

$('#editButton').click( function(event)
{
	event.preventDefault();

	var passwordString = $('#password_input').val();
	var firstnameString = $('#first_name_input').val();
	var lastnameString = $('#last_name_input').val();
	
	//Check data
	if(passwordString == '')
	{
		$('#errorcontainer').html('Please enter your password'); return false;
	}
	
	//Send request
	$.ajax({type: "POST", url: domain+"/api/account/name/",	
	data: {password: passwordString, firstName: firstnameString, lastName: lastnameString},
	error: function(xhr, status, error) {
		var response = JSON.parse(xhr.responseText);
		$('#errorcontainer').html(response['data']['reason']);
	},
	success: function(result)
	{
		var response = JSON.parse(result);
		//alert(result);
		if(response['meta']['status'] == 1)
		{
			//email changed
			location = domain+'/settings/?message=yournamehasbeenchanged';
		}
		else if(response['meta']['status'] == 0)
			$('#errorcontainer').html(response['data']['reason']);	
	}
	});
	
});

$('#disableButton').click( function(event)
{
	event.preventDefault();
	
	var passwordString = $('#password_input').val();
	$('#errorcontainer').html('');
	
	if(passwordString == '')
	{
		$('#errorcontainer').html('Please enter your password'); return false;
	}
	
	$.ajax({type: "POST", url: domain+"/api/account/disable/",	
	data: {password: passwordString},
	error: function(xhr, status, error) {
		var response = JSON.parse(xhr.responseText);
		$('#errorcontainer').html(response['data']['reason']);
	},
	success: function(result)
	{
		var response = JSON.parse(result);
		if(response['meta']['status'] == 1)
		{
			//Account disabled
			tryLogout();
		}
		else if(response['meta']['status'] == 0)
			$('#errorcontainer').html(response['data']['reason']);	
	}});
});