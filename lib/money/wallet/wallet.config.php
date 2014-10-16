<?
/**
 * Wallet configuration
 *
 * @version 0.0.1
 */
class ConfigWallet {
	const BASE_CURRENCY = 'RSM';
	/**
	 * Get base currency for wallet.
	 *
	 * @return Currency
	 */
	public static function base_currency_wallet() {
		$ret = NULL;
		if(method_exists('Config', 'base_currency_walet')) {
			$ret = Config::base_currency_wallet();
		} else {
			$ret = new Currency(self::BASE_CURRENCY);
		}
		return $ret;
	}
}

