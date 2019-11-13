<?php

// header to return JSON to the jQuery Ajax request
header('Content-Type: application/json');

// function for removing last colon
function removeLastChar($string){
	$string = substr($string, 0, -1);
	return $string;
}

// function to create files
function createFile($database, $myFile, $stringData){
	$path = "generated/".$database.date("Y-m-d_H-i");
	if (file_exists($path)) {

	} else {
		mkdir($path, 0777);
	}
	if (file_exists($path."/includes")) {

	} else {
		mkdir($path."/includes", 0777);
	}
	$fh = fopen($path."/".$myFile.".php", 'w') or die("can't open file");
	fwrite($fh, $stringData);
	fclose($fh);
}

// function to return a random glyphicon to be used in the side bar links
function random_glyphicon(){
	$glyphicons = array("asterisk", "plus", "euro", "eur", "minus", "cloud", "envelope", "pencil", "glass", "music", "search", "heart", "star", "star-empty", "user", "film", "th-large", "th", "th-list", "ok", "remove", "zoom-in", "zoom-out", "off", "signal", "cog", "file", "time", "road", "download-alt", "download", "upload", "inbox", "play-circle", "repeat", "refresh", "list-alt", "lock", "flag", "headphones", "volume-off", "volume-down", "volume-up", "qrcode", "barcode", "tag", "tags", "book", "bookmark", "print", "camera", "font", "bold", "italic", "text-height", "text-width", "align-left", "align-center", "align-right", "align-justify", "list", "indent-left", "indent-right", "facetime-video", "picture", "map-marker", "adjust", "tint", "share", "check", "move", "step-backward", "fast-backward", "backward", "play", "pause", "stop", "forward", "fast-forward", "step-forward", "eject", "chevron-left", "chevron-right", "plus-sign", "minus-sign", "remove-sign", "ok-sign", "question-sign", "info-sign", "screenshot", "remove-circle", "ok-circle", "ban-circle", "arrow-left", "arrow-right", "arrow-up", "arrow-down", "share-alt", "resize-full", "resize-small", "exclamation-sign", "gift", "leaf", "fire", "eye-open", "eye-close", "warning-sign", "plane", "calendar", "random", "comment", "magnet", "chevron-up", "chevron-down", "retweet", "shopping-cart", "folder-close", "folder-open", "resize-vertical", "resize-horizontal", "hdd", "bullhorn", "bell", "certificate", "thumbs-up", "thumbs-down", "hand-right", "hand-left", "hand-up", "hand-down", "circle-arrow-right", "circle-arrow-left", "circle-arrow-up", "circle-arrow-down", "globe", "wrench", "tasks", "filter", "briefcase", "fullscreen", "dashboard", "paperclip", "heart-empty", "link", "phone", "pushpin", "usd", "gbp", "sort", "sort-by-alphabet", "sort-by-alphabet-alt", "sort-by-order", "sort-by-order-alt", "sort-by-attributes", "sort-by-attributes-alt", "unchecked", "expand", "collapse-down", "collapse-up", "log-in", "flash", "new-window", "record", "save", "open", "saved", "import", "export", "send", "floppy-disk", "floppy-saved", "floppy-remove", "floppy-save", "floppy-open", "credit-card", "transfer", "cutlery", "header", "compressed", "earphone", "phone-alt", "tower", "stats", "sd-video", "hd-video", "subtitles", "sound-stereo", "sound-dolby", "sound-5-1", "sound-6-1", "sound-7-1", "copyright-mark", "registration-mark", "cloud-download", "cloud-upload", "tree-conifer", "tree-deciduous", "cd", "save-file", "open-file", "level-up", "copy", "paste", "alert", "equalizer", "king", "queen", "pawn", "bishop", "knight", "baby-formula", "tent", "blackboard", "bed", "apple", "erase", "hourglass", "lamp", "duplicate", "piggy-bank", "scissors", "bitcoin", "btc", "xbt", "yen", "jpy", "ruble", "rub", "scale", "ice-lolly", "ice-lolly-tasted", "education", "option-horizontal", "option-vertical", "menu-hamburger", "modal-window", "oil", "grain", "sunglasses", "text-size", "text-color", "text-background", "object-align-top", "object-align-bottom", "object-align-horizontal", "object-align-left", "object-align-vertical", "object-align-right", "triangle-right", "triangle-left", "triangle-bottom", "triangle-top", "console", "superscript", "subscript", "menu-left", "menu-right", "menu-down", "menu-up");

	$rand = array_rand($glyphicons, 1); 
	return $glyphicons[$rand];
}


