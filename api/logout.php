<?php
require_once __DIR__ . '/Session.php';

$session = Session::getInstance();
$session->logout();

?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signing out…</title>
  <meta http-equiv="refresh" content="3;url=../index.php">
</head>
<body>
  <script>
    try { localStorage.removeItem('nethshop_session'); } catch (e) {}
    try { window.location.replace('../index.php'); } catch (e) { window.location.href = '../index.php'; }
  </script>
  <noscript>
    <p>You have been signed out. <a href="../index.php">Continue</a></p>
  </noscript>
</body>
</html>


