<? 
/**
 * Expense/income journal.
 *
 * @author Marius Žilėnas <mzilenas@gmail.com>
 * @copyright 2013 Marius Žilėnas
 *
 * @version 0.0.7
 */
include_once 'includes.php';
DB::connect();
session_start();

Req::process();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="media/css/style.css">
		<title>Expenses and Incomes</title>
	</head>
	<body>

<?= LoggerHtmlBlock::messages() ?>
<p class="meniu">
<? if(!Login::is_logged_in()) { ?>
	<a href="?login">Login</a>
	<? include_once 'auth/sub/login.sub.php'; ?>
<? } else { ?>
		<a href="?expenses&list">Expenses</a>
		<a href="?incomes&list">Incomes</a>
		<a href="?clusters&list">Clusters</a>
		<span class="small"><?= sprintf(t("You are logged in as: %s"), Html::a("?account&my", Login::user()->login)) ?></a></span>
		</p>
		<? include_once 'sub/expense.sub.php'; ?>
		<? include_once 'sub/income.sub.php'; ?>
		<? include_once 'sub/cluster.sub.php'; ?>
		<? include_once 'sub/person.sub.php'; ?>
		<? include_once 'auth/sub/account.sub.php' ?>
<? } ?>

<? include_once 'sub/pages.sub.php'; ?>

<p>&copy; 2013 <a href="mailto:mzilenas@gmail.com">Marius Žilėnas</a></p>
	</body>
</html>

