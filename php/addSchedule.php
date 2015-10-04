<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
$mysqli = Database::connection();
if ($user instanceof CurrentUser) {
	if ($user->user_id == 1069062649779910) {
		if (isset($_POST['submit'])) {
			foreach ($_POST as $key=>$val) {
				$date = date('Y-m-d', strtotime("$key this week"));
				$times = explode(', ', $val);
				foreach ($times as $time) {
					$insert = "INSERT INTO `athletic_centre` (datename, begin_datetime) VALUES('Badminton Unavailable', '" . $date . ' ' . $time . "')
					ON DUPLICATE KEY UPDATE date_id = date_id";
					echo $insert;
					$result = $mysqli->query($insert)
					or die ($mysqli->error);
				}
			}
			echo 'submitted';
		}
	}
	else {
		header('Location: http://uoftbaddy.ca');
	}
}
else {
	header('Location: /index.php');
}
?>
<html>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		Monday
		<input type="text" name="monday">
		<hr>
		Tuesday
		<input type="text" name="tuesday">
		<hr>
		Wednesday
		<input type="text" name="wednesday">
		<hr>
		Thursday
		<input type="text" name="thursday">
		<hr>
		Friday
		<input type="text" name="friday">
		<hr>
		Saturday
		<input type="text" name="saturday">
		<hr>
		Sunday
		<input type="text" name="sunday">
		<hr>
		<input type="submit" name="submit" value="submit">
	</form>
</html>