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

var isHidden = false;
$('#match_hide').click( function()
{	
	isHidden = true;
	reflectMatch();
});

$('#match_show').click( function()
{
	isHidden = false;
	reflectMatch();
});

function reflectMatch()
{
	var match_block_height = 'auto';
	var match_timer_height = match_block_height;

	//Show show
	if(isHidden)
	{	//Hide match
		$('#match_block').animate({height: '0px'}, 250);
		$('#match_content').animate({opacity: '0'}, 250);
	
		$('#match_show').css('display', 'block');
		$('#match_show').animate({opacity: '1'}, 250);
		$('#match_banner').animate({opacity: '0'}, 250);
		
		$('#match_timer_container').css('display', 'block');
		$('#match_timer_container').animate({height: '300px'}, 250);
		$('#match_timer_content').animate({opacity: '1'}, 450);
	}
	else
	{
		//Hide match
		$('#match_block').animate({height: '390px'}, 250);
		$('#match_content').animate({opacity: '1'}, 250);
	
		$('#match_show').animate({opacity: '0'}, 250, function() { $('#match_show').css('display', 'none'); });
		$('#match_banner').animate({opacity: '1'}, 250);
		
		$('#match_timer_container').css('display', 'none');
		$('#match_timer_container').animate({height:'0px'}, 250);
		$('#match_timer_content').animate({opacity: '0'}, 450);
	}

}

setInterval(tickTime, 1000);
function tickTime()
{
	secondsLeft--;
	
	if(secondsLeft <= 0)
		location.reload();
	
	var hours =  Math.floor(secondsLeft / (60*60));
	var mins =  Math.floor((secondsLeft - (hours * 60 * 60)) / 60);
	var seconds =  Math.floor(secondsLeft - (hours * 60 * 60) - (mins * 60));
	
	$('#match_timer').html(hours+'h '+mins+'m '+seconds+'s');
}