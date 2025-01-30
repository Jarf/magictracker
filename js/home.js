document.addEventListener('DOMContentLoaded', function(event) {

	const modal = document.getElementById('modalcontainer');
	const modalcontent = document.getElementById('modalcontent');
	const gameid = document.getElementById('gameid').value;
	const seasonid = document.getElementById('seasonid').value;
	const seasonrankings = document.getElementById('seasonscores');

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
	document.getElementById('gameaddconcede').addEventListener('click', function(){
		clearModal();
		populateModal('concedes');
		toggleModal();
	});
	if(document.getElementById('startnewgame') !== null){
		document.getElementById('startnewgame').addEventListener('click', function(){
			if(confirm('Are you sure?')){
				const xhttp = new XMLHttpRequest();
				xhttp.onload = function(){
					location.assign('/');
				}
				xhttp.open('GET', '/ajax/game.php?do=startNewGame');
				xhttp.send();
			}
		});
	}else if(document.getElementById('startnewseason') !== null){
		document.getElementById('startnewseason').addEventListener('click', function(){
			clearModal();
			populateModal('newseason');
			toggleModal();
		});
	};
	document.getElementById('quotebox').addEventListener('click', function(){
		clearModal();
		populateModal('quote');
		toggleModal();
	});
	document.getElementById('gamedisplay').addEventListener('click', function(){
		clearModal();
		populateModal('games');
		toggleModal();
	});
	document.getElementById('winbin').addEventListener('click', function(){
		clearModal();
		populateModal('winbin');
		toggleModal();
	});
	document.getElementById('deckpicker').addEventListener('click', function(){
		clearModal();
		populateModal('deckpicker');
		toggleModal();
	});
	var seasoncountdowns = document.querySelectorAll('div.row.seasoncountdown');
	for (let i = 0; i < seasoncountdowns.length; i++){
		seasoncountdowns[i].addEventListener('click', function(){
			clearModal();
			populateModal('seasondate');
			toggleModal();
		});
	}

	// Dynamic listeners
	document.addEventListener('click', function(e){
		var button = e.target.id;
		var timestamp = Date.now();
		const xhttp = new XMLHttpRequest();
		if(button === undefined || button.length === 0){
			if(e.target.className !== undefined && e.target.className !== ''){
				button = e.target.className;
			}else{
				button = e.target.closest('.row').id;
			}
		}
		// console.log(button);
		switch (button){
			case 'closemodal':
				toggleModal();
				break;

			case 'killsubmit':
				var killerid = document.getElementById('killkiller').value;
				var killedid = document.getElementById('killkilled').value;
				if(killerid === '' || killedid === ''){
					alert('Select both a killer and killed you absolute cretin.')
				}else{
					clearModal();
					xhttp.onload = function(){
						populateModal('kills');
					}
					xhttp.open('GET', '/ajax/kills.php?do=addkill&killer=' + killerid + '&killed=' + killedid + '&game=' + gameid + '&_=' + timestamp, true);
					xhttp.send();
				}
				break;

			case 'killremove':
				var killerid = e.target.dataset.killerid;
				var killedid = e.target.dataset.killedid;
				if(confirm('Remove ' + e.target.dataset.text + '?')){
					clearModal();
					xhttp.onload = function(){
						populateModal('kills');
					}
					xhttp.open('GET', '/ajax/kills.php?do=removekill&killer=' + killerid + '&killed=' + killedid + '&game=' + gameid + '&_=' + timestamp, true);
					xhttp.send();
				}
				break;

			case 'addpoint':
			case 'subtractpoint':
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
				break;

			case 'savepoints':
				var pointinputs = document.querySelectorAll('ul#pointslist span.pointslistvalues input');
				var getargs = '';
				for (let i = 0; i < pointinputs.length; i++){
					getargs += '&points[' + pointinputs[i].dataset.player + ']=' + pointinputs[i].value;
				}
				if(getargs !== ''){
					xhttp.onload = function(){
						refreshSeasonRankings();
						toggleModal();
					}
					xhttp.open('GET', '/ajax/points.php?do=addPoints&game=' + gameid + getargs + '&_=' + timestamp, true);
					xhttp.send();
				}
				break;

			case 'saveconcedes':
				var concedeinputs = document.querySelectorAll('ul#concedeslist input.concedeinput:checked');
				var getargs = '';
				for (let i = 0; i < concedeinputs.length; i++){
					getargs += '&concede[]=' + concedeinputs[i].value;
				}
				xhttp.onload = function(){
					toggleModal();
				}
				xhttp.open('GET', '/ajax/concedes.php?do=updateConcedes&game=' + gameid + getargs + '&_=' + timestamp, true);
				xhttp.send();
				break;

			case 'savequote':
				var params = new Object();
				params.quote = document.getElementById('quoteinput').value;
				params.author = document.getElementById('quoteauthor').value;
				params.date = document.getElementById('quotedate').value;
				var getargs = '';
				for(name in params){
					getargs += '&' + encodeURIComponent(name)+'='+encodeURIComponent(params[name]);
				}
				getargs = getargs.substring(1)
				xhttp.onload = function(){
					toggleModal();
				}
				xhttp.open('GET', '/ajax/quote.php?' + getargs, true);
				xhttp.send();
				break;

			case 'saveseasondates':
				var startdate = document.getElementById('startdate').value;
				var enddate = document.getElementById('enddate').value;
				xhttp.onload = function(){
					location.assign('/');
				}
				xhttp.open('GET', '/ajax/season.php?do=saveSeasonDates&season=' + seasonid + '&startdate=' + startdate + '&enddate=' + enddate, true);
				xhttp.send();
				break;

			case 'createnewseason':
				var startdate = document.getElementById('startdate').value;
				var enddate = document.getElementById('enddate').value;
				var seasonname = document.getElementById('seasonname').value;
				xhttp.onload = function(){
					location.assign('/');
				}
				xhttp.open('GET', '/ajax/season.php?do=createNewSeason&startdate=' + startdate + '&enddate=' + enddate + '&name=' + encodeURIComponent(seasonname));
				clearModal();
				xhttp.send();
				break;

			case 'savewinbin':
				var params = new Object();
				var winbininputs = document.querySelectorAll('select.winbinselect');
				var getargs = '';
				for (let i = 0; i < winbininputs.length; i++){
					var playerid = winbininputs[i].dataset.player;
					getargs += '&winbin[' + playerid + ']=' + winbininputs[i].value;
				}
				getargs = getargs.substring(1)
				xhttp.onload = function(){
					toggleModal();
				}
				xhttp.open('GET', '/ajax/winbin.php?' + getargs, true);
				xhttp.send();
				break;

			case 'pickdeck':
				var playerid = document.getElementById('deckpickerplayer').value;
				xhttp.open('GET', '/ajax/deckpicker.php?id=' + playerid, true);
				xhttp.onload = function(){
					document.getElementById('deckpickerresult').innerHTML = xhttp.responseText;
				}
				xhttp.send();
				break;
		}
	});

	function clearModal(){
		modalcontent.innerHTML = '<img class="loading" src="/img/timespiral.svg"/>';
	}

	function populateModal(type){
		var timestamp = Date.now();
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function(){
			modalcontent.innerHTML = this.responseText;
		}
		xhttp.open('GET', '/ajax/modalData.php?type=' + type + '&game=' + gameid + '&season=' + seasonid + '&_=' + timestamp, true);
		xhttp.send();
	}

	function toggleModal(){
		modal.classList.toggle('open');
		modal.classList.toggle('closed');
	}

	function refreshSeasonRankings(){
		var timestamp = Date.now();
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function(){
			seasonrankings.innerHTML = this.responseText;
		}
		xhttp.open('GET', '/ajax/modalData.php?type=seasonranking&_=' + timestamp, true);
		xhttp.send();
	}
});