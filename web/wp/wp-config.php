<?php
/**
 * La configuration de base de votre WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/Editing_wp-config.php Modifier
 * wp-config.php} (en anglais). Vous devez obtenir les codes MySQL de votre
 * hébergeur.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

define('ISWINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Le nom de la base de données de WordPress. */
switch($_SERVER['HTTP_HOST'])
{
  case "www.auberges.com":
      define('DB_NAME', 'aj_wp_main_fr');

    break;

	case "www.aubergesdejeunesse.com":
      define('DB_NAME', 'aj_wp_fr_aj');

    break;
  case "www.hostales.com":

      define('DB_NAME', 'aj_wp_ht_es');

    break;

  case "www.alberguesjuveniles.com":

      define('DB_NAME', 'aj_wp_es');

    break;

  case "www.youth-hostel.com":

      define('DB_NAME', 'aj_wp_en');

    break;

  case "www.alberguesdajuventude.com":

      define('DB_NAME', 'aj_wp_pt');

    break;

  case "www.ostellidellagioventu.com":

      define('DB_NAME', 'aj_wp_it');

    break;

  case "www.youth-hostels.jp":

      define('DB_NAME', 'aj_wp_ja');

    break;

  case "www.youth-hostels.kr":

      define('DB_NAME', 'aj_wp_ko');

    break;

  case "www.jugendherbergen.eu":

      define('DB_NAME', 'aj_wp_de');

    break;

		case "www.pousadasdejuventude.com":

      define('DB_NAME', 'aj_wp_pj_pt');

    break;

		case "www.alberguesjuveniles.es":

      define('DB_NAME', 'aj_wp_es_es');

    break;

		case "www.albergues-pensiones.com":

      define('DB_NAME', 'aj_wp_ap_es');

    break;

		case "www.hostels.in":

      define('DB_NAME', 'aj_wp_hi');

    break;

	case "www.youth-hostel.co.uk":

      define('DB_NAME', 'aj_wp_en_uk');

    break;

	case "www.youth-hostels.co.uk":

      define('DB_NAME', 'aj_wp_en_uk');

    break;

	case "www.youth-hostel.hk":

      define('DB_NAME', 'aj_wp_zh');

    break;

	case "www.youth-hostels.hk":

      define('DB_NAME', 'aj_wp_zh');

    break;

	case "www.hostele.com":

      define('DB_NAME', 'aj_wp_pl');

    break;

		case "www.youth-hostels.ru":

      define('DB_NAME', 'aj_wp_ru_yh');

    break;

	case "www.schroniskamlodziezowe.com":

      define('DB_NAME', 'aj_wp_pl_sm');

    break;

	case "www.youth-hostels.ca":

      define('DB_NAME', 'aj_wp_en_ca');

    break;

	case "www.aubergesdejeunesse.ca":

      define('DB_NAME', 'aj_wp_fr_ca');

    break;

	case "www.hostelek.com":

      define('DB_NAME', 'aj_wp_hu');

    break;

	case "www.ifjusagiszallasok.com":

      define('DB_NAME', 'aj_wp_hu_if');

    break;

	case "www.hostels.ru.com":

      define('DB_NAME', 'aj_wp_ru');

    break;

	case "www.retkeilymajoja.com":

      define('DB_NAME', 'aj_wp_fi');

    break;

	case "www.hostelleja.com":

      define('DB_NAME', 'aj_wp_fi_ho');

    break;

	case "www.hostely.com":

      define('DB_NAME', 'aj_wp_cs');

    break;

	case "www.mladeznickeubytovny.com":

      define('DB_NAME', 'aj_wp_cs_ml');

    break;

	case "www.herbergen.com":

      define('DB_NAME', 'aj_wp_de_he');

    break;

	case "www.xn--e1amhmfp1c.xn--p1ai":

		define('DB_NAME', 'aj_wp_ru_xo');

    break;

	case "www.hosteis.com":

      define('DB_NAME', 'aj_wp_pt_ho');

    break;

	case "www.hostelli.com":

      define('DB_NAME', 'aj_wp_it_ho');

    break;

	case "www.youth-hostels.ie":

      define('DB_NAME', 'aj_wp_en_ie');

    break;

	case "www.youth-hostels.co.nz":

      define('DB_NAME', 'aj_wp_en_nz');

    break;

	case "www.youth-hostels.eu":

      define('DB_NAME', 'aj_wp_en_eu');

    break;

	case "www.youth-hostels.asia":

      define('DB_NAME', 'aj_wp_en_asia');

    break;

	case "www.youth-hostels.cn":

      define('DB_NAME', 'aj_wp_zh_cn');

    break;

	case "www.hostels.jp":

      define('DB_NAME', 'aj_wp_ja_ho');

    break;

	case "www.hostels.mobi":

      define('DB_NAME', 'aj_wp_en_mobi');

    break;

	case "www.nofeehostels.com":

      define('DB_NAME', 'aj_wp_en_nf');

    break;

	case "www.nofeeshostels.com":

      define('DB_NAME', 'aj_wp_en_nf');

    break;

	case "www.xn--xn2by4qtje86kn5ezmb.kr":

		define('DB_NAME', 'aj_wp_ko_ho');

    break;

        case "www.hbsitetest.com":

      define('DB_NAME', 'aj_wp_hb');

    break;

        case "www.hwsitetest.com":

      define('DB_NAME', 'aj_wp_hw');

    break;


}

