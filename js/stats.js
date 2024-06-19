document.addEventListener('DOMContentLoaded', function(event) {
	document.getElementById('statseasonselect').addEventListener('change', function(){
		location.assign('/stats/' + this.value);
	});
});