<?php
require_once __DIR__ . '/api/Session.php';
$session = Session::getInstance();

if (!$session->isAuthenticated() || !$session->getUser()) {
  header('Location: index.php?loginRequired=1');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart — NETH SHOP</title>
  <link href="./dist/styles.css" rel="stylesheet">
</head>
<body>

  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Cart content -->
  <main class="container py-5 content-with-footer">
    <div class="row">

      <!-- Items list -->
      <div class="col-lg-8 mb-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Shopping Cart</h4>
            <p class="text-muted">Review your items.</p>

            <div id="cartItems" class="list-group">
              <!-- cart items render here by javascript -->
            </div>

            <div class="d-flex justify-content-start align-items-center mt-4">
              <a id="continueShoppingBtn" href="index.php" class="btn btn-sm btn-outline-secondary btn-continue">← Continue shopping</a>
            </div>

          </div>
        </div>
      </div>

      <!-- Summary -->
      <div class="col-lg-4">
        <div class="card card-summary">
          <div class="card-body">
            <h5 class="card-title">Order Summary</h5>
            <ul id="summaryList" class="list-unstyled mb-3">
              <!-- summary lines injected by javascript -->
            </ul>
            <div class="d-flex justify-content-between mb-2">
              <span>Subtotal</span>
              <strong id="cartSubtotal">₱0</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
              <span>Shipping</span>
              <span class="muted">Calculated at checkout</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-3">
              <span class="h6">Total</span>
              <strong id="cartTotal" class="h5">₱0</strong>
            </div>

            <a id="checkoutSummaryBtn" href="checkout.php" class="btn btn-success w-100">Checkout</a>
          </div>
        </div>
      </div>

    </div>
  </main>

  <?php include 'footer.php'; ?>

  <!-- Bootstrap JS (for possible modal/tooltips) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <script>
    // wire continue shopping to go back when possible
    (function(){
      var btn = document.getElementById('continueShoppingBtn');
      if (!btn) return;
      btn.addEventListener('click', function(e){
        // prefer history back so user returns to previous listing
        if (window.history && window.history.length > 1) { e.preventDefault(); window.history.back(); }
      });

      // block navigation on disabled checkout (extra safety)
      var checkout = document.getElementById('checkoutSummaryBtn');
      if (checkout) {
        checkout.addEventListener('click', function(e){ if (checkout.classList.contains('disabled') || checkout.getAttribute('aria-disabled') === 'true') { e.preventDefault(); if (window.Toast && window.Toast.show) window.Toast.show('Your cart is empty.'); else try{ alert('Your cart is empty.'); }catch(e){} } });
      }
    })();
  </script>

  <script src="./javascript/cart.js"></script>

</body>
</html>


