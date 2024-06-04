document.addEventListener('DOMContentLoaded', function(event) {

	const modal = document.getElementById('modalcontainer');
	const modalcontent = document.getElementById('modalcontent');

	document.getElementById('gameaddkill').addEventListener('click', function(){
		clearModal();
		populateModal('kills');
		toggleModal();
	});
	document.getElementById('gameaddpoints').addEventListener('click', function(){
		toggleModal();
	});
	document.getElementById('closemodal').addEventListener('click', function(){
		toggleModal();
	});

	function clearModal(){
		// modalcontent.innerHTML = 'test';
	}

	function populateModal(type){
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function(){
			modalcontent.innerHTML = this.responseText;
		}
		xhttp.open('GET', '/ajax/modalData.php?type=' + type, true);
		xhttp.send();
	}

	function toggleModal(){
		modal.classList.toggle('open');
		modal.classList.toggle('closed');
	}
});