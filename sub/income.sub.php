<? if(Req::is_list("Income")) {
	$incomes         = Req::out('incomes');
	$total           = Req::out('total');
	$total_clustered = Req::out('total_clustered');
	$cluster_options = Req::out('cluster_options');
	$simple_filter   = Req::out('simple_filter'); 
	$chart           = Req::out('chart');
	$chart->set_title(t("Incomes"));
	$currency        = new Currency(Login::user()->currency);
	?>
<p>
	<?= Html::a(Html::link_to_new("Income"), t("New")) ?>
</p>

<p>
Incomes total: <?= $total->converted_to($currency); ?>
<table>
	<caption>Incomes clustered</caption>
	<thead>
		<tr>
			<th>Amount
			<th>Cluster
	</thead>
	<tbody>
<? foreach($total_clustered as $tc) { ?>
		<tr>
			<td><?= $tc->asm('total', $currency) ?>
			<td><?= so($tc->cluster_name) ?>
<? } ?>
</tbody>
</table>
</p>
<? if(!$chart->is_empty()) { ?>
	<img src="data:image/png;base64,<?= $chart->draw() ?>" />
<? } ?>

<p> <?= ObjSetHtml::makeListHeader($incomes, "?incomes&list") ?> </p>
<table>
	<caption>Incomes</caption>
	<thead>
		<tr>
			<th>Action
			<th>Amount
			<th>Date
			<th>Description
			<th>Cluster
	</thead>
	<tbody>
		<? while($i = $incomes->getNextObj()) { ?>
		<tr>
			<td><?= Html::ai(Html::link_to_edit($i), 'media/img/edit.png', t("Edit")) ?>
			<td><?= $i->asm('amount', $currency); ?>
			<td><?= Dbobj::toDate($i->astm('on_')) ?>
			<td><?= so($i->description) ?>
			<td><?= so($i->cluster_name) ?>
		<? } ?>
	</tbody>
</table>

<p>
Filter <span class="small">(for example year:<?= date("Y") ?>, month:<?= date("m") ?> )</span>
<form method="post" action="">
	<?= Form::action('set_filter') ?>
Day
<?= Form::select('filter[income][on_][days]', Form::options_for_day($simple_filter->get('on_', 'days'))); ?>
	Month
<?= Form::select('filter[income][on_][months]', Form::options_for_month($simple_filter->get('on_', 'months'))); ?>
Year
	<input type="text" name="filter[income][on_][years]" size="4" value="<?= $simple_filter->get("on_", 'years'); ?>"><br />
<?= Form::label_for("select_cluster_id", t("Cluster")) ?>
	<?= Form::select('filter[income][cluster_id]', Form::optionsA($cluster_options, $simple_filter->get('cluster_id'))) ?>
	<?= Form::submit(t("Apply filter")) ?>
</form>
<form method="post" action="">
	<?= Form::action('reset_filter'); ?>
	<?= Form::hiddenInput("filter_for", "income"); ?>
	<?= Form::submit(t("No filter")) ?>
</form>
</p>

<? } ?>

<? if(Req::is_new("Income")) { 
	$cluster_options = Req::out('cluster_options');
	list($year, $month, $day) = Req::out('date');
	?>
<p>
<?= Html::a(Html::link_to_list("Income"), t("Back to list")) ?>
</p>
<form method="post" action="">
	<?= Form::action_create() ?> 
<table>
	<thead>
		<tr><th><?= t("Amount") ?><th><?= t("Description") ?><th><?= t("Cluster")?><th><?= t("On")?>
	</thead>
	<tbody>
		<tr>
			<td>
<?= Form::validation('income', 'amount') ?>
<?= Form::inputHtml("text", "income[amount]", '', array('size' => 10)) ?>
			<td>
<?= Form::inputHtml("text", "income[description]") ?>
			<td>
<?= Form::select(
	'income[cluster_id]',
	Form::optionsA($cluster_options));?>
			<td>
Day <?= Form::inputHtml("text", "income[on_day]", $day, array('size' => '2')) ?>
Month <?= Form::inputHtml("text", "income[on_month]", $month, array('size' => 2)) ?>
Year <?= Form::inputHtml("text", "income[on_year]", $year, array('size' => 4)) ?>
	</tbody>
</table>
	<?= Form::submit(t("Save")) ?><?= Html::a(Html::link_to_list('Income'), t("Cancel")) ?>
</form>
<? } ?>

<? if(Req::is_edit("Income")) { 
	$cluster_options = Req::out('cluster_options');
	$income = Req::out('income');
	?>
<form method="post" action="">
	<?= Form::action_update() ?>
	<? include_once 'sub/income_form.sub.php'; ?><br />
	<?= Form::submit(t("Save")) ?> <?= Html::a(Html::link_to_list("Income"), t("Cancel")) ?>
</form>
<? } ?>
