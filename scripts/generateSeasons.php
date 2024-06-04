<?php
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
/* Algorithms taken from Meeus Astronomical Algorithms, 2nd edition */

/* Run the php script on the command line:
 *   php equinox.php <year> <what>
 * with <what> being MAR, JUN, SEP, or DEC
 *
 * For the the Summer Solstice of 2020:
 *   php equinox.php 2020 JUN
 */

/* Constants */
define('MAR', 0);
define('JUN', 1);
define('SEP', 2);
define('DEC', 3);

/* Tables from Meeus, page 178 */
/* Table 27.A, for the years -1000 to 1000 */
$yearTable0 = [
	MAR => [ 1721139.29189, 365242.13740, +0.06134, +0.00111, -0.00071 ],
	JUN => [ 1721233.25401, 365241.72562, -0.05323, +0.00907, +0.00025 ],
	SEP => [ 1721325.70455, 365242.49558, -0.11677, -0.00297, +0.00074 ],
	DEC => [ 1721414.39987, 365242.88257, -0.00769, -0.00933, -0.00006 ],
];

/* Table 27.B, for the years 1000 to 3000 */
$yearTable2000 = [
	MAR => [ 2451623.80984, 365242.37404, +0.05169, -0.00411, -0.00057 ],
	JUN => [ 2451716.56767, 365241.62603, +0.00325, +0.00888, -0.00030 ],
	SEP => [ 2451810.21715, 365242.01767, -0.11575, +0.00337, +0.00078 ],
	DEC => [ 2451900.05952, 365242.74049, -0.06223, -0.00823, +0.00032 ],
];

/* Meeus, page 177 */
function calculateJDE0($year, $which)
{
	global $yearTable0, $yearTable2000;

	$table = $year < 1000 ? $yearTable0 : $yearTable2000;
	$Y     = $year < 1000 ? ($year / 1000) : (($year - 2000) / 1000);
	$terms = $table[$which];

	$JDE0 = $terms[0] +
		($terms[1] * $Y) +
		($terms[2] * $Y * $Y) +
		($terms[3] * $Y * $Y * $Y) +
		($terms[4] * $Y * $Y * $Y * $Y);

	return $JDE0;
}

/* Meeus, Table 27.C, page 179 */
function calculateS($T)
{
	$table = [
		[ 485, 324.96,   1934.136 ],
		[ 203, 337.23,  32964.467 ],
		[ 199, 342.08,     20.186 ],
		[ 182,  27.85, 445267.112 ],
		[ 156,  73.14,  45036.886 ],
		[ 136, 171.52,  22518.443 ],
		[  77, 222.54,  65928.934 ],
		[  74, 296.72,   3034.906 ],
		[  70, 243.58,   9037.513 ],
		[  58, 119.81,  33718.147 ],
		[  52, 297.17,    150.678 ],
		[  50,  21.02,   2281.226 ],
		[  45, 247.54,  29929.562 ],
		[  44, 325.15,  31555.956 ],
		[  29,  60.93,   4443.417 ],
		[  18, 155.12,  67555.328 ],
		[  17, 288.79,   4562.452 ],
		[  16, 198.04,  62894.029 ],
		[  14, 199.76,  31436.921 ],
		[  12,  95.39,  14577.848 ],
		[  12, 287.11,  31931.756 ],
		[  12, 320.81,  34777.259 ],
		[   9, 227.73,   1222.114 ],
		[   8,  15.45,  16859.074 ],
	];
	
	$sum = 0;
	foreach( $table as $term ) {
		$c = $term[0] * cos(deg2rad( $term[1] + ($term[2] * $T) ));

		$sum += $c;
	}
	return $sum;
}

/* Meeus, chapter 10 */
function deltaDTtoUT($year)
{
	$t = ($year - 2000) / 100;

	if ($year < 948) {
		$dT = 2177 + (497 * $t) + (44.1 * $t * $t);
	}
	
	/* There is a table on page 79 for the years 1620 to 1998, which I didn't
	 * bother to include here */
	
	if (($year >= 948 && $year < 1600) || $year >= 2000) {
		$dT = 102 + (102 * $t) + (25.3 * $t * $t);
	}

	/* to avoid a discontinuity at A.D. 2000, it is advised to add the
	 * correction +0.37 x (year - 2100) for the years 2000 to 2100 */
	if ($year >= 2000 && $year < 2100) {
		$dT += 0.37 * ($year - 2100);
	}

	return $dT;
}

function JDEtoTimestamp($JDE)
{
	$tmp = $JDE;
	$tmp -= 2440587.5;
	$tmp *= 86400;

	return $tmp;
}

function getSeason($year, $which){
	if($which > 3){
		$which = 0;
		$year++;
	}
	/* Meeus, page 177 */
	$JDE0 = calculateJDE0($year, $which);
	$T   = ($JDE0 - 2451545.0) / 36525;
	$W = (35999.373 * $T) - 2.47;
	$L = 1 + 0.0334 * cos(deg2rad($W)) + 0.0007 * cos(2 * deg2rad($W));
	$S = calculateS($T);

	/* Meeus, page 178 */
	$JDE = $JDE0 + ((0.00001 * $S) / $L);

	/* Convert TD to PHP Date */
	$date = JDEtoTimestamp($JDE);
	return $date;
}

function generateSeasonName($year, $which){
	if($which > 3){
		$which = 0;
		$year++;
	}
	$seasons = array(
		'Spring',
		'Summer',
		'Autumn',
		'Winter'
	);
	$name = $seasons[$which] . ' ' . $year;
	return $name;
}

$month = intval(date('n'));
$month = (round($month / 3) - 1);
$year = intval(date('Y'));
$nextseasonstart = date('Y-m-d', getSeason($year, $month));
$nextseasonend = date('Y-m-d', (getSeason($year, $month + 1) - 86400));
$seasonname = generateSeasonName($year, $month);

$db = new db();
$sql = 'SELECT id FROM season WHERE startDate = :startDate AND endDate = :endDate';
$db->query($sql);
$db->bind('startDate', $nextseasonstart);
$db->bind('endDate', $nextseasonend);
$db->execute();
if($db->rowCount() === 0){
	$sql = 'INSERT INTO season (name, startDate, endDate) VALUES (:name, :startDate, :endDate)';
	$db->query($sql);
	$db->bind('name', $seasonname);
	$db->bind('startDate', $nextseasonstart);
	$db->bind('endDate', $nextseasonend);
	$db->execute();
}
?>