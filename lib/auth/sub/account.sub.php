<? if(Login::is_logged_in() && RequestAuth::is_account_my() && $user = Req::out('user')) { ?>
	<? include 'auth/sub/logout_form.sub.php' ?><br />

<? if($person = Person::load_by(sprintf('user_id = %d', $user->id))) { ?>
<p><?= Html::a(Html::link_to_view($person), t('Person information')) ?></p>
<? } ?>

<h3>Money</h3>
<? include_once 'auth/sub/monia.sub.php' ?>

<? } ?>

