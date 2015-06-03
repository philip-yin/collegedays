
$('#preferences_guys').hover( function()
{
	if(guys == 0)
	$('#preferences_guys').css('background', '#474747');
}, function()
{
	if(guys == 0)
	$('#preferences_guys').css('background', '#3a3a3a');
});

$('#preferences_girls').hover( function()
{
	if(girls == 0)
	$('#preferences_girls').css('background', '#474747');
}, function()
{
	if(girls == 0)
	$('#preferences_girls').css('background', '#3a3a3a');
});

$('#match_button').hover( function()
{
	if((girls > 0) || ((guys > 0) && (verified > 0)))
	$('#match_button').css('cursor', 'pointer');
}, function()
{
	if((girls > 0) || (guys > 0))
	$('#match_button').css('cursor', 'default');
});

$('#preferences_guys').click( function() {	toggleGuys(); reflectButton(); updatePreferences(); });
$('#preferences_girls').click( function() {	toggleGirls(); reflectButton(); updatePreferences(); });


$(window).load(function(e) {

	if(guys > 0){ guys = 0; toggleGuys(); };
	if(girls > 0){ girls = 0; toggleGirls(); };
	reflectButton();
});

function toggleGuys()
{
	if(guys == 0)
		guys = 1;
	else
		guys = 0;
	
	if(guys > 0)
	{
		$('#preferences_guys').css('background', '#85e0bd');
		$('#check_guys').animate({opacity: '1'}, 250);
	}
	else
	{
		$('#preferences_guys').css('background', '#3a3a3a');
		$('#check_guys').animate({opacity: '0'}, 250);
	}
		
}

function toggleGirls()
{
	if(girls == 0)
		girls = 1;
	else
		girls = 0;
	

	if(girls > 0)
	{
		$('#preferences_girls').css('background', '#ed5d83');
		$('#check_girls').animate({opacity: '1'}, 250);
	}
	else
	{
		$('#preferences_girls').css('background', '#3a3a3a');
		$('#check_girls').animate({opacity: '0'}, 250);
	}
}

function reflectButton()
{
	$('#preferences_message').html('');
	
	if((guys > 0) || (girls > 0))
	{
		if(verified == 0)
		{
			$('#preferences_message').html('Verify your email to start matching.<br><a id="resend">Resend</a>');
			
			$('#resend').click(function() {
				requestResend();
			});
			
			$('#match_button').animate({opacity: '0.3'}, 250);
			return;
		}
		
		$('#match_button').animate({opacity: '1'}, 250);
	}	
	else
	{
		if(!matching)
		$('#match_button').animate({opacity: '0.3'}, 250);
	}
}

var isUpdating = false;
function updatePreferences()
{
	if(isUpdating)
		return;

	isUpdating = true;
	
	//Send
	var guysInt = guys;
	var girlsInt = girls;
	
	$.ajax({type: "POST", url: domain+"/api/account/matching/preferences/",	
	data: {guys: guysInt, girls: girlsInt},
	error: function(xhr, status, error) {
		
		isUpdating = false;
		var response = JSON.parse(xhr.responseText);
		alert(response['data']['reason']);
	},
	success: function(result)
	{

		isUpdating = false;
		var response = JSON.parse(result);

		if(response['meta']['status'] == 1)
		{

		}
	}});
}

$('#match_button').click( function()
{
	updateMatching();
});

var isUpdatingMatching = false;
function updateMatching()
{
	if(isUpdatingMatching)
		return;

	isUpdatingMatching = true;
	
	//Send
	var matchingInt = matching;
	if(matchingInt == 0) matchingInt = 1;
	else matchingInt = 0;
	
	$.ajax({type: "POST", url: domain+"/api/account/matching/",	
	data: {matching: matchingInt},
	error: function(xhr, status, error) {
		
		isUpdatingMatching = false;
		var response = JSON.parse(xhr.responseText);
		alert(response['data']['reason']);
	},
	success: function(result)
	{

		isUpdatingMatching = false;
		var response = JSON.parse(result);

		if(response['meta']['status'] == 1)
		{
			location.reload();
		}
	}});
}


var isResending = false;
function requestResend()
{
	if(isResending)
		return;

	isResending = true;
	
	//Send	
	$.ajax({type: "POST", url: domain+"/api/account/verify/",	
	error: function(xhr, status, error) {
		
		isResending = false;
		var response = JSON.parse(xhr.responseText);
		alert(response['data']['reason']);
	},
	success: function(result)
	{

		isResending = false;
		var response = JSON.parse(result);

		if(response['meta']['status'] == 1)
		{
			$('#resend').css('pointer-events', 'none');
			$('#resend').css('opacity', '0');
		}
	}});
}


