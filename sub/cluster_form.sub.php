<?= Form::tf($cluster, 'name') ?>

<? if(Req::is_new('Cluster')) { ?>
for <?= Form::select('cluster[attached_to]', Form::options(array('Expense', 'Income'), $cluster->attached_to)) ?>
<? } ?>
