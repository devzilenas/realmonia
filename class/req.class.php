<?php 

/**
 * Request processor.
 */
class Req extends Request implements ReqInterface {
	/**
	 * Is reset filter
	 * @return boolean
	 */
	public static function is_reset_filter() {
		return self::is_action('reset_filter') && isset($_POST['filter_for']);
	}
	/**
	 * Is set filter
	 * @return boolean
	 */
	public static function is_set_filter() {
		return self::is_action('set_filter') && isset($_POST['filter']);
	}
	/**
	 * Is set items per page
	 *
	 * @return boolean
	 */
	public static function is_set_items_per_page() {
		return self::is_action('set_items_per_page') ;
	}

	/**
	 * Is update person
	 *
	 * @return boolean
	 */
	public static function is_update_person() {
		return self::is_action('update') && isset($_POST['person']);
	} 

	/**
	 * Is time zone set
	 *
	 * @return boolean
	 */
	public static function is_time_zone_set() {
		return self::is_action('time_zone_set');
	}

	/**
	 * Is currency set
	 * @return boolean
	 */
	public static function is_currency_set() {
		return self::is_action('currency_set');
	}

	/**
	 * Process create person.
	 * @return void
	 */
	private static function process_create_person() {
		$person = new Person();
		$person->ffde($_POST['person'], Person::editable_fields());
		$user = Login::user();

		/** Make user owner of this person */
		Access::owns($user, $person);

		if($validation = $person->hasValidationErrors()) {
			$SESSION['person_validation'] = $validation;
			self::saveToSession('Person', $_POST['person']);
			Logger::undefErr(array_values($validation));
			self::r2n("Person");
		} else {
			$person->save();
			LoggerAction::action_success('create', $person);
			Logger::info(t("Person saved!"));
			self::r2v($person);
		}
	}
	/**
	 * Process update person.
	 *
	 * @return void
	 */
	private static function process_update_person() {
		if($person = Person::load($_GET['person'])) {
			$user = Login::user();
			if($user->can_update($person)) {
				$person->ff($_POST['person'], Person::editable_fields());
				if($validation = $person->hasValidationErrors()) {
					self::saveToSession('Person', $_POST['person']);
					Logger::undefErr(array_values($validation));
					self::r2e($person);
				} else {
					$person->save();
					Logger::info(t("Updated!"));
					self::r2v($person);
				}
			} else {
				self::access_denied();
			}
		}
	}

	/**
	 * Process view person
	 *
	 * @return void
	 */
	private static function process_view_person() {
		if($person = Person::load($_GET['person'])) {
			$user = Login::user();
			if($user->can_view($person)) {
				self::set_out('person', $person);
				LoggerAction::action_success('view', $person);
			} else {
				LoggerAction::action_access_denied('view', $person);
				self::access_denied();
			}
		} else {
			LoggerAction::action_error('view', $person);
			self::error();
		}
	}

	/**
	 * Process edit person
	 *
	 * @return void
	 */
	private static function process_edit_person() {
		if($person = Person::load($_GET['person'])) {
			$user = Login::user();
			if($user->can_edit($person)) { 
				self::set_out('person',$person);
				LoggerAction::action_success('edit', $person);
			} else {
				LoggerAction::action_access_denied('edit', $person);
				self::access_denied();
			}
		} else {
			LoggerAction::action_error('edit', $person);
			self::error();
		}

	}

	/**
	 * Process set items per page.
	 *
	 * @return void
	 */
	private static function process_set_items_per_page() {
		$ipp = Config::items_per_page();
		if(is_numeric($_POST['items_per_page'])) {
			$ipp = abs($_POST['items_per_page']);
		}
		$_SESSION['items_per_page'] = $ipp;
		self::r2b();
	}

