$(document).ready((() => {
	var images = [];
	var image = "";
	var text = "";
	var interval = 5000;

	images[0] = ['images/ims-chips.png'];
	images[1] = ['images/fzi.png'];
	images[2] = ['images/hahn-schickard.png'];
	var index = 0;

	function change() {
		getEvent();
		if (image !== "" && interval > 0) {
			document.getElementById("mainPhoto").src = image;
			$('#text').html(text);
		} else {
			interval = 5000;
			document.getElementById("mainPhoto").src = images[index];
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
				interval = data.duration;
				image = data.image
			} else {
				qrcode = "";
			}
		})
		.catch(function(error) {
			console.log(error);
		});
	}	
}))