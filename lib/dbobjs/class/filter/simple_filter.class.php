<?

/**
 * @version 0.0.1
 * Holds values for filters of class.
 * array('field_name' => Field)
 * Filters integer data, strings, date time.
 */
class SimpleFilter {
	private $data = array();

	public function set($data) {
		$this->data = $data;
	}
	public function get($name, $subname = NULL) {
		$ret = NULL;
		if($subname && isset($this->data[$name][$subname])) {
			$ret = $this->data[$name][$subname];
		} else {
			if(isset($this->data[$name])) {
				$ret = $this->data[$name];
			}
		}
		return $ret;
	}

	public function get_values() {
		$values = array();
		foreach($this->data as $name => $val) {
			/** Only date, time fields have val of type array */
			if(is_array($val)) {
				foreach($val as $k => $v) {
					if(!empty($v)) {
						if(isset($values[$name])) {
							$value = $values[$name];
						} else {
							$value = new DateFilter();
						}
						$value->{'set_'.$k}($v);
						$values[$name] = $value;
					}
				}
			} else {
				if(!empty($val)) {
					$values[$name] = $val;
				}
			}
		}
		return $values;
	}

	/**
	 * Makes where part.
	 * @param array $wheres
	 * @return array
	 */
	public function make_wheres(array $wheres = array()) {
		if($values = $this->get_values()) {
			foreach($values as $name => $value) {
				if(!empty($values)) {
					if(is_object($value) && 'DateFilter' == get_class($value)) {
						if($w = $value->make_where_for($name)) {
							$wheres[] = $w;
						}
					} else {
						$wheres[] = sprintf("%s = %s", Dbobj::iq($name), Dbobj::edq($value));
					}
				}
			}
		}
		return $wheres;
	}
}

