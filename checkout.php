<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['authenticated']) || empty($_SESSION['user'])) {
  header('Location: index.php?loginRequired=1');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Checkout — NETH SHOP</title>
        <link href="./dist/styles.css" rel="stylesheet">
    </head>
    <body>

        <?php include 'header.php'; ?>

    <main class="container py-5 content-with-footer">
            <h2 class="mb-4">Checkout</h2>

            <div class="row">
                <!-- Items column (rendered from cart state) -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Items</h5>
                            <p class="text-muted">Review the items you'll purchase. Quantities are editable.</p>

                            <div id="checkoutItems" class="list-group">
                                <!-- rendered by javascript -->
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Summary column (values injected by JS) -->
                <div class="col-lg-4">
                    <div class="card card-summary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Order summary</h5>
                            <ul id="summaryList" class="list-unstyled mb-0"></ul>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <div>Subtotal</div>
                                <div id="subtotal">₱0</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>Shipping</div>
                                <div id="shippingFee">₱0</div>
                            </div>
                            <div class="d-flex justify-content-between fw-bold mt-2">
                                <div>Total</div>
                                <div id="total">₱0</div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Shipping address</h6>
                            <textarea id="shippingAddress" class="form-control mb-2" rows="3" placeholder="Full name, street, city, province, postal code"></textarea>
                            <a href="#" id="useCartAddress" class="btn btn-sm btn-link p-0">Use saved address</a>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Shipping method</h6>
                            <select id="shippingOption" class="form-select mb-2">
                                <option value="standard" data-fee="0">Standard (3-7 days) — ₱0</option>
                                <option value="express" data-fee="199">Express (1-2 days) — ₱199</option>
                                <option value="nextday" data-fee="399">Next day — ₱399</option>
                            </select>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Payment method</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="payment_card" value="card" checked>
                                <label class="form-check-label" for="payment_card">Card (Visa / Mastercard)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="payment_gcash" value="gcash">
                                <label class="form-check-label" for="payment_gcash">GCash / e-wallet</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="payment_cod" value="cod">
                                <label class="form-check-label" for="payment_cod">Cash on Delivery</label>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Order notes</h6>
                            <textarea id="orderNotes" class="form-control" rows="2" placeholder="Add a note (e.g., leave at reception)"></textarea>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button id="placeOrderBtn" class="btn btn-success btn-lg">Place order</button>
                    </div>
                    <div class="mt-2 text-center"><a href="index.php" class="btn btn-link">Continue shopping</a></div>
                </div>
            </div>

        </main>

        <?php include 'footer.php'; ?>

                <!-- Bootstrap JS for modal support -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

                <!-- Order success modal -->
                <div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-labelledby="orderSuccessModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered">
                        <div class="modal-content text-center p-3">
                            <div class="modal-body">
                                <div class="mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-success">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h5 id="orderSuccessModalLabel" class="mb-1">Order placed</h5>
                                <p id="orderSuccessMessage" class="small text-muted mb-1">Thank you — your order has been placed.</p>
                                <p id="orderSuccessOrderId" class="small text-muted mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="./javascript/checkout.js"></script>
        </body>
        </html>


