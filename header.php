<?php
require_once __DIR__ . '/api/Session.php';
$session = Session::getInstance();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">Neth shopping</a>
    <button class="navbar-toggler" type="button">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">🛒 Cart</a></li>
        <?php if ($session->isAuthenticated() && $session->getUser()): ?>
          <li class="nav-item d-flex align-items-center me-2">
            <span class="navbar-text small">Hello, <?php $user = $session->getUser(); echo htmlspecialchars($user['first_name'] ?? $user['username'] ?? 'User'); ?></span>
          </li>
          <li class="nav-item">
            <a class="btn btn-sm btn-outline-light nav-link px-3" href="api/logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <button class="btn btn-sm btn-outline-light nav-link" id="navLoginBtn" data-bs-toggle="modal" data-bs-target="#loginModal">LOGIN</button>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  
</nav>

