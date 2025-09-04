document.addEventListener('DOMContentLoaded', function(event) {
	const seasonId = document.getElementById('statseasonselect').value;
	const fontFamily = 'planewalkerregular';

	document.getElementById('statseasonselect').addEventListener('change', function(){
		location.assign('/stats/' + this.value);
	});

	// Points bar chart
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
});