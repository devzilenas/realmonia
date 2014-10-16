<?
/** 
 * Authentication request processing.
 *
 * @version 0.0.3
 */
class RequestAuth extends Request {

	public static function is_account_my() {
		return isset($_GET['account'], $_GET['my']);
	}

	public static function is_create_user() {
		return self::is_create('User');
	}

	public static function is_activate_user() {
		return isset($_GET['activate'], 
			         $_GET['id'],
					 $_GET['aid']);
	}

	public static function is_action_login() {
		return self::is_action("login");
	}

	public static function is_logout() {
		return self::is_action('logout');
	}

	public static function is_login() {
		return isset($_GET['login']);
	}

	public static function process() { 

		if(self::is_create_user()) {
			self::process_create_user();
		}

		if(self::is_activate_user()) {
			self::process_activate_user();
		}

		if(self::is_action_login()) {
			self::process_login();
		}

		if(Login::is_logged_in()) {

			if(self::is_logout()) {
				self::process_logout();
			}

			if(self::is_account_my()) {
				self::process_account_my();
			}
		}

	}

	/**
	 * My account.
	 * @return void
	 */
	private static function process_account_my() {
		self::set_out('user', Login::user());
	}

	/**
	 * Create user.
	 *
	 * @return void
	 */
	private static function process_create_user() {
		$user             = $_POST['user'];
		$_SESSION['user'] = $user;

		if ($user['password'] == $user['password_confirm']) {
			if (Login::create_user($user['login'], $user['password'], $user['email'])) {
				Logger::info(t("Created!"));
				self::r2b();
			} else {
				Logger::undefErr(t("User not created!"));
				self::r2n("User");
			}
		} else {
			Logger::err('PASS_MATCH', t("Passwords don't match!"));
			self::r2n("User");
		}
	}

	/**
	 * Activate user.
	 *
	 */
	private static function process_activate_user() {
		if(User::activate($_GET['id'], urldecode($_GET['aid']))) {
			Logger::info(t("User activated! You can login now."));
		} else {
			Logger::undefErr(t("User not activated!"));
		}
		self::r2b();
	}

	/**
	 * Logs user in and redirects to Config::BASE.
	 *
	 * @return void
	 */
	private static function process_login() {
		if (isset($_POST['user'], $_POST['user']['login'], $_POST['user']['password'])) {
			if(!($user = User::load_by(array(
						'User.login'  => $_POST['user']['login'],
						'User.phash'  => Crypt::genPhash($_POST['user']['password']),
						'User.is_active' => 1)))) {
				Logger::undefErr(t("Login failed!"));
				self::error();
			} else {
				Login::log_user_in($user);
				Logger::info(t("Successfuly logged in!"));
				self::r2b();
			}
		}
	}

	/**
	 * Logout.
	 *
	 * @return void
	 */
	private static function process_logout() {
		if(Login::is_logged_in()) {
			$user = Login::user();
			Login::logout($user);
			Logger::info(t("Bye bye!"));
			self::hlexit("?login");
		} else {
			Logger::undefErr("Not logged out!");
			self::error();
		}
	}
}

