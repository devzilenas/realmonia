<?

class ReqWallet extends Request implements ReqInterface {

	public static function process() {
		if(self::is_view_my('Wallet')) {
			self::process_view_my_wallet();
		}
	}

	/**
	 * View my wallet.
	 *
	 * @return void
	 */
	private static function process_view_my_wallet() {
		if($w = WalletManager::wallet_for(Login::user())) { 
			self::set_out('wallet', $w);
		} else {
			self::error();
		}
	}

}

