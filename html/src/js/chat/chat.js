getMessages();

setInterval( function()
{ 
	getMessages(); 
}, 5000);

function textAreaHandler(event)
{
	if(event.keyCode == 13 && !event.shiftKey)
	{
		event.preventDefault();
		$('#send_textarea').blur();
		sendMessage($('#send_textarea').val());
	}
}

$("#send_textarea").keydown(textAreaHandler).keypress(textAreaHandler);

$('#send_button').click( function()
{
	sendMessage($('#send_textarea').val());
});

$('#send_textarea').bind('input propertychange', function() {

      var text = this.value;
	  
	  if(text.length > maxLength)
	  {
		text = text.substr(0, maxLength);
		$('#send_textarea').val(text);
	  }
	  
	  //Get max length
	  reflectChar();
});

function reflectChar()
{
	var text = $('#send_textarea').val();
	var charsLeft = maxLength - text.length;
	var alpha = text.length / maxLength;

	$('#send_char').html(charsLeft);
	$('#send_char').css('opacity', alpha);
}

var isSendingMessage = false;
function sendMessage(text)
{
	if(isSendingMessage)
		return;
	
	//Verify text
	if(text.length == 0) return;
	
	//SEND
	isSendingMessage = true;
	
	$('#send_textarea').css("color", "#6b6b6b");

	//Send
	$.ajax({type: "POST", url: domain+"/api/chat/message/send/",	
	data: {conversation: conversationID, body: text},
	error: function(xhr, status, error) {
		isSendingMessage = false;
		var response = JSON.parse(xhr.responseText);
		$('#send_textarea').attr("placeholder", "Type message...");
		$('#send_textarea').val(text);
		$('#send_textarea').css("color", "#3d3d3d");
	},
	success: function(result)
	{
		isSendingMessage = false;
		var response = JSON.parse(result);
		getMessages();
		$('#send_textarea').attr("placeholder", "Type message...");
		$('#send_textarea').val('');
		$('#send_textarea').css("color", "#3d3d3d");
	}});
}

var isGettingMessages = false;
function getMessages(newFirst)
{
	if(isGettingMessages)
		return;

	isGettingMessages = true;
	
	//Send
	if(newFirst != 1) newFirst = 0;

	$.ajax({type: "POST", url: domain+"/api/chat/message/retrieve/",	
	data: {conversation: conversationID, new_first: newFirst, newer_than: newestTime},
	error: function(xhr, status, error) {
		
		isGettingMessages = false;
		var response = JSON.parse(xhr.responseText);
		alert(response['data']['reason']);

	},
	success: function(result)
	{

		isGettingMessages = false;
		var response = JSON.parse(result);

		if(response['meta']['status'] == 1)
		{
			var messages = response['data']['messages'];
			
			if(messages.length > 0) 
			{
				var lastMessageIndex = messages.length - 1;
				newestTime = messages[lastMessageIndex]['row']['time'];
			}
			
			loadMessages(messages);
		}
	}});
}

function loadMessages(messages)
{
	var i = 0;
	for(i; i < messages.length; i++)
	{
		var message = messages[i];
		if(message['row']['time'] > newestTime) newestTime = message['row']['time'];
		
		$('#conversation_messages').append(getMessageHTML(message));
	}
	
	if(i > 0)
	$('html, body').scrollTop( $(document).height() );
}

function getMessageHTML(message)
{
		var lr = 'left';
		if(message['row']['userID'] == userID) lr = 'right';
		
		var messageHTML = '';
		
			messageHTML += '<div class="message_container">';
			messageHTML += '<div class="message_body message_'+lr+'">';
			messageHTML += message['row']['body'];
			messageHTML += '</div>';
			messageHTML += '</div>';

		return messageHTML;
}