if (ISWINDOWS) {
	$username = "dev_aj_site";
	$password = "data2016";
	$DBHostname = "127.0.0.1:4040";
}
else
{
	$username = "aj_site";
	$password = "2bVHhwjCGQrRnGW2";
	$DBHostname = "92.243.25.30";
}


define('DB_NAME_AUBERGE', 'aj_ci');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', $username);

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', $password);

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', $DBHostname);

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Le type de collabtion de la base de données.
* N'y touchez qui si vous savez ce que vous faites.
*/
define('DB_COLLATE', '');

  /** Chemin absolu vers le dossier de CI. */
/* Valero chat globale variable set false for disable and True for enable chat*/
<<<<<<< HEAD
=======
define('DISPLAY_VELARO',FALSE);
>>>>>>> origin/MCWEB-87

/**#@+
 * Clefs uniques d'authentification.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/ Le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'C-_B(habIfp(2:Fl:8*z.^FlxRg*CBF zMV.4h--dR!o|#WE=H[-Z(b(+pbI>rcl');
define('SECURE_AUTH_KEY',  '&Q[:E  ^ofG@(4|a#RaP^Q4}d-@bB+nPD(cBtIGLbqb]+_ikw(5[`f+A`PMl|IGo');
define('LOGGED_IN_KEY',    'Dha/(H=m8Up:W1P*8kbiD i>SKC3%Gvo.9 `-~&na&XSerg7+Z;I#X(s6pkUg{+*');
define('NONCE_KEY',        'NiaG+0hM01Yj?#0#98|Vczg|&}>GrkX)O.+;MpuDMl?,T><,,*]`*kp]FK|`_5.*');
define('AUTH_SALT',        't-2&p;AAaP3JXsE*D4 i2udj4|2xPh)YZG/8#CZW{3P`I[>y+/|lxVV-F}n.*!91');
define('SECURE_AUTH_SALT', 'z8d<)-Vmy2Ye_UGDqHIQd2grVQqYOy+UC>^A0v,E[%C6:i]nQE:%iw(ZM3!=w;4)');
define('LOGGED_IN_SALT',   '1uK-g]%b^kaZ?k/ztQnNCuDIDW+}Ae_G5p+N!7*|4)n_pL`@x/O7k.*r<ovL%[?m');
define('NONCE_SALT',       '5,;py&`LpczunDm%3|Ui(L7Y#5*RXxqGM>n[C-W_r`^Y-3XGJ=A;IUU*p2~_]DZ)');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/**
 * Langue de localisation de WordPress, par défaut en Anglais.
 *
 * Modifiez cette valeur pour localiser WordPress. Un fichier MO correspondant
 * au langage choisi doit être installé dans le dossier wp-content/languages.
 * Par exemple, pour mettre en place une traduction française, mettez le fichier
 * fr_FR.mo dans wp-content/languages, et réglez l'option ci-dessous à "fr_FR".
 */
define ('WPLANG', 'en_US');

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if (ISWINDOWS)
{
	define('ABSPATH', dirname(__FILE__) . '/');
	define('CI_ABSPATH', 'c:/GitHub/source/web/ci/');
	define('CI_APPPATH', 'c:/GitHub/source/code/application/');
	define('CI_LANGPATH', 'c:/GitHub/source/languages/wp/');}
else
{
	define('ABSPATH', dirname(__FILE__) . '/');
	define('CI_ABSPATH', '/opt/web/ci/');
	define('CI_APPPATH', '/opt/code/application/');
	define('CI_LANGPATH', '/opt/languages/wp/');
}

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
