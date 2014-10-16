<table>
	<thead>
		<tr><th><?= t("Amount") ?><th><?= t("Description") ?><th><?= t("Cluster")?><th><?= t("On")?>
	</thead>
	<tbody>
		<tr>
			<td>
<?= Form::validation('income', 'amount') ?>
<?= Form::inputHtml("text", "income[amount]", $expense->amount, array('size' => 10)) ?>
			<td>
<?= Form::inputHtml("text", "income[description]", $expense->description) ?>
			<td>
<?= Form::select(
	'income[cluster_id]',
	Form::optionsA($cluster_options, $expense->cluster_id));?>
			<td>
<?= Form::inputHtml("text", "income[on_]", $expense->asDate('on_'), array('size' => '10')) ?>
	</tbody>
</table>