	/**
	  * Process: list expenses.
	  * @return void
	  */
	private static function process_list_expenses() {
		$user   = Login::user();

		$wheres = array(
			sprintf('e.user_id = %d', $user->id));

		if($simple_filter = Session::g0d('filter_expense', NULL)) { 
			$wheres = $simple_filter->make_wheres($wheres);
		} else {
			$simple_filter = new SimpleFilter();
		}
		self::set_out('simple_filter', $simple_filter);

		$whats = array('e.*', 'c.name as cluster_name');

		$filter = new SqlFilter(join(',', $whats));
		$filter->setFrom(Expense::tableName().' e');
		$filter->setJoin("LEFT JOIN ".Cluster::tableName()." c");
		$filter->setOn("ON c.id = e.cluster_id");
		$filter->setWhere(join(' AND ', $wheres));
		$filter->setOrderBy('e.on_ DESC');

		$epp      = Session::g0d('items_per_page', Config::items_per_page());
		$expenses = new ObjSet('Expense', $filter, self::get0('page'), $epp);
		$expenses->loadNextPage();

		/** all clusters */
		$cf = new SqlFilter('c.id, c.name');
		$cf->setFrom(Cluster::tableName().' c');
		$cf->setJoin('JOIN '.Expense::tableName().' e');
		$cf->setOn('ON c.id = e.cluster_id');
		$cf->setGroupBy('e.cluster_id');
		$cf->setWhere(join(' AND ', $wheres));
		$clusters = Cluster::find($cf);
		$cluster_options = array('' => NULL);
		foreach($clusters as $cluster) {
			$cluster_options[$cluster->id] = $cluster->name;
		}

		$total_clustered = Expense::total_grouped_by('cluster_id', 'amount', $filter);
		$chd = array();
		foreach($total_clustered as $tcl) {
			$chd[$tcl->cluster_name] = $tcl->asc('total')->as_f(); 
		}
		$chart = new ChartPie(400, 300, $chd);
		self::set_out('chart', $chart);

		$total = Expense::total_on('amount', $filter);

		self::set_out("expenses", $expenses);
		self::set_out("user", $user);
		self::set_out('total', $total);
		self::set_out('total_clustered', $total_clustered);
		self::set_out('cluster_options', $cluster_options);
	}

	/**
	  * Process: create expense.
	  * @return void
	  */
	private static function process_create_expense() {
		$e  = new Expense();
		$ed = $_POST['expense'];
		$e->ffde($ed, Expense::editable_fields());
		if(isset($ed['on_month'], $ed['on_day'], $ed['on_year'])) {
			$e->on_ = Dbobj::toDate(mktime(0, 0, 0, $ed['on_month'], $ed['on_day'], $ed['on_year']));
		} else {
			$e->on_ = Dbobj::toDate($_SERVER['REQUEST_TIME']);
		}
		$user = Login::user();
		$user->owns($e);

		if($validation = $e->hasValidationErrors()) {
			$_SESSION['expense_validation'] = $validation;
			self::saveToSession('Expense', $ed);
			Logger::undefErr(array_values($validation));
			self::r2n('Expense');
		} else {
			if($e->save()) {
				LoggerAction::action_success('create', $e);
				Logger::info(t("Created!"));
				self::r2l('Expense');
			} else {
				LoggerAction::action_error('create', $e);
				self::error();
			}
		}
	}

