$("document").ready(function(){

    $("#file_input").change(function() {
        uploadImage();
    });
});

function uploadImage()
{
	//Send request
	//var image = $('#file_input').val();
	var file_data = $('#file_input').prop('files')[0];   
    var form_data = new FormData(); 
	form_data.append('file', file_data);
	
	$.ajax({type: "POST", url: domain+"/api/upload/image/",	
	data: form_data,
	contentType: false,
	processData: false,
	cache: false,
	dataType: 'text',
	error: function(xhr, status, error) {
		var response = JSON.parse(xhr.responseText);
		//$('#errorcontainer').html(response['data']['reason']);
		alert(response);
	},
	success: function(result)
	{
		var response = JSON.parse(result);
		//alert(response);
		//alert(result);
		if(response['meta']['status'] == 1)
		{
			//email changed
			location.reload();// = domain+'/you/';

		}
		
	}
	});
}

$('#editButton').click( function(event)
{
	event.preventDefault();

	var descriptionString = $('#profile_description').val();

	//Send request
	$.ajax({type: "POST", url: domain+"/api/profile/",	
	data: {description: descriptionString},
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
			location = domain+'/you/';

		}
		else if(response['meta']['status'] == 0)
			$('#errorcontainer').html(response['data']['reason']);	
	}
	});
	
});