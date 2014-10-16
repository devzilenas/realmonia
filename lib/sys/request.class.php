<?

/**
 * Base class for request management.
 *
 * @version 0.1.4
 */
class Request {
	/** TRUE when request is to api. */
	private static $m_api;
	/** values that are returned by request */
	public static $m_out = array();

	public static function process() {
		if(self::is_p()) {
			self::process_view_p();
		}
	}

	private static function process_view_p() {
		include_once 'sub/pages/'.$_GET['p'].'.sub.php';
	}

	/**
	 * Setter for m_out.
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public static function set_out($name, $value) {
		self::$m_out[$name] = $value;
	}

	/**
	 * Getter for m_out.
	 * @param string (optional) $name
	 * @return mixed|NULL
	 */
	public static function out($name = NULL) {
		$out = NULL;
		if(NULL === $name) {
			$out = self::$m_out;
		} else if(isset(self::$m_out[$name])) {
			$out = self::$m_out[$name];
		}
		return $out;
	}

	/**
	 * Setter for m_api.
	 * @param boolean $api
	 */
	public static function setApi($api) { 
		self::$m_api = $api;
	}

	/**
	 * Getter for m_api.
	 * @return boolean
	 */
	public static function isApi() {
		return self::$m_api;
	}

	/**
	 * Returns base location.
	 * @return string
	 */
	public static function base() {
		return Config::base();
	}

	/**
	 * Redirects to BASE
	 * @return void
	 */
	public static function r2b() {
		self::hlexit(self::base());
	}

	/**
	 * Checks whether is action.
	 * @param string $name Action name
	 * @return boolean
	 */
	public static function is_action($name) {
		return isset($_POST['action']) && $name === $_POST['action'];
	}

	/**
	 * Checks whether is view.
	 * @param string $name
	 * @return boolean
	 */
	public static function is_view($cl) {
		return isset($_GET['view'], $_GET[c2u($cl)]);
	}

	/**
	 * Checks whether is new.
	 * 
	 * @param string $cl Class name.
	 *
	 * @return boolean
	 */
	public static function is_new($cl) {
		return isset($_GET['new'], $_GET[c2u($cl)]);
	}

	/**
	 * Checks whether is form submit to create a new object.
	 *
	 * @param string $cl Classname.
	 *
	 * @return boolean 
	 */
	public static function is_create($cl) {
		return isset($_POST['action'], $_POST[c2u($cl)]) && 'create' == $_POST['action'];
	}
	/**
	 * Is it edit action.
	 * @param string $class_name 
	 * @return boolean
	 */
	public static function is_edit($class_name) {
		return isset($_GET['edit'], $_GET[c2u($class_name)]);
	}

	/**
	 * Is it update action
	 * @param string $cl Class name.
	 * @return boolean
	 */
	public static function is_update($cl) {
		return self::is_action('update') && isset($_POST[c2u($cl)]);
	}

	/**
	 * Is delete action.
	 * @param string $class_name Class name of object that is deleted.
	 * @return boolean
	 */
	public static function is_delete($cl) {
		return self::is_action('delete') && isset($_GET[c2u($cl)]);
	}

	/**
	 * Is it list action.
	 * @param string $class_name Class name of objects that are listed.
	 * @return boolean
	 */
	public static function is_list($class_name) {
		return isset($_GET['list'], $_GET[c2up($class_name)]);
	}

	/**
	 * Returns value from request.
	 * @param string $name
	 * @return integer
	 */
	public static function get0($name) {
		return (!empty($_REQUEST[$name])) ? (int)$_REQUEST[$name] : 0;
	}

	/**
	 * Returns value from request.
	 * @param string $name
	 * @return mixed|NULL
	 */
	public static function getNull($name) {
		return (!empty($_REQUEST[$name])) ? $_REQUEST[$name] : NULL;
	}

	/**
	  * Gets value as array from GET.
	  *
	  * @param string $name
	  * 
	  * @return array
	  */
	public static function gA($name) {
		return isset($_GET[$name]) && is_array($_GET[$name]) ? $_GET[$name] : array();  
	}

