<?
if(Req::is_list('Person')) { ?>
<p><?= Html::a("?person&add", t("Add person")) ?></p>
<? if($people = Req::out('people')) { } ?>
<? } ?>

<? if(Req::is_new('Person')) { ?>
<? } ?>

<? if(Req::is_edit('Person') && $person = Req::out('person')) { ?>
<form method="post" action="">
	<?= Form::action_update() ?>
	<? include_once 'sub/person_form.sub.php'; ?><br />
	<?= Form::submit(t("Ok")) ?> <?= Html::a(Html::link_to_view($person), t("Cancel")) ?>
</form>
<? } ?>

<? if(Req::is_view('Person') && $person = Req::out('person')) { ?>
	<p>
		<?= Html::ab(t("Back")) ?>
		</p>
<p>
<?
	$user = Login::user();
	if($user->can_edit($person)) {
		echo Html::a(Html::link_to_edit($person), t("Edit"));
	} ?>
</p>
<?
	echo HtmlBlock::oastb($person, Person::editable_fields());
} ?>

