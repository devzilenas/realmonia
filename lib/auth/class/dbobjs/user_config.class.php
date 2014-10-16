<?

/**
 * User configuration.
 */
class UserConfig extends Dbobj implements DbobjInterface {
	/**
	 * Fields.
	 *
	 * @return Field[]
	 */
	public static function fields() {
		return array(
			new Field("id", Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field("user_id", Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field("items_per_page", Field::T_NUMERIC, "%d", FALSE, FALSE)
		);
	}

	/**
	 * Items per page.
	 *
	 * @param User $user
	 *
	 * @return integer|NULL
	 */
	public static function get_items_per_page(User $user) {
		$ret = NULL;
		if($o = self::load_by(array(
			"UserConfig.user_id" => $user->id))) {
			$ret = $o->reals_per_page;
		}
		return $ret;
	}

}