if($_POST) {

	// getting parameters from the ajax request
	$action 	= $_POST["action"];
	$host 		= $_POST["host"];
	$username 	= $_POST["username"];
	$password 	= $_POST["password"];

	// connecting to the host
	$link = mysqli_connect($host, $username, $password);
	if (!$link) {
		die(json_encode(array('status' => 'error','message'=> 'Could not connect: ' . mysqli_error($link))));
	}

	// if the action is to connect, we request all databases
	if($action == "connect"){
		$result = '';
		$res = mysqli_query($link, "SHOW DATABASES");
		if (!$res) {
			die(json_encode(array('status' => 'error','message'=> 'Listing databases failed: ' . mysqli_error($link))));
		}
		while ($row = mysqli_fetch_assoc($res)) {
			$result .= "<option value=\"" .$row['Database'] . "\">" .$row['Database'] . "</option>";
		}

		if(!$result){
			echo json_encode(array('status' => 'error','message'=> 'Error in data collection'));
		} else {
			echo json_encode(array('status' => 'success','result'=> $result));
		}

	}

	// process starts if the action is to generate the admin panel
	else if($action == "generate") {

		// get the database name
		$database = $_POST["database"];

		// gather success info and display to user at the end
		$message = "The operations that were performed are: <ul>";

		// select the database
		$linkdb = mysqli_select_db($link, $database);
		if (!$linkdb) {
			die(json_encode(array('status' => 'error','message'=> 'Couldn\'t select database: ' . mysqli_error($link))));
		}

		// creating the users table if it doesn't exist
		$sql = "CREATE TABLE IF NOT EXISTS `users` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `email` varchar(255) NOT NULL, `password` varchar(255) NOT NULL, `role` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2";

		$res = mysqli_query($link, $sql);
		if (!$res) {
			die(json_encode(array('status' => 'error','message'=> 'Couldn\'t create users table: ' . mysqli_error($link))));
		}

		// inserting the entry for admin, password is MD5'ed
		mysqli_query($link, "INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES (1, 'Admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1)");

		// loop to show all the tables and fields
		$loop = mysqli_query($link, "SHOW FULL TABLES from $database WHERE Table_Type = 'BASE TABLE'");
		
		if (!$loop) {
			die(json_encode(array('status' => 'error','message'=> 'Couldn\'t select table: ' . mysqli_error($link))));
		}

		// split the list of tables to those that have a primary key to employ as the id and those that do not have
		$loop_no_key_tables = array();

		// the generation process starts here
		// collecting DB connection info to generate includes/connect.php file
		$connection = "<?php
		\$link = mysqli_connect(\"$host\", \"$username\", \"$password\");
		mysqli_select_db(\$link, \"$database\");  
		mysqli_query(\$link, \"SET CHARACTER SET utf8\");
		session_start();
		?>
		";

		// starting the save.php file which controls create, update, and delete operations on the database.
		$save = "<?php
		include(\"includes/connect.php\");

		$"."cat = $"."_POST['cat'];
		$"."cat_get = $"."_GET['cat'];
		$"."act = $"."_POST['act'];
		$"."act_get = $"."_GET['act'];
		$"."id = $"."_POST['id'];
		$"."id_get = $"."_GET['id'];

		";

		// collecting the home.php page which shows a full database table of contents
		$index = "<?php
		include \"includes/header.php\";
		?>
		<table class=\"table table-striped\">
		<tr>
		<th class=\"not\">Table</th>
		<th class=\"not\">Entries</th>
		</tr>
		";

		// collecting data for the includes/header.php page which is the header of all pages
		$header = '<?php
		if ($_COOKIE["auth"] != "admin_in"){header("location:"."./");}
			include("includes/connect.php");
			include("includes/data.php");
			?>
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<meta name="author" content="@housamz">

				<meta name="description" content="Mass Admin Panel">
				<title>' .ucfirst($database). ' Admin Panel</title>
	
				<!-- Latest compiled and minified CSS -->
				<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous"> -->

				<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-h21C2fcDk/eFsW9sC9h0dhokq5pDinLNklTKoxIZRUn3+hvmgQSffLLQ4G4l2eEr" crossorigin="anonymous">
				
				<!-- Custom CSS -->
				<link rel="stylesheet" href="includes/style.css">
				<link href="//cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />

				<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
				<!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
				<!--[if lt IE 9]>
					<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
					<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
				<![endif]-->
					
			</head>

			<body>

			<div class="wrapper">
				<!-- Sidebar Holder -->
				<nav id="sidebar" class="bg-primary">
					<div class="sidebar-header">
						<h3>
							' .ucfirst($database). ' Admin Panel<br>
							<i id="sidebarCollapse" class="glyphicon glyphicon-circle-arrow-left"></i>
						</h3>
						<strong>
							' .ucfirst($database). '<br>
							<i id="sidebarExtend" class="glyphicon glyphicon-circle-arrow-right"></i>
						</strong>
						
					</div><!-- /sidebar-header -->

					<!-- start sidebar -->
					<ul class="list-unstyled components">
						<li>
							<a href="home.php" aria-expanded="false">
								<i class="glyphicon glyphicon-home"></i>
								Home
							</a>
						</li>
			';

			// looping all the database tables
			while($table = mysqli_fetch_array($loop)){
				$attach_password = 0;
				$head = "";
				$body = "";

				$insert = "";
				$values = "";
				$update = "";


				// having a name for the table in two cases, all small caps and capitalised
				$capital = ucfirst($table[0]);
				$small = strtolower($table[0]);
				$table_name = $table[0];

				// create pages for the tbales with primary key(s)
				$pkquery = mysqli_query($link, "SELECT column_name AS primary_key
					FROM information_schema.KEY_COLUMN_USAGE
					WHERE TABLE_NAME = '".$table_name."' AND 
						TABLE_SCHEMA = '".$database."' AND 
					  CONSTRAINT_NAME = 'PRIMARY'");
				$pkrow = mysqli_fetch_assoc($pkquery);
				$pkname = $pkrow['primary_key'];

				if (!$pkname) {
					$loop_no_key_tables[] = $table_name;
					continue;
				}

				// collecting the contents for the table main page tableName.php
				$show = "<?php
				include \"includes/header.php\";
				?>

				<div>
					<a class=\"btn btn-primary\" href=\"edit-".$table_name.".php?act=add\" style=\"float: right; margin-top=20px;\"> <i class=\"glyphicon glyphicon-plus-sign\"></i> Add New " . $capital . "</a>

					<h1>" . $capital . "</h1>
					<?php if (isset($"."_SESSION['success'])): 
						if ($"."_SESSION['success']):	
							$"."alertclass = 'alert-success';
							$"."alertcaption = 'Success! ';
						elseif (!$"."_SESSION['success']): 
							$"."alertclass = 'alert-danger';
							$"."alertcaption = 'Oops! '; ?>
					<?php endif; ?>
					<div class=\"alert <?php echo $"."alertclass; ?> alert-dismissible\" role=\"alert\">
					  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
					  <strong><?php echo $"."alertcaption; ?> </strong> <?php echo $"."_SESSION['message']; ?>
					</div>
					<?php unset($"."_SESSION['success']);
						unset($"."_SESSION['message']);
					endif; ?>
				</div>

				<p>This table includes <?php echo counting(\"".$table_name."\", \"".$pkname."\");?> ".$table_name.".</p>

				<table id=\"sorted\" class=\"table table-striped table-bordered\">
				<thead>
				<tr>
				";

				// collecting data for the edit page
				$edit = "<?php
				include \"includes/header.php\";

				$"."act = $"."_GET['act'];
				if($"."act == \"edit\"){
					$".$pkname." = $"."_GET['".$pkname."'];
					$".$table_name." = getById(\"".$table_name."\", $".$pkname.", '".$pkname."');
				}
				?>

				<form method=\"post\" action=\"save.php\" enctype='multipart/form-data'>
					<fieldset>
						<legend class=\"hidden-first\">Add New ".$capital."</legend>
						<input name=\"cat\" type=\"hidden\" value=\"".$table_name."\">
						<input name=\"".$pkname."\" type=\"hidden\" value=\"<?=$".$pkname."?>\">
						<input name=\"act\" type=\"hidden\" value=\"<?=$"."act?>\">
				";

				// continue the save page
				$save .= "
				if($"."cat == \"".$table_name."\" || $"."cat_get == \"".$table_name."\"){
					";

				// continue the home page
				$index .= "
				<tr>
					<td><a href=\"" . $table_name . ".php\">" . $capital . "</a></td>
					<td><?=counting(\"" . $table_name . "\", \"".$pkname."\")?></td>
				</tr>
				";

				// continue the sidebar in header
				$icon = random_glyphicon();
				$header .= "<li><a href=\"" . $table_name . ".php\"> <i class=\"glyphicon glyphicon-".$icon."\"></i>" . $capital . " <span class=\"pull-right\"><?=counting(\"" . $table_name . "\", \"".$pkname."\")?></span></a></li>\n";


				// finding all the columns in a table
				$column_desc = mysqli_query($link, "DESCRIBE " . $table[0])
					or die ('cannot select table fields');

				// looping in the columns
				while ($col = mysqli_fetch_assoc($column_desc)){
					// data for the table in the show page tableName.php 
					$head .= "		\t<th>" . ucfirst(str_replace("_", " ", $col['Field'])) . "</th>\n";
					$body .= "	\t<td><?php echo $".$table_name."s['" . $col['Field'] . "']?></td>\n";

					if($col['Key'] != "PRI"){
						$col_attributes = array(
							'data_type' => null,
							'col_min' => 0,
							'col_max' => 0,
							'length' => null,
							'nullable' => '',
							'label_nullable' => '',
							'save_nullable' => '',
							'pattern' => '',
							'options' => null,
							'multiple' => ''
						);
						// $col_attributes['nullable'] = ($col['Null'] == 'NO') ? 'required=""' : '';
						if ($col['Null'] == 'NO') {
							$col_attributes['save_nullable'] = "$" . $col['Field'] . " = mysqli_real_escape_string($" . "link, $"."_POST[\"" . $col['Field'] . "\"]);\n";
							$col_attributes['nullable'] = 'required=""';
							$col_attributes['label_nullable'] = ' *';
						} else {
							$col_attributes['save_nullable'] = " if (empty($"."_POST[\"" . $col['Field'] . "\"])) {
									$" . $col['Field'] . " = 'NULL';\n
								} else {
									$" . $col['Field'] . " = mysqli_real_escape_string($" . "link, $"."_POST[\"" . $col['Field'] . "\"]);\n
								}
							";
						}
						if($col['Type'] == "text"){

							// continue the edit page with a text area for a type text column
							$edit .= "
							<label>" . ucfirst(str_replace("_", " ", $col['Field'])) . $col_attributes['label_nullable'] . "</label>
							<textarea class=\"ckeditor form-control\" name=\"" . $col['Field'] . "\" " . $col_attributes['nullable'] . "><?=$".$table_name."['" . $col['Field'] . "']?></textarea><br>
							";
						} else {
							// get the data type
							$matches = null;

							if (preg_match('/((?:tiny|small|medium|big)?int(?:eger)?)(?:\((\d+)\))?/', $col['Type'], $matches)) {
								// match: INTEGER values
								$col_attributes['data_type'] = 'number';
								// set data length
								$exp = (int)$matches[2];
								if ($exp) {
									$col_attributes['col_min'] = -(2**($exp - 1));
									$col_attributes['col_max'] = (2**($exp - 1)) - 1;
								} else {
									switch ($matches[1]) {
										case 'tinyint':
										case 'TINYINT':
											$col_attributes['col_min'] = -128;
											$col_attributes['col_max'] = 127;
											break;
										case 'smallint':
										case 'SMALLINT':
											$col_attributes['col_min'] = -32768;
											$col_attributes['col_max'] = 32767;
											break;
										case 'mediumint':
										case 'MEDIUMINT':
											$col_attributes['col_min'] = -8388608;
											$col_attributes['col_max'] = 8388607;
											break;
										case 'integer':
										case 'INTEGER':
											$col_attributes['col_min'] = -2147483648;
											$col_attributes['col_max'] = 2147483647;
											break;
										case 'bigint':
										case 'BIGINT':
											$col_attributes['col_min'] = -(2**63);
											$col_attributes['col_max'] = (2**63) - 1;
											break;
										default:
											$col_attributes['col_min'] = 0;
											$col_attributes['col_max'] = 0;
											break;
									}
								}
								$col_attributes['length'] = ' min="' . $col_attributes['col_min'] . '" max="' . $col_attributes['col_max'] . '" step="1" ';
							} elseif (preg_match('/(decimal|numeric)(?:\((\d+)?,(\d+)?\))?/', $col['Type'], $matches)) {
								// match: floating point values
								$col_attributes['data_type'] = 'number';
								$num_m = ((int)$matches[2]) ? (int)$matches[2] : 10;
								$num_d = ((int)$matches[3]) ? (int)$matches[3] : 0;
								$num_step = 1/(10**$num_d);
								$col_attributes['col_min'] = -(10**($num_m - $num_d)) + 1;
								$col_attributes['col_max'] = -$col_attributes['col_min'];
								$col_attributes['length'] = ' min="' . $col_attributes['col_min'] . '" max="' . $col_attributes['col_max'] . '" step="' . $num_step . '"';
							} elseif (preg_match('/char(?:\((\d+)\))?/', $col['Type'], $matches)) {
								// match: CHAR values
								$col_attributes['data_type'] = 'text';
								$col_length = ((int)$matches[1]) ? ((int)$matches[1]) : 64;
								$col_attributes['length'] = ' minlength="0" maxlength="' . $col_length . '" ';
							} elseif (preg_match('/varchar(?:\((\d+)\))?/', $col['Type'], $matches)) {
								// match: VARCHAR values
								$col_attributes['data_type'] = 'text';
								$col_length = ((int)$matches[1]) ? ((int)$matches[1]) : 65535;
								$col_attributes['length'] = ' minlength="0" maxlength="' . $col_length . '" ';
							} elseif (preg_match('/bit(?:\((\d+)\))?/', $col['Type'], $matches)) {
								// match: BIT values
								$col_attributes['data_type'] = 'text';
								$col_length = ((int)$matches[1]) ? ((int)$matches[1]) : 64;
								$col_attributes['length'] = ' minlength="0" maxlength="' . $col_length . '" ';
							} elseif (preg_match('/((?:tiny|medium|big)?text)(?:\((\d+)\))?/', $col['Type'], $matches)) {
								// match: TEXT values
								$col_attributes['data_type'] = 'text';
								// set data length
								$col_attributes['col_min'] = 0;
								if ((int)$matches[2]) {
									$col_attributes['col_max'] = (int)$matches[2];
								} else {
									switch ($matches[1]) {
										case 'tinytext':
										case 'TINYTEXT':
											$col_attributes['col_max'] = 2**8;
											break;
										case 'text':
										case 'TEXT':
											$col_attributes['col_max'] = 2**16;
											break;
										case 'mediumtext':
										case 'MEDIUMTEXT':
											$col_attributes['col_max'] = 2**24;
											break;
										case 'longtext':
										case 'LONGTEXT':
											$col_attributes['col_max'] = 2**32;
											break;
										default:
											$col_attributes['col_max'] = 0;
											break;
									}
								}
								$col_attributes['length'] = ' minlength="' . $col_attributes['col_min'] . '" maxlength="' . $col_attributes['col_max'] . '" ';
							}elseif (preg_match('/(date|datetime|timestamp|time|year)(?:\((\d+)\))?$/', $col['Type'], $matches)) {
								// match: DATE and TIME values
								switch ($matches[1]) {
									case 'date':
									case 'DATE':
										$col_attributes['data_type'] = 'date';
										break;
									case 'datetime':
									case 'DATETIME':
										$col_attributes['data_type'] = 'datetime-local';
										break;
									case 'timestamp':
									case 'TIMESTAMP':
										$col_attributes['data_type'] = 'datetime-local';
										break;
									case 'time':
									case 'TIME':
										$col_attributes['data_type'] = 'time';
										break;
									case 'year':
									case 'YEAR':
										$col_attributes['data_type'] = 'number';
										$col_attributes['col_min'] = 1901;
										$col_attributes['col_max'] = 2155;
										$col_attributes['length'] = ' min="' . $col_attributes['col_min'] . '" max="' . $col_attributes['col_max'] . '" ';
										break;
									default:
										break;
								} 
								$col_attributes['col_length'] = '';
							} elseif (preg_match('/(enum|set)(?:\((.+)\))$/', $col['Type'], $matches)) {
								$col_attributes['data_type'] = $matches['1'];
								$data_options = explode(',', $matches['2']);
								$col_attributes['options'] = array();
								foreach ($data_options as $option) {
									$col_attributes['options'][] = trim($option, " '");
								}
								if ($matches['1'] == 'set') {
									$col_attributes['multiple'] = ' multiple="" ';
									if ($col['Null'] != 'NO') {
										$col_attributes['save_nullable'] = " if (empty($"."_POST[\"" . $col['Field'] . "\"])) {
												$" . $col['Field'] . " = 'NULL';\n
											} else {
												$" . $col['Field'] . " = \"('\" . mysqli_real_escape_string($" . "link, implode(\",\", $"."_POST[\"" . $col['Field'] . "\"])) . \"')\";\n
											}
										";
									}
								} else {
									$col_attributes['multiple'] = "";
								};
							} else {
								$col_attributes['data_type'] = 'text';
							}
							// continue the edit page with an input field or a select if data type is enum/set
							if (!$col_attributes['options']) {
								$edit .= "
								<label>" . ucfirst(str_replace("_", " ", $col['Field'])) . $col_attributes['label_nullable'] . "</label>
								<input class=\"form-control\" type=\"" . $col_attributes['data_type'] . "\" name=\"" . $col['Field'] . "\" value=\"<?=$".$table_name."['" . $col['Field'] . "']?>\" " . $col_attributes['nullable'] . " " . $col_attributes['length'] . " " . $col_attributes['pattern'] . "/><br>
								";
							} else {
								$edit .= "
								<label>" . ucfirst(str_replace("_", " ", $col['Field'])) . $col_attributes['label_nullable'] . "</label>
								<select class=\"form-control\" name=\"" . $col['Field'] . (strlen($col_attributes['multiple']) > 1 ? "[]" : "") . "\" value=\"<?=$".$table_name."['" . $col['Field'] . "']?>\" " . $col_attributes['nullable'] . " " . $col_attributes['multiple'] . " >
								<?php $" . $table_name . "['" . $col['Field'] . "'] = explode(',', $" . $table_name . "['" . $col['Field'] . "']); ?>
								";
								// create the 'NULL' option if the column can be NULL
								if ($col['Null'] == 'YES') {
									$edit .= "<option value=\"" . ($col_attributes['data_type'] == 'set' ? "" : "NULL") . "\"></option>";
								}
								foreach ($col_attributes['options'] as $option) {
									$edit .= "<option value=\"" . $option . "\"" . " <?= (in_array('" . $option . "', $".$table_name."['" . $col['Field'] . "']) ? 'selected' : '' )?> " . ">" . $option . "</option>
									";
								}
								$edit .= "</select><br>";
							}
						}
					}

					// check if the column is not the ID to create the corresponding save and insert data
					if ($col['Field'] != $pkname){

						$save .= $col_attributes['save_nullable'];

						$insert .= " `" . $col['Field'] . "` ,";

						if($col['Field'] == "password"){
							$attach_password = 1;
							$values .= " '\".md5($" . $col['Field'] . ").\"',";

						}else{
							if ($col['Null'] == 'NO') {
								if ($col_attributes['data_type'] == 'set') {
									$values .= " \".$" . $col['Field'] . ".\" ,";
									$update .= " `" . $col['Field'] . "` =  \".$" . $col['Field'] . ".\" ,";
								} else {
									$values .= " '\".$" . $col['Field'] . ".\"' ,";
									$update .= " `" . $col['Field'] . "` =  '\".$" . $col['Field'] . ".\"' ,";
								}
							} else {
								if ($col_attributes['data_type'] == 'set') {
									$values .= " \".($" . $col['Field'] . " == 'NULL' ? '(\"\")' : \"$" . $col['Field'] . "\") . \",";
									$update .= " `" . $col['Field'] . "` =  \".($" . $col['Field'] . " == 'NULL' ? '(\"\")' : \"$" . $col['Field'] . "\") . \",";
								} else {
									$values .= " \".($" . $col['Field'] . " == 'NULL' ? 'NULL' : \"'$" . $col['Field'] . "'\") . \",";
									$update .= " `" . $col['Field'] . "` =  \".($" . $col['Field'] . " == 'NULL' ? 'NULL' : \"'$" . $col['Field'] . "'\") . \",";
								}
							}
						}
					}

				} // end row loop

				// insert the primary key selector
				$save .= "\n";
				$save .= "$"."pkname = $"."_POST['".$pkname."']; \n";
				$save .= "$"."pkname_get = $"."_GET['".$pkname."']; \n\n";
				$save .= "\n$"."ret;\n\n";

				// continue show page top part
				$head .= "
				<th class=\"not\">Edit</th>
				<th class=\"not\">Delete</th>
				</tr>
				</thead>";

				// show page central part
				$mid = "
				<?php
				$".$table_name." = getAll(\"".$table_name."\");
				if($".$table_name.") foreach ($".$table_name." as $".$table_name."s):
					?>
					<tr>";


				// build the whole page
				$show .= $head."\n";
				$show .= $mid."\n";
				$show .= $body."\n";
				$show .= "
						<td><a href=\"edit-".$table_name.".php?act=edit&".$pkname."=<?php echo $".$table_name."s['".$pkname."']?>\"><i class=\"glyphicon glyphicon-edit\"></i></a></td>
						<td><a href=\"save.php?act=delete&".$pkname."=<?php echo $".$table_name."s['".$pkname."']?>&cat=".$table_name."\" onclick=\"return navConfirm(this.href);\"><i class=\"glyphicon glyphicon-trash\"></i></a></td>
						</tr>
					<?php endforeach; ?>
					</table>
					<?php include \"includes/footer.php\";?>
				";


				$edit .= "<br>
					<input type=\"submit\" value=\" Save \" class=\"btn btn-success\">
					</form>
					<?php include \"includes/footer.php\";?>
				";

				$save .= "

				if($"."act == \"add\"){
					$"."ret = mysqli_query(\$link, \"INSERT INTO `".$table_name."` ( ".removeLastChar($insert).") VALUES (".removeLastChar($values).") \");
				}elseif ($"."act == \"edit\"){
					$"."ret = mysqli_query(\$link, \"UPDATE `".$table_name."` SET ".removeLastChar($update)." WHERE `".$pkname."` = '\".$"."pkname.\"' \"); ";

				if($attach_password == 1){
					$save .= "
					if($"."_POST[\"password\"] && $"."_POST[\"password\"] != \"\"){
						$"."ret = mysqli_query(\$link, \"UPDATE `".$table_name."` SET  `password` =  '\".md5($"."password).\"' WHERE `".$pkname."` = '\".$"."pkname_get.\"' \");
					}
					";
				}

				$save .= "	
					}elseif ($"."act_get == \"delete\"){
						$"."ret = mysqli_query(\$link, \"DELETE FROM `".$table_name."` WHERE ".$pkname." = '\".$"."pkname_get.\"' \");
					}
					header(\"location:\".\"".$table_name.".php\");
				}
				";

				// creating the show page tableName.php
				createFile($database, $table_name, $show);
				$message .= "<li>Created page: ".$table_name.".php</li>";

				// creating the edit page edit-tableName.php
				createFile($database, "edit-".$table_name, $edit);
				$message .= "<li>Created page: edit-".$table_name.".php</li>";

				// empty all variables
				$head = "";
				$body = "";

				$insert = "";
				$values = "";
				$update = "";

			} //end table loop

			$save .= "
				if (!$"."ret) {
					$"."_SESSION['message'] = mysqli_error($"."link);
					$"."_SESSION['success'] = FALSE;
				} else {
					$"."_SESSION['message'] = 'Operation executed successfully!';
					$"."_SESSION['success'] = TRUE;
				}";
			$save .= "?>";

			$footer ='
					</div>
				</div>


				<!-- jQuery Version 1.11.1 -->
				<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>

				<!-- Bootstrap Core JavaScript -->
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

				<script type="text/javascript" src="//cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>
				<script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
				<script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>

				<script type="text/javascript">
					 $(document).ready(function () {

					 	$("#sidebarCollapse, #sidebarExtend").on("click", function () {
							$("#sidebar").toggleClass("active");
						});

						$("#sorted").DataTable( {
							"bStateSave": true,
							"sPaginationType": "full_numbers"
						});
					 });
				</script>

				<script type="text/javascript">
				function navConfirm(loc) {
					if (confirm("Are you sure?")) {
						window.location.href = loc;
					}
					return false;
				}
				</script>

				
			</body>
			</html>';

			$index .= "</table>
			<?php include \"includes/footer.php\";?>
			";

			$header .= "<li><a href=\"logout.php\"><i class=\"glyphicon glyphicon-log-out\"></i> Logout</a></li>
				</ul>
				
				<div class=\"visit\">
					<p class=\"text-center\">Created using MAGE &hearts;</p>
					<a href=\"https://github.com/tej-kweku/php-mysql-admin-panel-generator\" target=\"_blank\" >Visit Project</a>
				</div>
			</nav><!-- /end sidebar -->

			<!-- Page Content Holder -->
			<div id=\"content\">";

createFile($database, "includes/connect", $connection);
$message .= "<li>Created connect.php for database connection info.</li>";

createFile($database, "save", $save);
$message .= "<li>Created save.php for create, update, and delete operations on the database.</li>";

createFile($database, "includes/footer", $footer);
$message .= "<li>Created footer.php to hold pages footer.</li>";

createFile($database, "home", $index);
$message .= "<li>Created home.php to have tables at the start page.</li>";

createFile($database, "includes/header", $header);
$message .= "<li>Created header.php to hold pages header.</li>";

$library = "library/";
$path = "generated/".$database.date("Y-m-d_H-i");

copy($library."index.php", $path."/index.php");
$message .= "<li>Created index.php to have login page.</li>";

copy($library."login.php", $path."/login.php");
$message .= "<li>Created login.php to control login.</li>";

copy($library."logout.php", $path."/logout.php");
$message .= "<li>Created logout.php to control login.</li>";

copy($library."data.php", $path."/includes/data.php");
$message .= "<li>Created data.php to have all functions ready.</li>";

copy($library."style.css", $path."/includes/style.css");
$message .= "<li>Created style.css for styling</li></ul>";

// check if not all tables had primary keys and report those whose pages were not created
if (!empty($loop_no_key_tables)) {
	$message .= "Unbale to create CRUD functionality for the following tables due to absence primary keys: <ul>";
	foreach ($loop_no_key_tables as $tbl) {
		$message .= "<li>Table name: ".$tbl."</li>";
	}

	$message .= "</ul>";
}

echo json_encode(array('status' => 'finished','message'=> '<h1>Finished!</h1><h3>Username: admin<br> Password: admin<br><br><a href="'.$path.'" target="_blank">Visit the Admin Panel <i class="glyphicon glyphicon-new-window"></i></a></h3><br><br>'.$message));

	}


} else {

	echo json_encode(array('status' => 'error','message'=> 'Unknown error occured'));
}
?>