		eventId = 0;
		lastSend = 0;

function checkName()
{
	if(eventId != 0)
	{
		clearInterval(eventId)
		eventId = 0;
	}

	if(document.getElementById("newcharname").value=="")
	{
		document.getElementById("name_check").innerHTML = '<b><font color="red">Please enter new character name.</font></b>';
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

	var newcharname = document.getElementById("newcharname").value;
	$.get("tools/validate.php", { name: newcharname, uid: Math.random() },
		function(data){
			document.getElementById("name_check").innerHTML = data;
			lastSend = timeNow;
	});
	
	lastSend = timeNow;
}