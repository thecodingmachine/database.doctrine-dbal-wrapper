<?php 
use Mouf\Doctrine\DBAL\Controllers\DBALConnectionInstallController;

/* @var $this DBALConnectionInstallController */
?>

<h1>Setting up your database connection</h1>

<p>You will need a database connection. This installation wizard will create a "dbConnection" instance for a MySQL connection, and will add 5 config parameters:</p>
<ul>
	<li><b>DB_HOST</b>: The database host (the IP address or URL of the database server).</li>
	<li><b>DB_PORT</b>: The database port (the port of the database server, keep empty to use default port).</li>
	<li><b>DB_NAME</b>: The name of your database.</li>
	<li><b>DB_USERNAME</b>: The username to access the database.</li>
	<li><b>DB_PASSWORD</b>: The password to access the database.</li>
</ul>

<form action="configure" class="form-horizontal">
	<div class="control-group">
      <button type="submit" class="btn btn-danger">Configure database connection</button>
    </div>
</form>
<form action="skip">
	<button class="btn">Skip</button>
</form>