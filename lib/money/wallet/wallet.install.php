<?
/**
 * Wallet installation
 *
 * @version 0.0.1
 */
class InstallWallet extends InstallB {
	/**
	 * Base install
	 *
	 * @return void
	 */
	public static function install() {
		self::createTables();
	}

	/**
	 * Create wallet tables
	 *
	 * @return void
	 */
	private static function createTables() {
		self::createTableWallets();
		self::createTableWalletLines();
	}

	/**
	 * Create wallet table
	 *
	 * @return void
	 */
	private static function createTableWallets() {
		$table = Wallet::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id       INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id  INTEGER,
				name     VARCHAR(255),
				currency CHAR(3)
			)") or self::dieTNC($table); 
	}

	/**
	 * Create wallet line table
	 *
	 * @return void
	 */
	private static function createTableWalletLines() {
		$table = WalletLine::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id          INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				wallet_id   INTEGER,
				amount      DECIMAL(12,5),
				amount_left DECIMAL(12,5),
				attached_to VARCHAR(255),
				attached_id INTEGER,
				what        VARCHAR(255),
				on_         DATETIME, 
				currency    CHAR(3)
			)") or self::dieTNC($table);
	}
}

