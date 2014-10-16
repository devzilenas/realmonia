<?

/**
 * User class.
 *
 * @version 0.1.1 
 *
 */
class User extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field('login', Field::T_TEXT),
			new Field('phash', Field::T_TEXT),
			new Field('sid', Field::T_TEXT),
			new Field('email', Field::T_TEXT),
			new Field('aid', Field::T_TEXT),
			new Field('is_active', Field::T_BOOLEAN),
			new Field('time_zone', Field::T_TEXT),
			new Field('currency', Field::T_TEXT),
			new Field('api_key', Field::T_TEXT)
		);
	}

	/**
	 * After insert.
	 * @param integer id
	 * @param self $o
	 * @return void
	 */
	public function after_insert() { 
		/** Create person for the user */
		if(class_exists('Person') && !Person::exists_by(array("user_id" => $this->id))) {
			$person = new Person();
			$this->owns($person);
			$person->save();
		}
	}

	/**
	 * Activates user.
	 *
	 * @return boolean
	 */
	public static function activate($id, $aid) {
		$ret = FALSE;
		if ($user = self::load($id)) {
			$ret = $user->aid === $aid && !$user->is_active && self::update($id,
					array('is_active', 'aid'),
					array('is_active' => 1, 'aid' => ''));
		}
		return $ret;
	}

	/**
	 * Is owner of object
	 * @param mixed $o
	 * @return boolean
	 */
	public function is_owner_of($o) {
		return Access::is_owner($this, $o);
	}

	/**
	 * Can view object?
	 * @param mixed $o
	 * @return boolean
	 */
	public function can_view($o) {
		return Access::can_view($this, $o);
	}
	/**
	 * Can edit object?
	 * @param mixed $o
	 * @return boolean
	 */
	public function can_edit($o) {
		return Access::can_edit($this, $o);
	}

	/**
	 * Can update object?
	 * @param mixed $o
	 * @return boolean
	 */
	public function can_update($o) {
		return Access::can_update($this, $o);
	} 

	/**
	 * Makes user owner of object.
	 * @param mixed $o
	 * @return void
	 */
	public function owns($o) {
		Access::owns($this, $o);
	}

	/**
	 * Returns person name and surname if exist or email.
	 * @return string
	 */
	public function nsore() {
		$person   = Person::load_by(sprintf("user_id = %d", $this->id));
		$fullname = $person->full_name(); 
		if(!empty($fullname)) {
			$ret = $fullname;
		} else {
			$ret = $this->email;
		}
		return $ret;
	}
}

