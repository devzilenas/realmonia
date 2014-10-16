<?

class Person extends Dbobj implements DbobjInterface {
	/**
	 * Fields for Person.
	 *
	 * @return Field[]
	 */
	public static function fields() {
		return array(
			new Field("id", Field::T_NUMERIC, "%d"),
			new Field('user_id', Field::T_NUMERIC, "%d"),
			new Field('hive_id', Field::T_NUMERIC, "%d"),
			new Field('cluster_id', Field::T_NUMERIC, "%d"),
			new Field("name", Field::T_TEXT),
			new Field("surname", Field::T_TEXT),
			new Field('attached_to', Field::T_TEXT),
			new Field('attached_id', Field::T_NUMERIC, "%d")
		);
	}

	/**
	 * Editable fields.
	 * @return Field[]
	 */
	public static function editable_fields() { 
		return fan(self::fields(), array('user_id', 'attached_to', 'attached_id', 'hive_id', 'cluster_id'));
	}
	/**
	 * As string
	 * @return string
	 */
	public function to_s() {
		$name = $this->name;
		if(!empty($name)) {
			$ret = $name;
		} else {
			$ret = parent::to_s();
		}
		return $ret;
	}

	/**
	 * Returns full name.
	 * @return string
	 */
	public function full_name() {
		return join(' ', array($this->name, $this->surname));
	}
}

