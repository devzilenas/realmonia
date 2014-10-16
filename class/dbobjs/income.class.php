<?

class Income extends Dbobj implements DbobjInterface {
	/**
	  * Fields.
	  * @return Field[]
	  */
	public static function fields() {
		return array(
				new Field('id', Field::T_NUMERIC, "%d"),
				new Field('user_id', Field::T_NUMERIC, '%d'),
				new Field('cluster_id', Field::T_NUMERIC, "%d"),
				new Field('on_', Field::T_TEXT),
				new Field('description', Field::T_TEXT),
				new Field('amount', Field::T_NUMERIC, "%.2f"),
				new Field('attached_to', Field::T_TEXT),
				new Field('attached_id', Field::T_NUMERIC, "%d"));
	}
	/**
	  * Editable fields.
	  * @return Field[]
	  */
	public static function editable_fields() {
		return fan(self::fields(), array('id', 'user_id'));
	}
}

