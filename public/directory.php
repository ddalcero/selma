<?php

$conn = new PDO('mysql:host=10.247.2.1;port=3306;dbname=asterisk', 'root', 'portal', 
		array( PDO::ATTR_PERSISTENT => false));

$sql = 'SELECT extension,name FROM users ORDER BY name';

header ("content-type: text/xml");
echo "<CiscoIPPhoneDirectory>\n";
echo "<Title>PBX Directory</Title>\n";
echo "<Prompt>Select a User</Prompt>\n";

foreach ($conn->query($sql) as $row) {
	if ($row['name']!=$row['extension']) {
		echo "<DirectoryEntry>\n";
		echo "<Name>" . $row['name'] . "</Name>\n";
		echo "<Telephone>" . $row['extension'] . "</Telephone>\n";
		echo "</DirectoryEntry>\n";
	}
}
echo "</CiscoIPPhoneDirectory>\n";
