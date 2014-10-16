<?
/**
 * Logger installation.
 *
 * @version 0.0.1
 */
class InstallLogger extends InstallB {
	/**
	 * Base install
	 *
	 * @return void
	 */
	public static function install() {
		self::createTables();
	}

	/**
	 * Create logger tables.
	 *
	 * @return void
	 */
	private static function createTables() {
		self::createTableLoggerActions();
	}

	/**
	 * Create logger action table.
	 *
	 * @return void
	 */
	private static function createTableLoggerActions() {
		$table = LoggerAction::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id          INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name        VARCHAR(255),
				ip          VARCHAR(255),
				user_id     INTEGER,
				on_         DATETIME,
				attached_to VARCHAR(255),
				attached_id INTEGER,
				is_access_denied TINYINT(1),
				is_error    TINYINT(1)
			)") or self::dieTNC($table);
	}

}

