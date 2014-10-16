<?
/**
 * Logs actions
 *
 * @author Marius Žilėnas
 * @copyright 2013, Marius Žilėnas
 *
 * @version 0.0.1
 */
class LoggerAction extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d"),
			new Field('name', Field::T_TEXT, "%s"),
			new Field('ip', Field::T_TEXT, "%s"),
			new Field('user_id', Field::T_NUMERIC, "%d"),
			new Field('on_', Field::T_TEXT, '%s'),
			new Field('attached_to', Field::T_TEXT, '%s'),
			new Field('attached_id', Field::T_NUMERIC, '%d'),
			new Field('is_access_denied', Field::T_NUMERIC, "%d"),
			new Field('is_error', Field::T_NUMERIC, "%d")
		);
	}

	/**
	 * Create new action
	 *
	 * @param string $name Can be 'view', 'create', 'delete', or other string.
	 */
	public static function new_action($name = NULL) {
		$a       = new static();
		$a->on_  = self::toDateTime($_SERVER['REQUEST_TIME']);
		$a->ip   = $_SERVER['REMOTE_ADDR'];
		$a->name = $name;
		if(Login::is_logged_in()) {
			$a->user_id = Login::logged_id();
		}
		return $a;
	}

	/**
	 * Create successful action log.
	 * @param string $name Action name.
	 * @param mixed $o Object.
	 * @return void
	 */
	public static function action_success($name, $o) {
		$la = self::new_action($name);
		$la->attach_to($o, TRUE);
	}

	/**
	 * Create access denied action log.
	 * @param string $name Action name.
	 * @param mixed $o Object.
	 * @return void
	 */
	public static function action_access_denied($name, $o) {
		$la                   = self::new_action($name);
		$la->is_access_denied = TRUE;
		$la->attach_to($o, TRUE);
	}

	/**
	 * Create error action log.
	 * @param string $name Action name.
	 * @param mixed $o Object.
	 * @return void
	 */
	public static function action_error($name, $o) {
		$la = self::new_action($name);
		$la->is_error = TRUE;
		$la->attach_to($o, TRUE);
	} 
}
