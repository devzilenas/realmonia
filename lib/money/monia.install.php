<?
/**
 * Setup for money.
 *
 * @version 0.0.3
 */
class InstallMonia extends InstallB {
	/**
	 * Base install.
	 *
	 * @return void
	 */
	public static function install() {
		self::createTables();
	}

	/**
	 * Create tables.
	 *
	 * @return void
	 */
	public static function createTables() {
		self::createTableExchangeRates();
	}

	private static function createTableExchangeRates() {
		$table = ExchangeRate::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id            INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				ratedate      DATE,
				from_currency CHAR(3),
				to_currency   CHAR(3),
				quantity      INTEGER,
				rate          DECIMAL(12,5))")
			or self::dieTNC($table); 
	}
}

