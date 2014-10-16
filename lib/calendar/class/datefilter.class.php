<?

class DateFilter {
	public $m_years;
	public $m_months;
	public $m_days;

	public function set_years($years) {
		$this->m_years = $years;
	}

	public function set_months($months) {
		$this->m_months = $months;
	}

	public function set_days($days) {
		$this->m_days = $days;
	}

	/**
	 * yyyy-mm-dd
	 * yyyy-mm
	 * yyyy
	 *
	 * @return array
	 */
	public function make_filter() {
		$year  = 0 == $this->m_years  ? NULL : $this->m_years ;
		$month = 0 == $this->m_months ? NULL : $this->m_months; 
		$day   = 0 == $this->m_days   ? NULL : $this->m_days  ;
		$ret   = array();
		/** yyyy */
		if(NULL == $month && NULL == $day && NULL != $year) {
			$ret  = array(
				$year.'-'.'01-01 00:00:00', 
				$year.'-'.'12-31 23:59:59');
		/** yyyy-mm */
		} else if(NULL == $day && NULL != $month && NULL != $year) {
			$last_day   = date("d", CalendarDate::monthEnd($year, $month));
			$ret = array(
				$year.'-'.$month.'-01 00:00:00',
				$year.'-'.$month.'-'.$last_day.' 23:59:59');
		/** yyyy-mm-dd */
		} else if(NULL != $day && NULL != $month && NULL != $year) {
			$ret = array("$year-$month-$day");
		}
		return $ret;
	}

	/**
	 * Makes where string for field.
	 * @param string $name Field name. 
	 * @return string|NULL
	 */
	public function make_where_for($name) {
		$str = NULL;
		if($df = $this->make_filter()) {
			if(count($df) == 2) {
				$str = sprintf(' %1$s >= %2$s AND %1$s <= %3$s', 
					Dbobj::iq($name), 
					Dbobj::edq($df[0]), Dbobj::edq($df[1]));
			} else if(count($df) == 1) {
				$str = sprintf(' %s = %s ', Dbobj::iq($name), Dbobj::edq($df[0]));
			}
		}

		return $str ;
	}

}

