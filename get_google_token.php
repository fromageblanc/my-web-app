<?php
session_start();
require_once('const.php');
$code = (isset($_REQUEST["code"])) ? $_REQUEST["code"] : "";

  // current uri
  if (!strlen($_SERVER['HTTP_REFERER'])) {
  	$_SESSION['return_url'] = "http://" .$_SERVER['HTTP_HOST']. "/" ."index.html";
  } else {
  	$_SESSION['return_url'] = $_SERVER['HTTP_REFERER'];
  }

if(empty ($code))
{
    $dialog_url = "https://accounts.google.com/o/oauth2/auth?"
       . "scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&"
       . "client_id=" . GOOGLE_CLIENT_ID . "&redirect_uri=" . urlencode (GOOGLE_CALLBACK) . "&response_type=code";

    echo("<script> top.location.href='" . $dialog_url . "'</script>");
}
?>
