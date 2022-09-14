$(document).ready((() => {
	var images = [];
	var qrcode = "";
	var id = 0;

	images[0] = ['images/ims-chips.png'];
	images[1] = ['images/fzi.png'];
	images[2] = ['images/hahn-schickard.png'];
	var index = 0;

	function change() {
		//var qrcode = test();
		test();
		if (qrcode !== "") {
			document.getElementById("mainPhoto").src = qrcode;

			var interval = 30000;
		} else {
			var interval = 5000;
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
	
	
	function test() {
		fetch('api/fetch.php', {
                method: 'get',
                headers: { 'content-type': 'application/json' }
            })
		.then((response) => response.json())
		.then(function(data) {
			
			if(data.error === false){
			//	images.push([newRecord.qrcode]);
			//	console.log(images);
			//	interval = 30000;
				qrcode = data.qrcode
			} else {
				qrcode = "";
			}
		})
		.catch(function(error) {
			console.log(error);
		});
	}
	
	function checkStatus() {
		if(id > 0){
		fetch('api/status.php?function=check&id=' + id, {
                method: 'get',
                headers: { 'content-type': 'application/json' }
            })
		.then((response) => response.json())
		.then(function(data) {
			
			if(data.error === false){
			//	images.push([newRecord.qrcode]);
			//	console.log(images);
			//	interval = 30000;
				qrcode = data.qrcode
			} else {
				qrcode = "";
			}
		})
		.catch(function(error) {
			console.log(error);
		});
		}
		setTimeout(checkStatus, 2000);
	}
	
	async function qrcode() {
		try {
            const response = await fetch('api/fetch.php', {
                method: 'get',
                headers: { 'content-type': 'application/json' },
                credentials: 'include'
            })
			
            const newRecord = await response.json();
            if (newRecord.error == false) {
				images.push([newRecord.qrcode]);
				console.log(images);
				interval = 30000;
            } 

        } catch (err) {
            console.error(err);
        }
	}
	
}))