	/**
	 * Gets value from POST.
	 *
	 * @param string $name
	 * 
	 * @return array
	 */
	public static function gPostArray($name) {
		return isset($_POST[$name]) && is_array($_POST[$name]) ? $_POST[$name] : array();
	}

	/**
	 * Redirects and exits.
	 *
	 * @param string location
	 *
	 * @return void
	 */
	public static function hlexit($location) {
		header("Location: ".$location);
		exit;
	}

	/**
	 * Redirect to new
	 *
	 * @param string $cl Classname.
	 *
	 * @return void
	 */
	public static function r2n($cl) {
		self::hlexit(Html::link_to_new($cl));
	}

	/**
	 * Redirect to view
	 *
	 * @param mixed $o Object.
	 *
	 * @return void
	 */
	public static function r2v($o) {
		self::hlexit(Html::link_to_view($o));
	}

	/**
	 * Redirect to edit
	 *
	 * @param mixed $o object.
	 *
	 * @return void
	 */ 
	public static function r2e($o) {
		self::hlexit(Html::link_to_edit($o));
	}

	/**
	 * Redirect to list
	 *
	 * @param string $cl
	 *
	 * @return void
	 */
	public static function r2l($cl) {
		self::hlexit(Html::link_to_list($cl));
	}

	/**
	 * Saves object to session. 
	 *
	 * @param $name string Class name of the object wich data is saved.
	 * @param $data array Data to save.
	 *
	 * @return void
	 */
	static function saveToSession($name, array $data) {
		$_SESSION[c2u($name)] = $data;
	}

	/**
	 * Loads object from session and unsets it.
	 *
	 * @param $name string Name of the Class.
	 *
	 * @return array|NULL
	 */
	protected static function loadFromSessionU($name) {
		if(isset($_SESSION[$name])) {
			$n   = c2u($name);
			$ret = $_SESSION[$n];
			unset($_SESSION[$n]);
			return $ret;
		}
	}

	/**
	 * Saves validation data into session.
	 *
	 * @param $class string Name of the class to which object belongs.
	 * @param $validation array Validation information.
	 *
	 * @return void
	 */
	protected static function saveValidation($class, array $validation) {
		$_SESSION[c2u($class).'_validation'] = $validation;
	}

	/**
	 * Loads object data from POST.
	 *
	 * @param $class string Class name of the object.
	 *
	 * @param $fields array Field names to load data from.
	 * 
	 * @return object Object of the class data.
	 */
	protected static function oFromForm($class, array $fields) {
		$cl = c2u($class);
		if(isset($_POST[$cl])) {
			$o = $class::fromForm($_POST[$cl], $fields);
			return $o;
		}
	}

	/**
	 * Is my ?
	 *
	 * @param string $cl Class name.
	 *
	 * @return boolean
	 */
	public static function is_view_my($cl) {
		return self::is_view($cl) && isset($_GET[c2u($cl)]);
	}

	/**
	 * Is view page.
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public static function is_p() {
		return isset($_GET['p']) && file_exists('sub/pages/'.$_GET['p'].'.sub.php');
	}

	/**
	 * Access denied.
	 * @param string $redirect_to (optional) Redirect link.
	 * @return void
	 */
	private static function access_denied($redirect_to = NULL) {
		Logger::error(t("Access denied!"));
		if(NULL !== $redirect_to) {
			self::hlexit($redirect_to);
		} else {
			self::r2b();
		}
	}

	/**
	 * Error.
	 * @param string $message (optional) Message.
	 * @return void
	 */
	protected static function error($msg = NULL) {
		Logger::undefErr(t($msg ? $msg : "Error!"));
		Request::r2b();
	}

	/**
	  * Log access denied with object and redirect.
	  * @param string $name Action name.
	  * @param mixed $o Object.
	  * @return void
	  */
	protected static function ladwor($name, $o) {
		LoggerAction::action_access_denied($name, $o);
		self::access_denied();
	}
	/**
	  * Log error with object and redirect.
	  * @param string $name Action name.
	  * @param mixed $o Object.
	  * @return void
	  */
	protected static function lewor($name, $o) {
		LoggerAction::action_error($name, $o);
		self::error();
	}
}