	/**
	  * Process: edit income.
	  * @return void
	  */
	private static function process_edit_income() {
		if($i = Income::load($_GET['income'])) {
			$user = Login::user();
			if($user->can_edit($i)) { 
				self::set_out('cluster_options', HtmlBlock::cluster_options_for("Income"));
				self::set_out('income', $i);
			} else {
				LoggerAction::action_access_denied('edit', $i);
				self::access_denied();
			}
		} else {
			$i = new Income();
			$i->id = $_GET['income'];
			LoggerAction::action_error('edit', $i);
			self::error();
		}
	}
	/**
	  * Process: edit expense.
	  * @return void
	  */
	private static function process_edit_expense() {
		if($e = Expense::load($_GET['expense'])) {
			$user = Login::user();
			if($user->can_edit($e)) { 
				self::set_out('cluster_options', HtmlBlock::cluster_options_for("Expense"));
				self::set_out('expense', $e);
			} else {
				LoggerAction::action_access_denied('edit', $e);
				self::access_denied();
			}
		} else {
			$e = new Expense();
			$e->id = $_GET['expense'];
			LoggerAction::action_error('edit', $e);
			self::error();
		}
	}
	/**
	  * Process: update income.
	  * @return void
	  */
	private static function process_update_income() {
		if($i = Income::load($_GET['income'])) {
			$user = Login::user();
			if($user->can_edit($i)) {
				$i->ff($_POST['income'], Income::editable_fields());
				if($validation = $i->hasValidationErrors()) {
					self::saveToSession('Income', $_POST['income']);
					Logger::undefErr(array_values($validation));
					self::r2e($i);
				} else {
					if($i->save()) {
						LoggerAction::action_success('update', $i);
						Logger::info(t("Updated!"));
						self::r2l("Income");
					}
				}
			} else {
				self::ladwor('update', $i);
			}
		} else {
			$i     = new Income();
			$i->id = $_GET['income'];
			self::lewor('update', $i);
		}
	}
	/**
	  * Process: update expense.
	  * @return void
	  */
	private static function process_update_expense() {
		if($e = Expense::load($_GET['expense'])) {
			$user = Login::user();
			if($user->can_edit($e)) {
				$e->ff($_POST['expense'], Expense::editable_fields());
				if($validation = $e->hasValidationErrors()) {
					self::saveToSession('Expense', $_POST['expense']);
					Logger::undefErr(array_values($validation));
					self::r2e($e);
				} else {
					if($e->save()) {
						LoggerAction::action_success('update', $e);
						Logger::info(t("Updated!"));
						self::r2l("Expense");
					}
				}
			} else {
				self::ladwor('update', $e);
			}
		} else {
			$e     = new Expense();
			$e->id = $_GET['expense'];
			self::lewor('update', $e);
		}
	}
	/**
	 * Process: reset filter, removes filter.
	 * @return void
	 */
	private static function process_reset_filter() {
		$for = $_POST['filter_for'];
		unset($_SESSION['filter_'.$for]);
	}
	/**
	 * Process: set filter.
	 * @return void
	 */
	private static function process_set_filter() {
		$fd            = $_POST['filter'];
		$cls           = array_keys($fd);
		$cl            = NULL;
		/** each class */
		foreach($cls as $c) {
			$cl = u2c($c);
			$cd = $fd[$c];
			$simple_filter = new SimpleFilter();
			$simple_filter->set($cd);
			$_SESSION['filter_'.$c] = $simple_filter;
		}

	}

	public static function process($api = FALSE) {
		RequestAuth::process();
		RequestMonia::process();

		if(self::is_set_items_per_page()) {
			self::process_set_items_per_page();
		}

		if(Login::is_logged_in()) {

			if(self::is_set_filter()) {
				self::process_set_filter();
			}
			if(self::is_reset_filter()) {
				self::process_reset_filter();
			}

			if(self::is_new("Income")) {
				self::process_new_income();
			}

			if(self::is_create("Income")) {
				self::process_create_income();
			}

			if(self::is_list("Income")) {
				self::process_list_incomes();
			}

			if(self::is_edit("Income")) {
				self::process_edit_income();
			}

			if(self::is_update("Income")) {
				self::process_update_income();
			}

			if(self::is_new('Expense')) {
				self::process_new_expense();
			}

			if(self::is_create('Expense')) {
				self::process_create_expense();
			}

			if(self::is_list('Expense')) {
				self::process_list_expenses();
			}

			if(self::is_edit("Expense")) {
				self::process_edit_expense();
			}

			if(self::is_update("Expense")) {
				self::process_update_expense();
			}

			if(self::is_delete("Expense")) {
				self::process_delete_expense();
			}

			if(self::is_list('Person')) {
				self::process_list_people();
			}

			if(self::is_create('Person')) {
				self::process_create_person();
			}

			if(self::is_view('Person')) {
				self::process_view_person();
			}

			if(self::is_edit("Person")) {
				self::process_edit_person();
			}

			if(self::is_update("Person")) {
				self::process_update_person();
			}

			if(self::is_new("Cluster")) {
				self::process_new_cluster();
			}

			if(self::is_create("Cluster")) {
				self::process_create_cluster();
			}

			if(self::is_view("Cluster")) {
				self::process_view_cluster();
			}

			if(self::is_list("Cluster")) {
				self::process_list_clusters();
			}

			if(self::is_edit("Cluster")) {
				self::process_edit_cluster();
			}

			if(self::is_update("Cluster")) {
				self::process_update_cluster();
			}

			if(self::is_delete("Cluster")) {
				self::process_delete_cluster();
			}

			if(self::is_time_zone_set()) {
				self::process_time_zone_set();
			}

			if(self::is_currency_set()) {
				self::process_currency_set();
			}
		} 
	}

