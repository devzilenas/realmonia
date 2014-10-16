<?

class ConfigMonia {

	const BASE_CURRENCY = 'LTL';

	public static function base_currency() {
		$ret = NULL;
		if(class_exists('Config')) {
		   if(method_exists('Config', 'base_currency')) {
				$ret = Config::base_currency();
		   } else if(defined("Config::BASE_CURRENCY") && Currency::is_valid(self::BASE_CURRENCY)) {
			   $ret = new Currency(Config::BASE_CURRENCY);
		   }
		}
		if(!$ret) {
			$ret = new Currency(self::BASE_CURRENCY);
		}
		return $ret;
	}

}

