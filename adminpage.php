<?php
	// adminpage file
	require 'database.php';
	
	// test database connection and check if table exists
	$result = $mydb->get_results("SELECT COUNT(*) as num_of_tables FROM information_schema.tables WHERE table_schema='".$db_name."' AND table_name='".$db_table."';");
	$num_of_tables = (int)$result[0]->num_of_tables;
	if ($num_of_tables<=0) {
		// We need to create the table
		$mydb->query( 
			"
				CREATE TABLE ".$db_table." (
				  id INT NOT NULL AUTO_INCREMENT,
				  first_name  TEXT NOT NULL,
				  last_name  TEXT NOT NULL,
				  email  TEXT NOT NULL,
				  address  TEXT NOT NULL,
				  mobile  TEXT NOT NULL,
				  PRIMARY KEY (id)
				);
			"
		);		
		print "Please note: file 'database.php' must be updated with correct credentials!<br>";
		print "We attempted to create table '".$db_table."' in the database '".$db_name."'. Please refresh the page.<br>";
		print "If you get this message again, please double check credentials in 'database.php'.<br>";
		exit;
	} 
	
	function getVar($var) {
		// Get a variable passed with POST or GET in a safe way
		// Returns NULL if the variable does not exist
		if (isset($_POST[$var])) return stripslashes($_POST[$var]);
		else if (isset($_GET[$var])) return stripslashes($_GET[$var]);
		else return NULL;
	}	
	
	$error_msg = "";
	
	$index = getVar("index");
	$action = getVar("action");
	$id = getVar("id");
	$first_name = getVar("first_name");
	$last_name = getVar("last_name");
	$email = getVar("email");
	$address = getVar("address");	
	$mobile = getVar("mobile");	
	
	if ($id!=NULL) {
		// Do Update or Delete only if it makes sense (it makes sense if id is given)
		if ($action=="Update") {
			if (
				$first_name == NULL || strlen($first_name)==0 ||
				$last_name == NULL || strlen($last_name)==0 ||
				$email == NULL || strlen($email)==0 ||
				$address == NULL || strlen($address)==0 ||
				$mobile == NULL || strlen($mobile)==0
			) {
				$error_msg .= "Please fill all fields. All fields are required...\n<br>";
			} else {				
				$mydb->query( $mydb->prepare( 
					"
						UPDATE ".$db_table."
						SET first_name = %s, 
							last_name = %s,
							email = %s,
							address = %s,
							mobile = %s 
						WHERE id = %d;
					", 
			        array (
						$first_name, 
						$last_name, 
						$email,
						$address,
						$mobile,
						$id
					) 
				));
			}	
		}
		if ($action=="Delete") {
			$mydb->query( $mydb->prepare( 
				"
					DELETE FROM ".$db_table."
					WHERE id = %d
				", 
		        array (
					$id
				) 
			));			
		}
	}
	
	// Insert should work even if negative index is passed. Index is negative if there are no rows
	if ($action=="Insert") {
		if (
			$first_name == NULL || strlen($first_name)==0 ||
			$last_name == NULL || strlen($last_name)==0 ||
			$email == NULL || strlen($email)==0 ||
			$address == NULL || strlen($address)==0 ||
			$mobile == NULL || strlen($mobile)==0
		) {
			$error_msg .= "Please fill all fields. All fields are required...\n<br>";
		} else {
			$mydb->query( $mydb->prepare( 
				"
					INSERT INTO ".$db_table."
					( first_name, last_name, email, address, mobile )
					VALUES ( %s, %s, %s, %s, %s )
				", 
		        array (
					$first_name, 
					$last_name, 
					$email,
					$address,
					$mobile
				) 
			));
		}
	}
	
	$rows = $mydb->get_results("SELECT * from ".$db_table." ORDER BY Id");
	
	if ($action=="Insert" && strlen($error_msg)==0) {
		$index = count($rows)-1;
	}	
	
	if ($action=="Refresh") {
		$index = 0;
	}
	
	if ($action=="Update") {
		// Select row by id which we just updated
		for ($i=0; $i<count($rows); $i++) {
			$res = $rows[$i];
			if ($res->id == $id) {
				$index = $i;
				break;
			}
		}
	}
	
	if ($action=="Search") {
		for ($i=0; $i<count($rows); $i++) {
			$res = $rows[$i];
			if (substr($res->first_name, 0, strlen($first_name)) !== $first_name) {
				array_splice($rows, $i, 1);
				$i--;				
				continue;
			}
			if (substr($res->last_name, 0, strlen($last_name)) !== $last_name) {
				array_splice($rows, $i, 1);
				$i--;
				continue;
			}
			if (substr($res->address, 0, strlen($address)) !== $address) {
				array_splice($rows, $i, 1);
				$i--;
				continue;
			}
			if (substr($res->email, 0, strlen($email)) !== $email) {
				array_splice($rows, $i, 1);
				$i--;
				continue;
			}
			if (substr($res->mobile, 0, strlen($mobile)) !== $mobile) {
				array_splice($rows, $i, 1);
				$i--;
				continue;
			}
		}
		$index = 0;
	}
	
	// Put selected index into allowed range
	if ($index==NULL) $index = 0; // By default first row is selected
	if ($index<0) $index = 0;
	if ($index>=count($rows)) $index = count($rows)-1;
	// It will be -1 if there are 0 rows in the table