	/**
	  * Process: new income.
	  * @return void
	  */
	private static function process_new_income() {
		$cluster_options = HtmlBlock::cluster_options_for("Income");
		$date = array(date('Y', $_SERVER['REQUEST_TIME']), date('m', $_SERVER['REQUEST_TIME']), date('d', $_SERVER['REQUEST_TIME']));
		self::set_out('date', $date);
		self::set_out('cluster_options', $cluster_options);
	}
	/**
	  * Process: create income.
	  * @return void
	  */
	private static function process_create_income() {
		$i = new Income();
		$id = $_POST['income'];
		$i->ffde($id, Income::editable_fields());
		if(isset($id['on_month'], $id['on_day'], $id['on_year'])) {
			$i->on_ = Dbobj::toDate(mktime(0, 0, 0, $id['on_month'], $id['on_day'], $id['on_year']));
		} else {
			$i->on_ = Dbobj::toDate($_SERVER['REQUEST_TIME']);
		}
		$user = Login::user();
		$user->owns($i);

		if($validation = $i->hasValidationErrors()) {
			$_SESSION['income_validation'] = $validation;
			self::saveToSession('Income', $id);
			self::r2n("Income");
		} else {
			if($i->save()) {
				LoggerAction::action_success('create', $i);
				Logger::info(t("Created!"));
				self::r2l("Income");
			} else {
				LoggerAction::action_error('create', $i);
				self::error();
			}
		}
	}
	/**
	  * Process: list incomes.
	  * @return void
	  */
	private static function process_list_incomes() { 
		$user = Login::user();

		$wheres = array(
			sprintf( 'i.user_id = %d' , $user->id ));

		/** wheres */
		if($simple_filter = Session::g0d('filter_income', NULL)) {
			$wheres = $simple_filter->make_wheres($wheres);
		} else {
			$simple_filter = new SimpleFilter();
		} 
		self::set_out('simple_filter', $simple_filter);

		$filter = new SqlFilter("i.*, c.name as cluster_name");
		$filter->setFrom(Income::tableName()." i");
		$filter->setJoin("LEFT JOIN ".Cluster::tableName()." c");
		$filter->setOn("ON c.id = i.cluster_id");
		$filter->setWhere(join(' AND ',$wheres));
		$filter->setOrderBy("i.on_ DESC");

		$ipp = Session::g0d('items_per_page', Config::items_per_page());

		$incomes = new ObjSet('Income', $filter, self::get0('page'), $ipp);
		$incomes->loadNextPage();

		/** all clusters */
		$cf = new SqlFilter('c.id, c.name');
		$cf->setFrom(Cluster::tableName().' c');
		$cf->setJoin('JOIN '.Income::tableName().' i');
		$cf->setOn('ON c.id = i.cluster_id');
		$cf->setGroupBy('i.cluster_id');
		$cf->setWhere(join(' AND ', $wheres));
		$clusters = Cluster::find($cf);

		$cluster_options = array('' => NULL);
		foreach($clusters as $cluster) {
			$cluster_options[$cluster->id] = $cluster->name;
		}

		/** Clustered */
		$total_clustered = Income::total_grouped_by('cluster_id', 'amount', $filter);
		$total           = Income::total_on('amount', $filter);

		/** Chart */
		$chd = array();
		foreach($total_clustered as $tcl) {
			$chd[$tcl->cluster_name] = $tcl->asc('total')->as_f(); 
		}
		$chart = new ChartPie(400, 300, $chd);
		self::set_out('chart', $chart);

		self::set_out('incomes', $incomes);
		self::set_out('user', $user);
		self::set_out('total', $total);
		self::set_out('total_clustered', $total_clustered);
		self::set_out('cluster_options', $cluster_options);
	}
	/**
	 * Process: new expense.
	 * @return void
	 */
	private static function process_new_expense() {
		$cluster_options = HtmlBlock::cluster_options_for('Expense');
		$date = array(date('Y', $_SERVER['REQUEST_TIME']), date('m', $_SERVER['REQUEST_TIME']), date('d', $_SERVER['REQUEST_TIME']));
		self::set_out('date', $date);
		self::set_out('cluster_options', $cluster_options);
	}

