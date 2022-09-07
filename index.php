<?php
include("db.php");
include("Classes/PHPExcel/IOFactory.php");
if(isset($_POST['submit'])) {
if(isset($_FILES['uploadFile']['name']) && $_FILES['uploadFile']['name'] != "") {
$allowedExtensions = array("xls","xlsx");
$ext = pathinfo($_FILES['uploadFile']['name'], PATHINFO_EXTENSION);
if(in_array($ext, $allowedExtensions)) {
$file_size = $_FILES['uploadFile']['size'] / 1024;
if($file_size < 500) {
$file = "uploads/".$_FILES['uploadFile']['name'];
$isUploaded = copy($_FILES['uploadFile']['tmp_name'], $file);
if($isUploaded) {
try {
$objPHPExcel = PHPExcel_IOFactory::load($file);
} catch (Exception $e) {
die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME). '": ' . $e->getMessage());
}
$sheet = $objPHPExcel->getSheet(0);
$total_rows = $sheet->getHighestRow();
$highest_column = $sheet->getHighestColumn();
$ttrow = $total_rows-1;
for($row =2; $row <= $total_rows; $row++) {
$single_row = $sheet->rangeToArray('A' . $row . ':' . $highest_column . $row, NULL, TRUE, FALSE);
echo "<pre>";print_r($single_row);   echo "<br>";
$id = $single_row[0][0];
$firstname = $single_row[0][1];
$lastname = $single_row[0][2];
$mobile = $single_row[0][3];
$country = $single_row[0][4];
$FirstnameChar = substr("$firstname", 0, 1);

$sql = "select username from user_details";
if ($result = mysqli_query($con, $sql)) {
while ($obj = mysqli_fetch_object($result)) {
$dbuser = $obj->username;
}
}

if ($dbuser == $lastname){
$uname = $lastname.trim($FirstnameChar);
} else {
$uname = $lastname;
}
$query = "INSERT INTO user_details (firstname,lastname,username,mobile,country) VALUES('$firstname','$lastname','$uname','$mobile','$country')";
$result = mysqli_query($con,$query);
}

if($result) {
echo $msg="Database table updated!";
} else {
echo $msg="Can\'t update database table! try again.";
}
} else {
echo $msg= "File not uploaded!";
}
} else {
echo $msg="Maximum file size should not cross 50 KB on size!";
}
} else {
echo $msg= "This type of file not allowed!";
}
} else {
echo $msg= "Select an excel file first! ";
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>work --excel save</title>
</head>
<body>
<div class="wrapperDiv" style="color:blue;text-align:center" >
<form action="" method="post" enctype="multipart/form-data">
<table border="2" align="center" >
Upload excel file :
<input type="file" name="uploadFile" value="" />
<input type="submit" name="submit" value="Save" />
</table>
</form>
</div>
</body>