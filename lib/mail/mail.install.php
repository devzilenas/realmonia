<?

/**
 * Setup for mail.
 */

class InstallMail {

	/**
	 * Base install.
	 *
	 * @return void
	 */
	public static function install() {
		self::createTables();
		self::make_emails_directory();
	}

	/**
	 * Create tables
	 *
	 * @return void
	 */
	public static function createTables() {
		self::createTableEmails();
	}

	/**
	 * Make directory for emails. Used when emails are not sent.
	 *
	 * @return void
	 */
	private static function make_emails_directory() {
		$dname = EmailManager::edir();
		if(!file_exists($dname)) {
			mkdir($dname);
		}
	}

	/**
	 * Create emails table.
	 */
	private static function createTableEmails() {
		$table = Email::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				subject VARCHAR(255),
				message TEXT,
				is_html TINYINT(1),
				is_sent TINYINT(1),
				to_		VARCHAR(255)
			)") or self::dieTNC($table);
	}

}

