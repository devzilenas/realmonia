<?
set_include_path(get_include_path() . PATH_SEPARATOR . 'class');
set_include_path(get_include_path() . PATH_SEPARATOR . 'lib');

# -------------- CONFIG -------------------
include 'config.inc.php'                   ;

# -----------------------------------------
# -------------- LIB ----------------------
# -----------------------------------------
# -------------- DB -----------------------
include_once 'db/db.class.php'; 
# -------------- DBOBJS -------------------
include_once 'dbobjs/dbobjs.include.php';
# -------------- CALENDAR -----------------
include_once 'calendar/calendar.include.php';
# -------------- CHART --------------------
include_once 'chart/chart.include.php'     ;
# -------------- REQUEST ------------------
include_once 'sys/request.class.php'       ;
include_once 'sys/req.interface.php';
# -------------- MONEY --------------------
include_once 'money/money.include.php'              ;
# -------------- WALLET -------------------
include_once 'money/wallet/wallet.include.php';
# -------------- ITEMS LIST ---------------
include_once 'dbobjs/html/items_list.html.php';
include_once 'dbobjs/req/list.req.php'     ;
# -------------- RANKS --------------------
include_once 'dbobjs/rank/rankenstein.class.php';
# -------------- LANGUAGE ----------------- 
include_once 'lang/language.class.php';
include_once 'lang/dict/lt.inc.php';
include_once 'lang/dict/ru.inc.php';
include_once 'lang/dict/de.inc.php'; 
# -------------- LOGGER ------------------- 
include_once 'sys/logger/logger.include.php'      ;
# -------------- HTML ---------------------
include_once 'html/form.class.php'         ;
include_once 'html/html.class.php'         ;
# -------------- SESSION ------------------
include_once 'sys/session.class.php'       ;
include_once 'sys/session.inc.php'         ;
# -------------- MAIL ---------------------
include_once 'mail/mail.include.php'       ;
# -------------- USER ---------------------
include_once 'auth/auth.include.php'       ;

include_once 'lib.inc.php'                 ;

# -----------------------------------------
# -------------- SETUP --------------------
# -----------------------------------------
$DBOBJS = array("Person", "Expense", 'Income', 'Cluster');
foreach($DBOBJS as $name) include 'dbobjs/'.strtolower(c2u($name)).'.class.php';

# -------------- REQUEST ------------------
include 'req.class.php'                    ;
# -------------- HTML ---------------------
include 'html_block.class.php'             ;

