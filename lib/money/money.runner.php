<?

include_once 'class/syncer_rate.class.php';

/**
 * Runner for money.
 *
 * @version 0.0.1
 */
class RunnerMoney {

	/**
	 * Base run.
	 * 
	 * @return void
	 */
	public static function run() {
		self::sync_rates();
	}

	/**
	 * Sync rates
	 *
	 * @return void
	 */
	private static function sync_rates() {
	}

}

