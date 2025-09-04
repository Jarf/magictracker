document.addEventListener('DOMContentLoaded', function(event) {
	var seasonId = document.getElementById('statseasonselect').value;

	document.getElementById('statseasonselect').addEventListener('change', function(){
		location.assign('/stats/' + this.value);
	});

	let xhttp = new XMLHttpRequest();
	let url = '/ajax/chartData.php';
	if(seasonId !== ''){
		url += '?season=' + seasonId;
	}
	xhttp.responseType = 'json';
	xhttp.open("GET", url, true);
	xhttp.onreadystatechange = function(){
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
										family: 'planewalkerregular'
									}
								}
							}
						}
					}
				}
			);
		}
	}
	xhttp.send();

});