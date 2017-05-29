<?php
namespace Mouf\Doctrine\DBAL\Controllers;

use Doctrine\DBAL\Connection;

use Mouf\Actions\InstallUtils;
use Mouf\Console\ConsoleUtils;
use Mouf\MoufManager;
use Mouf\Mvc\Splash\Controllers\Controller;

/**
 * The controller managing the install process.
 * It will query the database details.
 *
 * @Component
 */
class DBALConnectionInstallController extends Controller  {
	
	public $selfedit;
	
	/**
	 * The active MoufManager to be edited/viewed
	 *
	 * @var MoufManager
	 */
	public $moufManager;
	
	/**
	 * The template used by the main page for mouf.
	 *
	 * @Property
	 * @Compulsory
	 * @var TemplateInterface
	 */
	public $template;
	
	/**
	 * The content block the template will be writting into.
	 *
	 * @Property
	 * @Compulsory
	 * @var HtmlBlock
	 */
	public $contentBlock;
	
	/**
	 * List of supported drivers and their mappings to the driver classes.
	 *
	 * To add your own driver use the 'driverClass' parameter to
	 * {@link DriverManager::getConnection()}.
	 *
	 * @var array
	 */
	protected $driverMap = array(
		'pdo_mysql'          => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
		'pdo_sqlite'         => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
		'pdo_pgsql'          => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
		'pdo_oci'            => 'Doctrine\DBAL\Driver\PDOOracle\Driver',
		'oci8'               => 'Doctrine\DBAL\Driver\OCI8\Driver',
		'ibm_db2'            => 'Doctrine\DBAL\Driver\IBMDB2\DB2Driver',
		'pdo_sqlsrv'         => 'Doctrine\DBAL\Driver\PDOSqlsrv\Driver',
		'mysqli'             => 'Doctrine\DBAL\Driver\Mysqli\Driver',
		'drizzle_pdo_mysql'  => 'Doctrine\DBAL\Driver\DrizzlePDOMySql\Driver',
		'sqlanywhere'        => 'Doctrine\DBAL\Driver\SQLAnywhere\Driver',
		'sqlsrv'             => 'Doctrine\DBAL\Driver\SQLSrv\Driver',
	);
	
	/**
	 * Displays the first install screen.
	 * 
	 * @Action
	 * @Logged
	 * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only) 
	 */
	public function defaultAction($selfedit = "false") {
		$this->selfedit = $selfedit;
		
		if ($selfedit == "true") {
			$this->moufManager = MoufManager::getMoufManager();
		} else {
			$this->moufManager = MoufManager::getMoufManagerHiddenInstance();
		}
				
		$this->contentBlock->addFile(dirname(__FILE__)."/../../../../views/installStep1.php", $this);
		$this->template->toHtml();
	}
	
	/**
	 * Skips the install process.
	 * 
	 * @Action
	 * @Logged
	 * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only)
	 */
	public function skip($selfedit = "false") {
		InstallUtils::continueInstall($selfedit == "true");
	}

	protected $host;
	protected $port;
	protected $dbname;
	protected $user;
	protected $password;
	
	/**
	 * Displays the second install screen.
	 * 
	 * @Action
	 * @Logged
	 * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only) 
	 */
	public function configure($selfedit = "false", $instanceName = null) {
		$this->selfedit = $selfedit;
		
		if ($selfedit == "true") {
			$this->moufManager = MoufManager::getMoufManager();
		} else {
			$this->moufManager = MoufManager::getMoufManagerHiddenInstance();
		}
		
		$configManager = $this->moufManager->getConfigManager();
		$constants = $configManager->getMergedConstants();

		$this->host = isset($constants['DB_HOST']) ? $constants['DB_HOST']['value'] : "localhost";
		$this->port = isset($constants['DB_PORT']) ? $constants['DB_PORT']['value'] : "";
		$this->dbname = isset($constants['DB_NAME']) ? $constants['DB_NAME']['value'] : "";
		$this->user = isset($constants['DB_USERNAME']) ? $constants['DB_USERNAME']['value'] : "root";
		$this->password = isset($constants['DB_PASSWORD']) ? $constants['DB_PASSWORD']['value'] : "";
		
		$this->contentBlock->addFile(dirname(__FILE__)."/../../../../views/installStep2.php", $this);
		$this->template->toHtml();
	}
	
	
	
