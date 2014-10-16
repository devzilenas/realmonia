<? if(Req::is_new('User')) {
	$user = new User(); ?>
<form action="?user" method="post">
	<?= Form::action("create") ?>

	<?= Form::validation("user_validation", "login") ?>
	<?= Form::label(t("Login"), "user_login") ?>
	<?= Form::inputHtml("text", "user[login]", so($user->login), array("id" => "user_login", "class" => "edit human")) ?><br />

	<?= Form::validation("user_validation", "password") ?>
	<?= Form::label(t("Password"), "user[password]") ?>
	<?= Form::inputHtml("password", "user[password]", "", array("class" => "edit password", "id" => "user[password]")) ?><br />

	<?= Form::label(t("Confirm password"), "user[password_confirm]") ?>
	<?= Form::inputHtml("password", "user[password_confirm]", "", array("class" => "edit password", "id" => "user[password_confirm]")) ?><br />

	<?= Form::validation("user_validation", "email") ?> 
	<?= Form::label(t("E-mail"), "user_email") ?>
	<?= Form::inputHtml("text", "user[email]", so($user->email), array("id" => "user_email", "class" => "edit mail")) ?><br />

	<?= Form::submit(t("Create your account"), array("class" => "submit")) ?> <?= Html::a('', t("Cancel")) ?>
</form>
<? } ?>

<? if(RequestAuth::is_login()) { ?>
<br />
<span>For demonstration purposes you can <form method="post" action="?user" class="inl">
	<?= Form::action("login") ?>
	<input type="hidden" name="user[login]" value="demo" />
	<input type="hidden" name="user[password]" value="demo" />
	<input type="submit" value="login as demo!" />
</form>
</span>
<span>For demonstration purposes you can <form method="post" action="?user" class="inl">
	<?= Form::action("login") ?>
	<input type="hidden" name="user[login]" value="demo2" />
	<input type="hidden" name="user[password]" value="d" />
	<input type="submit" value="login as demo2!" />
</form>
</span>
<? include 'login_form.sub.php' ?>
<? } ?>

