<?
/**
 * Class for configuration.
 */
class Config { 
# ------------------------------------------------------
# ---------- APPLICATION RELATED -----------------------
# ------------------------------------------------------
	/** Aplication name */
	const APP                     = 'realmonia';

	/** Temporary directory */
	const DIR_TMP                 = 'tmp/';

	/** Default timezone */
	const TZ                      = 'Europe/Vilnius';

	/** Items per page */
	const ITEMS_PER_PAGE          = 20;

	/** Base currency */
	const BASE_CURRENCY           = 'LTL';

# ------------------------------------------------------
# ---------- INSTALLATION RELATED ----------------------
# ------------------------------------------------------
	const IS_PRODUCTION         = FALSE;

# ---------- BASE LOCATION -----------------------------
	const BASE                  = "http://localhost";

# ---------- DATABASE CONFIGURATION --------------------
	public static $DB_NAME      = self::APP;
	public static $DB_HOST      = 'localhost';
	public static $DB_USER      = 'root';
	public static $DB_PASSWORD  = '';

	public static $SESSION_SHOW = FALSE;

# ---------- DEVELOPER RELATED -------------------------
	/**
	 * Returns full path to application.
	 * @return string
	 */
	public static function base() {
		return self::BASE.'/'.self::APP;
	}

	/**
	 * Is production?
	 * @return boolean
	 */
	public static function is_production() {
		return defined('self::IS_PRODUCTION') && self::IS_PRODUCTION;
	}

	/**
	 * Email from
	 * @return string|NULL
	 */
	public static function email_from() {
		$ret = NULL;
		if(defined('self::EMAIL_FROM')) {
			$ret = self::EMAIL_FROM;
		} else if(class_exists('ConfigMail')) {
			$ret = ConfigMail::EMAIL_FROM;
		}
		return $ret;
	}

	/**
	 * Items per page.
	 * @return integer|NULL
	 */
	public static function items_per_page() {
		return self::ITEMS_PER_PAGE;
	}

	/**
	 * Temporary directory
	 * @return string
	 */
	public static function tmp_dir() {
		return self::DIR_TMP;
	}

}

