$(document).ready((() => {
	var images = [];
	var image = "";
	var text = "";
	var interval = 5000;
	var permanent = false;

	images[0] = ['images/ims-chips.png'];
	images[1] = ['images/fzi.png'];
	images[2] = ['images/hahn-schickard.png'];
	var index = 0;

	function change() {
		getEvent();
		if ((image !== "" || text !== "") && interval > 0) {
			document.getElementById("mainPhoto").src = image;
			$('#text').html(text);
		} else {
			interval = 5000;
			document.getElementById("mainPhoto").src = images[index];
			$('#text').html("");
		if (index == 2) {
			index = 0;
		} else {
			index++;
		}
		}
		
		

	setTimeout(change, interval);
	}

	window.onload = change();
	
	
	function getEvent() {
		fetch('api/event.php', {
                method: 'get',
                headers: { 'content-type': 'application/json' }
            })
		.then((response) => response.json())
		.then(function(data) {
			
			if(data.error === false){
				
				if ((data.image !== "" || data.text !== "") && data.duration < 0) {
					permanent = true;
					
					interval = 5000;
					image = data.image;
					text = data.text;
					
				} else if (data.image === "" && data.text === "" && data.duration < 0) {
					permanent = false;
					
					image = "";
					text = "";
				}
				
				if(!permanent){
					interval = data.duration;
					image = data.image;
					text = data.text;
				}
				
			} else {
				qrcode = "";
			}
		})
		.catch(function(error) {
			console.log(error);
		});
	}	
}))
