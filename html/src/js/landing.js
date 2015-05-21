
$('#signupform').submit(function(event) {
    event.preventDefault();

    trySignup();
});

function trySignup()
{
	var emailvalue = $('#emailinput').val();
	var passvalue = $('#passinput').val();
	var firstName = $('#fNameinput').val();
	var lastName = $('#lNameinput').val();
	
	$('#errorcontainer').html('');
	
	$.ajax({type: "POST", url: "http://gocollegedays.com/api/user/create/", data: {email: emailvalue, password: passvalue, fName: firstName, lName: lastName},  
	error: function(xhr, status, error) {

		var response = JSON.parse(xhr.responseText);
		$('#errorcontainer').html(response['data']['reason']);
	},
	success: function(result)
	{
		var response = JSON.parse(result);
		if(response['meta']['status'] == 1)
		{
			//Match found, refresh page
			tryLogin();
		}
	}});
}

$('#loginform').submit(function(event) {
    event.preventDefault();

    tryLogin();
});

function tryLogin()
{
	var emailvalue = $('#emailinput').val();
	var passvalue = $('#passinput').val();
	$('#errorcontainer').html('');
	
	$.ajax({type: "POST", url: "http://gocollegedays.com/api/login/", data: {email: emailvalue, password: passvalue},  error: function(xhr, status, error) {

		var response = JSON.parse(xhr.responseText);
		$('#errorcontainer').html(response['data']['reason']);
	},
	success: function(result)
	{
		var response = JSON.parse(result);
		if(response['meta']['status'] == 1)
		{
			//Match found, refresh page
			window.location.href = "http://gocollegedays.com";
		}
	}});
}