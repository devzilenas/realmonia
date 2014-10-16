<? if(Req::is_list('Cluster')) { ?> 
<p><?= Html::a(Html::link_to_new("Cluster"), t("New")) ?></p>

<?
if($clusters = Req::out('clusters')) {
	echo HtmlBlock::ial($clusters, 'Html::link_to_view');
} ?>

<? } ?>

<? if(Req::is_view('Cluster') && $cluster = Req::out('cluster')) { 
?>
<p>
<?= Html::a(Html::link_to_edit($cluster), t('Edit')) ?>
</p>
<?
	echo HtmlBlock::oastb($cluster, Cluster::showable_fields());
	echo Html::a(Html::link_to_list("Cluster"), t("Back"));
} ?>

<? if(Req::is_new('Cluster') && $cluster = Req::out('cluster')) { ?>
<form method="post" action="">
	<?= Form::action_create() ?>
	<? include_once 'sub/cluster_form.sub.php'; ?><br />
	<?= Form::submit(t("Ok")) ?><?= Html::a(Html::link_to_list("Cluster"), t("Cancel")) ?>
</form>

<? } ?>

<? if(Req::is_edit('Cluster') && $cluster = Req::out('cluster')) { ?>
<form method="post" action="">
	<?= Form::action_update() ?>
	<? include_once 'sub/cluster_form.sub.php'; ?><br />
	<?= Form::submit(t("Save")); ?><?= Html::a(Html::link_to_view($cluster), t("Cancel")) ?>
</form>
<? } ?>

