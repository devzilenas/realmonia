<table>
	<thead>
		<tr><th><?= t("Amount") ?><th><?= t("Description") ?><th><?= t("Cluster")?><th><?= t("On")?>
	</thead>
	<tbody>
		<tr>
			<td>
<?= Form::validation('income', 'amount') ?>
<?= Form::inputHtml("text", "income[amount]", $income->amount, array('size' => 10)) ?>
			<td>
<?= Form::inputHtml("text", "income[description]", $income->description) ?>
			<td>
<?= Form::select(
	'income[cluster_id]',
	Form::optionsA($cluster_options, $income->cluster_id));?>
			<td>
On <?= Form::inputHtml("text", "income[on_]", $income->asDate('on_'), array('size' => '10')) ?>
	</tbody>
</table>

