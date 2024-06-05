document.addEventListener('DOMContentLoaded', function(event) {

	const modal = document.getElementById('modalcontainer');
	const modalcontent = document.getElementById('modalcontent');
	const gameid = document.getElementById('gameid').value;

	document.getElementById('gameaddkill').addEventListener('click', function(){
		clearModal();
		populateModal('kills');
		toggleModal();
	});
	document.getElementById('gameaddpoints').addEventListener('click', function(){
		clearModal();
		populateModal('points');
		toggleModal();
	});
	document.getElementById('startnewgame').addEventListener('click', function(){
		if(confirm('Are you sure?')){

		}
	});

	// Dynamic listeners
	document.addEventListener('click', function(e){
		var button = e.target.id;
		if(button === undefined || button.length === 0){
			button = e.target.className;
		}
		console.log(button);
		if(button === 'closemodal'){
			toggleModal();
		}else if(button === 'killsubmit'){
			var killerid = document.getElementById('killkiller').value;
			var killedid = document.getElementById('killkilled').value;
			if(killerid === '' || killedid === ''){
				alert('Select both a killer and killed you absolute cretin.')
			}else{
				clearModal();
				const xhttp = new XMLHttpRequest();
				xhttp.onload = function(){
					populateModal('kills');
				}
				xhttp.open('GET', '/ajax/kills.php?do=addkill&killer=' + killerid + '&killed=' + killedid + '&game=' + gameid);
				xhttp.send();
			}
		}else if(button === 'killremove'){
			var killerid = e.target.dataset.killerid;
			var killedid = e.target.dataset.killedid;
			if(confirm('Remove ' + e.target.dataset.text + '?')){
				clearModal();
				const xhttp = new XMLHttpRequest();
				xhttp.onload = function(){
					populateModal('kills');
				}
				xhttp.open('GET', '/ajax/kills.php?do=removekill&killer=' + killerid + '&killed=' + killedid + '&game=' + gameid);
				xhttp.send();
			}
		}else if(button === 'addpoint' || button === 'subtractpoint'){
			var pointinput = e.target.parentElement.querySelector('input');
			var pointvalue = pointinput.value;
			if(button === 'addpoint'){
				pointvalue++;
			}else{
				pointvalue--;
			}
			if(pointvalue >= 0 && pointvalue <= 2){
				pointinput.value = pointvalue;
			}
		}else if(button === 'savepoints'){
			var pointinputs = document.querySelectorAll('ul#pointslist span.pointslistvalues input');
			var getargs = '';
			for (let i = 0; i < pointinputs.length; i++){
				getargs += '&points[' + pointinputs[i].dataset.player + ']=' + pointinputs[i].value;
			}
			if(getargs !== ''){
				const xhttp = new XMLHttpRequest();
				xhttp.onload = function(){
					populateModal('points');
				}
				xhttp.open('GET', '/ajax/points.php?do=addPoints&game=' + gameid + getargs);
				xhttp.send();
			}
		}
	});

	function clearModal(){
		modalcontent.innerHTML = '<img class="loading" src="/img/timespiral.svg"/>';
	}

	function populateModal(type){
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function(){
			modalcontent.innerHTML = this.responseText;
		}
		xhttp.open('GET', '/ajax/modalData.php?type=' + type + '&game=' + gameid, true);
		xhttp.send();
	}

	function toggleModal(){
		modal.classList.toggle('open');
		modal.classList.toggle('closed');
	}
});