<?

class RequestMonia extends Request implements ReqInterface {
	/**
	 * Is currency set
	 * @return boolean
	 */
	public static function is_currency_set() {
		return self::is_action('currency_set');
	}

	/**
	 * Process: set currency.
	 * @return void
	 */
	private static function process_currency_set() {
		$u = Login::user();
		if(isset($_POST['currency']) && Currency::is_valid($_POST['currency'])) {
			$u->currency = $_POST['currency'];
			$u->save();
			Logger::info(t("Success"));
			self::hlexit("?account&my");
		} else {
			self::error();
		}
	}

	/**
	 * Base process.
	 * @return void
	 */
	public static function process() {
		if(self::is_currency_set()) {
			self::process_currency_set();
		}
	}

}
