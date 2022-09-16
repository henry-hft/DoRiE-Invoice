$(document).ready((() => {
	url = new URL(window.location.href);
	
	if (url.searchParams.get('id')) {
		if(isNumeric(url.searchParams.get('id'))){
			getOrder(url.searchParams.get('id'))
		} else {
			alert("Invalid order ID");
		}
	} else {
		alert("Missing id parameter");
	}
	
	$("#button_pay").click(function(){
		payInvoice(url.searchParams.get('id'))
	});
	
	$("#button_cancel").click(function(){
		cancelInvoice(url.searchParams.get('id'))
	});
	   
	async function getOrder(id) {
        try {
            const response = await fetch('api/invoice.php?id=' + id, {
                method: 'get',
                headers: { 'content-type': 'application/json' },
                credentials: 'include'
            })
			
            const newRecord = await response.json();
            if (newRecord.error == false) {
				$(document).prop("title", "DoRiE - Invoice #" + newRecord.id);
                $('#orderid').text("Invoice #" + newRecord.id)
				$('#seat').text("Seat: " + newRecord.seat)
				$('#status').text("Status: " + capitalizeFirstLetter(newRecord.status))
				$('#time').text(newRecord.time)
				
				if(newRecord.paid == 1){
					$("#paid").addClass("badge-success");
					$('#paid').text("Paid")
				} else {
					$("#paid").addClass("badge-danger");
					$('#paid').text("Unpaid")
					
					if(newRecord.status !== "canceled"){
						$("#button_cancel").prop("disabled", false);
						$("#button_pay").prop("disabled", false);
					}
				}
				
				var x = "";
                newRecord.items.forEach(e => {
                    x += '<tr><td><h5 class="mb-1">' + e.id + ' | ' + e.name + '</h5>' + e.description + '<br>' + e.time + '</td><td class="font-weight-bold align-middle text-right text-nowrap">' + e.price + ' €</td></tr>'
				})
                $('#total').text("Total: " + newRecord.total + " €")
                $('#items').html(x)

            } else {
                alert(newRecord.message)
            }

        } catch (err) {
            console.error(err);
        }

    }
	
	async function payInvoice(id) {
		try {
            const response = await fetch('api/status.php?function=pay&id=' + id, {
                method: 'get',
                headers: { 'content-type': 'application/json' },
                credentials: 'include'
            })
			
            const newRecord = await response.json();
            if (newRecord.error == false) {
				if(!alert(newRecord.message)){
					window.location.reload();  
				}
            } else {
                alert(newRecord.message)
            }

        } catch (err) {
            console.error(err);
        }
	}	

	async function cancelInvoice(id) {
		try {
            const response = await fetch('api/status.php?function=cancel&id=' + id, {
                method: 'get',
                headers: { 'content-type': 'application/json' },
                credentials: 'include'
            })
			
            const newRecord = await response.json();
            if (newRecord.error == false) {
                if(!alert(newRecord.message)){
					window.location.reload();  
				}
            } else {
                alert(newRecord.message)
            }

        } catch (err) {
            console.error(err);
        }
	}	
	
	function isNumeric(value) {
		return /^\d+$/.test(value);
	}
	
	function capitalizeFirstLetter(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}
}))