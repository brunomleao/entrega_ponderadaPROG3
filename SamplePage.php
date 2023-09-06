<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Sample page</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the EMPLOYEES table exists. */
  VerifyEmployeesTable($connection, DB_DATABASE);

  /* Ensure that the EXTRA_INFOS table exists. */
  VerifyExtraInfosTable($connection, DB_DATABASE);

  /* If input fields are populated, add a row to the EMPLOYEES table. */
  $employee_name = htmlentities($_POST['NAME']);
  $employee_address = htmlentities($_POST['ADDRESS']);

  if (strlen($employee_name) || strlen($employee_address)) {
    AddEmployee($connection, $employee_name, $employee_address);
  }

  /* If input fields for EXTRA_INFOS are populated, add a row to the EXTRA_INFOS table. */
  $birthday = htmlentities($_POST['BIRTHDAY']);
  $salary = htmlentities($_POST['SALARY']);
  if (!is_numeric($salary)) {
    echo "Entrada de salário inválida. Por favor, insira um valor numérico válido.";
  }
  $comments = htmlentities($_POST['COMMENTS']);

  if (strlen($birthday) || strlen($salary) || strlen($comments)) {
    // Convert the date to 'YYYY-MM-DD' format
    $mysqlFormattedBirthday = date('Y-m-d', strtotime($birthday));
    AddInfo($connection, $mysqlFormattedBirthday, $salary, $comments);
  }
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NAME</td>
      <td>ADDRESS</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="ADDRESS" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" value="Add Data" />
      </td>
    </tr>
  </table>
</form>

<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>BIRTHDAY</td>
      <td>SALARY</td>
      <td>COMMENTS</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="BIRTHDAY" maxlength="45" size="30" placeholder="YYYY-MM-DD" />
      </td>
      <td>
        <input type="text" name="SALARY" maxlength="90" size="60" />
      </td>
      <td>
        <input type="text" name="COMMENTS" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" value="Add Data" />
      </td>
    </tr>
  </table>
</form>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NAME</td>
    <td>ADDRESS</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>";
  echo "</tr>";
}
?>

</table>

<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>BIRTHDAY</td>
    <td>SALARY</td>
    <td>COMMENTS</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM EXTRA_INFOS");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>";
  echo "</tr>";
}
?>

<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add an employee to the table. */
function AddEmployee($connection, $name, $address) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);

   $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName) {
  if(!TableExists("EMPLOYEES", $connection, $dbName))
  {
     $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

function VerifyExtraInfosTable($connection, $dbName) {
  if(!TableExists("EXTRA_INFOS", $connection, $dbName))
  {
     $query = "CREATE TABLE EXTRA_INFOS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         BIRTHDAY DATE,
         SALARY DECIMAL(10,2),
         COMMENTS TEXT
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

function AddInfo($connection, $birthday, $salary, $comments) {
  $b = mysqli_real_escape_string($connection, $birthday);
  $s = mysqli_real_escape_string($connection, $salary);
  $c = mysqli_real_escape_string($connection, $comments);

  $query = "INSERT INTO EXTRA_INFOS (BIRTHDAY, SALARY, COMMENTS) VALUES ('$b', '$s', '$c');";

  if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