?>

<html>
<body>

<h1>Manage personal details</h1>
<form action="#" method="POST">
<?php if (strlen($error_msg)>0) print "<font color='red'><b>Error: $error_msg</b></font>"?>
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td class="nowrap_class">
			First name:<br>
			<input type="text" name="first_name" id="first_name" class="input_field">
		</td>
		<td class="column_separator"></td>
		<td class="nowrap_class">
			Last name:<br>
			<input type="text" name="last_name" id="last_name" class="input_field">
		</td>
		<td class="column_separator"></td>
		<td class="nowrap_class">
			Email:<br>
			<input type="text" name="email" id="email" class="input_field">
		</td>
		<td class="column_separator"></td>
		<td class="nowrap_class">
			Address:<br>
			<input type="text" name="address" id="address" class="input_field">
		</td>		
		<td class="column_separator"></td>
		<td class="nowrap_class">
			Mobile:<br>
			<input type="text" name="mobile" id="mobile" class="input_field">
		</td>
	</tr>
	<tr>
		<td colspan="9">
			<hr class="separator">
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" value="Insert" name="action" class="submitButton">
		</td>
		<td></td>
		<td>
			<input type="submit" value="Update" name="action" class="submitButton">
		</td>
		<td></td>
		<td>
			<input type="submit" value="Delete" name="action" class="submitButton">
		</td>
		<td></td>
		<td>
			<input type="submit" value="Refresh" name="action" class="submitButton">
		</td>
		<td></td>
		<td>
			<input type="submit" value="Search" name="action" class="submitButton">			
		</td>
	</tr>
</table>

<hr class="separator">
	
<?php
	if (count($rows)>0) { // Don't show table if there are no rows
		print "<input type='hidden' id='index' name='index' value='$index'>\n";
?>
<table cellspacing="0" cellpadding="10" border="1" class="data_table">
	<tr>
		<td></td>
		<td><b>Id</b></td>
		<td><b>First name</b></td>
		<td><b>Last name</b></td>
		<td><b>Email</b></td>
		<td><b>Address</b></td>
		<td><b>Mobile</b></td>				
	</tr>
	<?php	
	for ($i=0; $i<count($rows); $i++) {
		print "<tr onclick='selectRow($i); fillTextboxesWithSelectedData($i);' class='dataRow basic' id='row_$i'>\n";
		$res = $rows[$i];
		print "<td><input type='radio' id='radio_$i' name='id' value='".$res->id."'></td>\n";
		print "<td>".$res->id."</td>\n";
		print "<td>".htmlentities($res->first_name)."</td>\n";
		print "<td>".htmlentities($res->last_name)."</td>\n";
		print "<td>".htmlentities($res->email)."</td>\n";
		print "<td>".htmlentities($res->address)."</td>\n";
		print "<td>".htmlentities($res->mobile)."</td>\n";		
		print "</tr>\n";		
	}
	?>
</table>
<?php
	} // End of 'if (count($rows)>0)'
?>
<script type="text/javascript">
	selectRow(<?php print $index; ?>);
	<?php
		if ($action!="Refresh" && $action!="Delete") {
	?>
	first_name = <?php print json_encode($first_name); ?>;
	last_name = <?php print json_encode($last_name); ?>;
	email = <?php print json_encode($email); ?>;
	address = <?php print json_encode($address); ?>;
	mobile = <?php print json_encode($mobile); ?>;
	jQuery("#first_name").val(first_name);
	jQuery("#last_name").val(last_name);
	jQuery("#email").val(email);
	jQuery("#address").val(address);	
	jQuery("#mobile").val(mobile);
	<?php
		} else if ($action=="Delete") {
	?>
	fillTextboxesWithSelectedData(<?php print $index; ?>);
	<?php
		}
	?>
</script>
</form>
</body>
</html>