<?

class Cluster extends Dbobj implements DbobjInterface {

	/**
	 * Array of fields for Cluster.
	 *
	 * @return Field[]
	 */
	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, '%d'),
			new Field('name', Field::T_TEXT),
			new Field('user_id', Field::T_NUMERIC, "%d"),
			new Field('attached_to', Field::T_TEXT),
			new Field('attached_id', Field::T_NUMERIC, "%d")
		);
	}

	/**
	 * Validation
	 * @return array
	 */
	public function hasValidationErrors() {
		$validation = array();
		if($v = self::validateNotEmpty('name')) {
			$validation['name'] = $v;
		}
		return $validation;
	}
	/**
	 * Editable fields
	 * @return Field[]
	 */
	public static function editable_fields() {
		return fan(self::fields(), array('id', 'user_id'));
	}
	/**
	 * Showable fields
	 * @return Field[]
	 */
	public static function showable_fields() {
		return fan(self::editable_fields(), array('attached_id'));
	}

	/**
	 * Make list of clusters for user.
	 * @param string $cl Class name.
	 * @param User $user
	 * @return Cluster[]
	 */
	public static function options_for($cl, User $user) {
		$filter = new Filter("id,name");
		$filter->setFrom(array("Cluster" => "c"));
		$filter->setWhere(array(
			'Cluster.user_id'     => $user->id,
			'Cluster.attached_to' => $cl
		));
		$filter->setGroupBy("name");
		$filter->setOrderBy('name');
		return self::find($filter);
	}

}

