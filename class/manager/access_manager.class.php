<?

class AccessManager { 

	public static function can_view(User $user, $o) {
		$ret = FALSE;
		switch(get_class($o)) {
			case "Person":
				/** can view if is from the same hive */
				if($up = Person::load_by(sprintf("user_id = %d", $user->id))) {
					$ret = $up->hive_id == $o->id;
				}
				break;
		}
		return $ret;
	}

}

