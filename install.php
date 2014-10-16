<?
include 'includes.php'           ;

include 'deploy/install_b.class.php';

include 'auth/user.install.php'  ;
include 'money/monia.install.php';
include 'money/wallet/wallet.install.php';
include 'mail/mail.install.php'  ;
include 'sys/logger/logger.install.php';

/**
 * Class for installation logic.
 *
 * @version 0.0.3
 */
class Installator extends InstallB {

	/**
	 * Base install
	 *
	 * @return void
	 */
	public static function install() {

		/** Base install */
		parent::install();

		InstallUser::install();
		InstallMail::install();
		InstallLogger::install();
		InstallMonia::install();
		InstallWallet::install();

		self::createTables();
	}

	/**
	 * Creates tables.
	 * 
	 * @return void
	 */
	public static function createTables() {
		self::createTableClusters();
		self::createTablePeople();
		self::createTableExpenses();
		self::createTableIncomes();
	}

	/**
	  * Create incomes table.
	  * @return void
	  */
	public static function createTableIncomes() {
		$table = Income::tableName();
		mysql_query("
				CREATE TABLE IF NOT EXISTS $table (
					id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id INTEGER,
					cluster_id INTEGER,
					on_ DATETIME,
					description VARCHAR(255),
					amount DECIMAL(12,3),
					attached_to VARCHAR(255),
					attached_id INTEGER)"
			) or self::dieTNC($table); 
	}
	/**
	  * Create expenses table.
	  * @return void
	  */
	public static function createTableExpenses() {
		$table = Expense::tableName();
		mysql_query("
				CREATE TABLE IF NOT EXISTS $table (
					id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id INTEGER,
					cluster_id INTEGER,
					on_ DATETIME,
					description VARCHAR(255),
					amount DECIMAL(12,3),
					attached_to VARCHAR(255),
					attached_id INTEGER)"
			) or self::dieTNC($table); 
	}

	/**
	 * Create cluster table
	 * @return void
	 */
	private static function createTableClusters() {
		$table = Cluster::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id INTEGER,
				name VARCHAR(255),
				attached_to VARCHAR(255),
				attached_id INTEGER
			)") or self::dieTNC($table);
	}
	/**
	 * Table for people.
	 * @return void
	 */
	private static function createTablePeople() {
		$table = Person::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id      INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id INTEGER,
				cluster_id INTEGER,
				name    VARCHAR(255),
				surname VARCHAR(255),
				attached_to VARCHAR(255),
				attached_id INTEGER
			)") or self::dieTNC($table);
	}

}

?>

<h1><?= t("Installation"); ?></h1>
	<p>Connection with database <b><?= Config::$DB_NAME ?></b>
<? 
	if (DB::connect()) echo 'WORKS';
	else die("DOESN'T WORK");
?>
	</p>

	<? Installator::install() ?>

<? Installator::dsd(); ?>

<? /** Install user */ ?>
	<p>
	<? if (InstallUser::userOk('demo')) { ?>
		Demo user account <b>name</b>- demo, <b>password</b>- demo</b>
	<? } else { ?>
		<b>No demo user exists!</b>
	<? } ?>
	</p>

<?
/** Clear session data if there where any sessions. */
if(''==session_id()) {
	session_start();
	session_destroy();
}
?>
<?= Html::ab(t("Start using")) ?>

