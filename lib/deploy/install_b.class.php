<?
/**
 * Base class for install.
 *
 * @version 0.0.1
 */
class InstallB {

	/**
	 * Base install.
	 *
	 * @return void
	 */
	public static function install() {
		self::make_temporary_directory();
	}

	/**
	 * Make temporary directory.
	 *
	 * @return void
	 */
	private static function make_temporary_directory() {
		$dname = Config::tmp_dir();
		if(!file_exists($dname)) {
			mkdir($dname);
		}
	}

	/**
	 * Die. Table not created.
	 */
	protected static function dieTNC($name) {
		return die(t("Table").' '.t($name).' '.t('not created'). mysql_error());
	}

	/**
	 * Destroy session data.
	 */
	public static function dsd() {
		if(''==session_id()) {
			session_start();
			session_destroy();
		}
	}
}