	/**
	 * Process new cluster
	 * @return void
	 */
	private static function process_new_cluster() {
		$user = Login::user();
		$cluster = new Cluster();
		Access::owns($user, $cluster);
		Req::set_out('cluster', $cluster);
	}

	/**
	 * Process create cluster
	 * @return void
	 */
	private static function process_create_cluster() {
		$cluster = new Cluster();

		$cluster->ffde($_POST['cluster'], Cluster::editable_fields());
		$user = Login::user();
		$user->owns($cluster);

		if($validation = $cluster->hasValidationErrors()) {
			$_SESSION['cluster_validation'] = $validation;
			self::saveToSession('Cluster', $_POST['cluster']);
			Logger::undefErr(array_values($validation));
			self::r2n('Cluster');
		} else {
			if($cluster->save()) {
				LoggerAction::action_success('create', $cluster);
				Logger::info(t("Created!"));
				self::r2l('Cluster');
			} else {
				LoggerAction::action_error('create', $cluster);
				self::error();
			}
		}
	}

	/**
	 * Process view cluster.
	 * @return void
	 */
	public static function process_view_cluster() {
		if($cluster = Cluster::load($_GET['cluster'])) {
			$user = Login::user();
			if($user->can_view($cluster)) {
				Req::set_out('cluster', $cluster);
			} else {
				self::access_denied();
			}
		} else {
			self::error();
		}
	}

	/**
	 * Process list clusters
	 *
	 * @return void
	 */
	private static function process_list_clusters() {
		$user   = Login::user();
		$filter = new Filter("id, name");
		$filter->setFrom(array("Cluster" => "c"));
		$filter->setWhere(array(
			"Cluster.user_id" => $user->id
		));
		$filter->setOrderBy('name');
		$clusters = Cluster::find($filter);
		self::set_out('clusters', $clusters);
	}

	/**
	 * Process edit cluster
	 * @return void
	 */
	private static function process_edit_cluster() {
		if($cluster = Cluster::load($_GET['cluster'])) {
			$user = Login::user();
			if($user->is_owner_of($cluster)) {
				self::set_out('cluster', $cluster);
			} else {
				self::access_denied();
			}
		} else {
			self::error();
		}
	}
	/**
	 * Process: update cluster.
	 * @return void
	 */
	private static function process_update_cluster() {
		if($cluster = Cluster::load($_GET['cluster'])) {
			$user = Login::user();
			if($user->can_update($cluster)) {
				$cluster->ff($_POST['cluster'], Cluster::editable_fields());
				if($validation = $cluster->hasValidationErrors()) {
					self::saveToSession('Cluster', $_POST['cluster']);
					Logger::undefErr(array_values($validation));
					self::r2e($cluster);
				} else {
					$cluster->save();
					LoggerAction::action_success('update', $cluster);
					Logger::info(t("Updated!"));
					self::r2v($cluster);
				}
			} else {
				LoggerAction::action_access_denied('update', $cluster);
				self::access_denied();
			}
		} else {
			LoggerAction::action_error('update', $cluster);
			self::error();
		}
	}
}

