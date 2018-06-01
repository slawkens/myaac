$(function() {
	$('#character_name').blur(function() {
		checkName();
	});
});

var eventId = 0;
var lastSend = 0;

function checkName()
{
	if(eventId != 0)
	{
		clearInterval(eventId)
		eventId = 0;
	}

	if(document.getElementById("character_name").value=="")
	{
		$('#character_error').html('<span color="red">Please enter new character name.</span>');
		return;
	}

	//anti flood
	var date = new Date;
	var timeNow = parseInt(date.getTime());

	if(lastSend != 0)
	{
		if(timeNow - lastSend < 1100)
		{
			eventId = setInterval('checkName()', 1100)
			return;
		}
	}

	var name = document.getElementById("character_name").value;
	$.getJSON("tools/validate.php", { name: name, uid: Math.random() },
		function(data){
			if(data.hasOwnProperty('success')) {
				$('#character_error').html ('<span style="color: green">' + data.success + '</span>');
				$('#character_indicator').attr('src', 'images/global/general/ok.gif');
			}
			else if(data.hasOwnProperty('error')) {
				$('#character_error').html('<span style="color: red">' + data.error + '</span>');
				$('#character_indicator').attr('src', 'images/global/general/nok.gif');
			}

            lastSend = timeNow;
	});
}