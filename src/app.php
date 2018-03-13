<?php
require_once __DIR__ . '/../vendor/autoload.php';

$templatedir =  __DIR__.'/webapp/templates/';
$app = new \Slim\Slim([
    'debug' => true,
    'templates.path' => $templatedir,
    'view' => new \Slim\Views\Twig($templatedir
  )
]);
$view = $app->view();
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);
$view->parserOptions = array(
    'debug' => true
);

define('PATH', __DIR__);
define('DB_USER', "infosik");
define('DB_PW', "infosik"); //TODO change in prod
define('DB_DOMAIN', 'localhost');
define('DB_NAME', 'app');
define('DB_PATH', PATH . '/db/' . DB_NAME);

$app->db = \ttm4135\webapp\Sql::getDB();


$ns ='ttm4135\\webapp\\controllers\\';


/// app->(GET/POST) (URL, $ns . CONTROLLER);    // description..   <who has access>

$app->get('/',     $ns . 'HomeController:index');             //front page            <all site visitors>

$app->get('/admin', $ns . 'AdminController:index');        //admin overview        <staff and group members>

$app->get( '/login', $ns . 'LoginController:index');        //login form            <all site visitors>
$app->post('/login', $ns . 'LoginController:login');       //login action          <all site visitors>
$app->post('/logout',$ns . 'LoginController:logout');  //logs out    <all users>
$app->get('/logout', $ns . 'LoginController:logout');  //logs out    <all users>
$app->get( '/register', $ns . 'UserController:index');     //registration form     <all visitors with valid personal cert>
$app->post('/register', $ns . 'UserController:create');    //registration action   <all visitors with valid personal cert>
$app->get('/admin',  $ns  .  'AdminController:index');

$app->get('/admin/delete/:userid', $ns . 'UserController:delete');     //delete user userid        <staff and group members>

$app->post('/admin/deleteMultiple', $ns . 'UserController:deleteMultiple');     //delete user userid        <staff and group members>
$app->get('/admin/edit/:userid',    $ns . 'UserController:show');       //add user userid          <staff and group members>
$app->post('/admin/edit/:userid',   $ns . 'UserController:edit');       //add user userid          <staff and group members>

$app->get('/admin/create',    $ns . 'AdminController:create');       //add user userid          <staff and group members>
$app->post('/admin/create',   $ns . 'UserController:newuser');       //add user userid          <staff and group members>  //TODO FIX


return $app;
