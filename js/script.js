$(document).ready((() => {
	var images = [];
	var image = "";
	var text = "";
	var interval = 5000;
	var permanent = false;
	var qrCodeChecks = 0;

	images[0] = ['images/ims-chips.png'];
	images[1] = ['images/fzi.png'];
	images[2] = ['images/hahn-schickard.png'];
	var index = 0;

	function change() {
		getEvent();
		
		if(image === "" && text !== ""){
			$("#text").css("padding-top", "400px");
		} else {
			$("#text").css("padding-top", "0px");
		}
		
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
				
				if(qrCodeChecks > 0){
					if(data.text == "Invoice opened"){
						qrCodeChecks = 0;
						interval = data.duration;
					} else {
						qrCodeChecks--;
						if(qrCodeChecks == 0){
							interval = 1000;
						}
					}
				} else {
				
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
					
						if((data.image).includes("qrcode.php")){
							qrCodeChecks = data.duration / 5000;
							interval = 5000;
						} else {
							interval = data.duration;
						}
						image = data.image;
						text = data.text;
					}
				}
				
				
			} else {
				text = "Unknown error";
				interval = 5000;
			}
		})
		.catch(function(error) {
			console.log(error);
		});
	}	
}))
