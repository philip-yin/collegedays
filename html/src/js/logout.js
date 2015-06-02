
$('#logoutcontainer').click(function(){
	event.preventDefault();
    tryLogout();
});

function tryLogout()
{	
	$.ajax({type: "POST", url: "http://gocollegedays.com/api/logout/", error: function(xhr, status, error) {

	},
	success: function(result)
	{
		var response = JSON.parse(result);
		//alert(result);
		if(response['meta']['status'] == 1)
		{
			//Match found, refresh page
			window.location.href = "http://gocollegedays.com";
		}
	}});
}