	/**
	 * Action to create the database connection.
	 * 
	 * @Action
	 * @Logged
	 * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only)
	 */
	public function install($host, $port, $dbname, $user, $password, $driver, $selfedit = "false") {
		if ($selfedit == "true") {
			$this->moufManager = MoufManager::getMoufManager();
		} else {
			$this->moufManager = MoufManager::getMoufManagerHiddenInstance();
		}
		
		$moufManager = $this->moufManager;
		$configManager = $moufManager->getConfigManager();
		$constants = $configManager->getMergedConstants();
		
		if (!isset($constants['DB_HOST'])) {
			$configManager->registerConstant("DB_HOST", "string", "localhost", "The database host (the IP address or URL of the database server).");
		}
		
		if (!isset($constants['DB_PORT'])) {
			$configManager->registerConstant("DB_PORT", "int", "", "The database port (the port of the database server, keep empty to use default port).");
		}
		
		if (!isset($constants['DB_NAME'])) {
			$configManager->registerConstant("DB_NAME", "string", "", "The name of your database.");
		}
		
		if (!isset($constants['DB_USERNAME'])) {
			$configManager->registerConstant("DB_USERNAME", "string", "", "The username to access the database.");
		}
		
		if (!isset($constants['DB_PASSWORD'])) {
			$configManager->registerConstant("DB_PASSWORD", "string", "", "The password to access the database.");
		}
		
		if (!$moufManager->instanceExists("dbalConnection")){
			$driverInstance = $moufManager->createInstance($driver);
			$eventManager = $moufManager->createInstance('Doctrine\\Common\\EventManager');
			
			$connectionInstance = $moufManager->createInstance("Doctrine\\DBAL\\Connection");
			$connectionInstance->getProperty("params")->setOrigin("php")->setValue('return array(
			    "host" => DB_HOST,
			    "user" => DB_USERNAME,
			    "password" => DB_PASSWORD,
			    "port" => DB_PORT,
			    "dbname" => DB_NAME,
			    "charset" => "utf8",
			    "driverOptions" => array(
			        1002 =>"SET NAMES utf8"
			    )
			);');
			$connectionInstance->getProperty("driver")->setValue($driverInstance);
			$connectionInstance->getProperty("eventManager")->setValue($eventManager);
			$connectionInstance->setName("dbalConnection");
		} else {
			$connectionInstance = $moufManager->getInstanceDescriptor('dbalConnection');
		}

		$consoleUtils = new ConsoleUtils($moufManager);

		// Let's configure the console
		if (!$moufManager->instanceExists("dbalConnectionHelper")){
			$dbalConnectionHelper = InstallUtils::getOrCreateInstance('dbalConnectionHelper', 'Doctrine\\DBAL\\Tools\\Console\\Helper\\ConnectionHelper', $moufManager);
			$dbalConnectionHelper->getConstructorArgumentProperty("connection")->setValue($connectionInstance);
			$consoleUtils->registerHelper($dbalConnectionHelper, 'db');
		}

		$dbalRunSqlCommand = InstallUtils::getOrCreateInstance('dbalRunSqlCommand', 'Doctrine\\DBAL\\Tools\\Console\\Command\\RunSqlCommand', $moufManager);
		$dbalImportCommand = InstallUtils::getOrCreateInstance('dbalImportCommand', 'Doctrine\\DBAL\\Tools\\Console\\Command\\ImportCommand', $moufManager);
		$dbalReservedWordsCommand = InstallUtils::getOrCreateInstance('dbalReservedWordsCommand', 'Doctrine\\DBAL\\Tools\\Console\\Command\\ReservedWordsCommand', $moufManager);

		$consoleUtils->registerCommand($dbalRunSqlCommand);
		$consoleUtils->registerCommand($dbalImportCommand);
		$consoleUtils->registerCommand($dbalReservedWordsCommand);


		$configPhpConstants = $configManager->getDefinedConstants();
		$configPhpConstants['DB_HOST'] = $host;
		$configPhpConstants['DB_PORT'] = $port;
		$configPhpConstants['DB_USERNAME'] = $user;
		$configPhpConstants['DB_PASSWORD'] = $password;
		$configPhpConstants['DB_NAME'] = $dbname;
		$configManager->setDefinedConstants($configPhpConstants);
		
		$moufManager->rewriteMouf();		
		
		InstallUtils::continueInstall($selfedit == "true");
	}
	
	/**
	 * Displays the list of all databases installed in JSON format.
	 * If the connection parameters are incorrect, returns an empty JSON array 
	 * 
	 * @Action
	 * @param string $host
	 * @param string $port
	 * @param string $user
	 * @param string $password
	 */
	public function getDbList($host, $port, $user, $password, $driver) {
		error_reporting(E_ALL);
		ini_set('display_errors',1);

		if (empty($driver)) {
			echo "[]";
			return;
		}
		$driverClass = new $driver();
		$params = array(
			"host" => $host,
			"user" => $user,
			"password" => $password
		);
		$conn = new Connection($params, $driverClass);

		try {
			$dbList = $conn->getSchemaManager()->listDatabases();
		} catch (\Exception $e) {
			// If bad parameters are passed, let's just return an empty list.
			echo "[]";
			return;
		}
		// Display the list.
		echo json_encode($dbList);
	}
	
	
}