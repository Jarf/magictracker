document.addEventListener('DOMContentLoaded', function(event) {
	const seasonId = document.getElementById('statseasonselect').value;
	const fontFamily = 'planewalkerregular';

	document.getElementById('statseasonselect').addEventListener('change', function(){
		location.assign('/stats/' + this.value);
	});

	// Points line chart
	let xhttp1 = new XMLHttpRequest();
	let url = '/ajax/chartData.php?chart=points';
	if(seasonId !== ''){
		url += '&season=' + seasonId;
	}
	xhttp1.responseType = 'json';
	xhttp1.open("GET", url, true);
	xhttp1.onreadystatechange = function(){
		if(this.readyState == 4 && this.status == 200){
			new Chart(
				document.getElementById('pointsgraphcanvas'),
				{
					type: 'line',
					data: this.response,
					options: {
						plugins: {
							legend: {
								labels: {
									font:{
										family: fontFamily
									}
								}
							},
							title: {
								display: true,
								text: 'Sum of Points Scored',
								font: {
									family: fontFamily
								}
							}
						}
					}
				}
			);
		}
	}
	xhttp1.send();

	// KD line chart
	url = '/ajax/chartData.php?chart=kd';
	if(seasonId !== ''){
		url += '&season=' + seasonId;
	}
	let xhttp2 = new XMLHttpRequest();
	xhttp2.responseType = 'json';
	xhttp2.open("GET", url, true);
	xhttp2.onreadystatechange = function(){
		if(this.readyState == 4 && this.status == 200){
			new Chart(
				document.getElementById('kdgraphcanvas'),
				{
					type: 'line',
					data: this.response,
					options: {
						plugins: {
							legend: {
								labels: {
									font:{
										family: fontFamily
									}
								}
							},
							title: {
								display: true,
								text: 'K/D Ratio History',
								font: {
									family: fontFamily
								}
							}
						}
					}
				}
			);
		}
	}
	xhttp2.send();

	// Wins polar area chart
	url = '/ajax/chartData.php?chart=wins';
	if(seasonId !== ''){
		url += '&season=' + seasonId;
	}
	let xhttp3 = new XMLHttpRequest();
	xhttp3.responseType = 'json';
	xhttp3.open("GET", url, true);
	xhttp3.onreadystatechange = function(){
		if(this.readyState == 4 && this.status == 200){
			new Chart(
				document.getElementById('winsgraphcanvas'),
				{
					type: 'polarArea',
					data: this.response,
					options: {
						maintainAspectRation: false,
						plugins: {
							legend: {
								labels: {
									font:{
										family: fontFamily
									}
								}
							},
							title: {
								display: true,
								text: 'Game Wins',
								font: {
									family: fontFamily
								}
							}
						},
						scale: {
							ticks: {
								precision: 0
							}
						}
					}
				}
			);
		}
	}
	xhttp3.send();
});