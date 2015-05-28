$('#hello_button').click( function()
{
	$.ajax({type: "POST", url: "http://gocollegedays.com/api/match/sayhi/",  
	error: function(xhr, status, error) {

		var response = JSON.parse(xhr.responseText);
		alert(xhr.resrponseText);
	},
	success: function(result)
	{
		var response = JSON.parse(result);
		if(response['meta']['status'] == 1)
		{
			//Match found, refresh page
			location.reload();
		}
	}});
});