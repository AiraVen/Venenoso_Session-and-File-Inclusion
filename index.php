<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Shop - Bootstrap</title>

  <!-- Bootstrap CSS (comment out to see plain HTML) -->
  <link href="./dist/styles.css" rel="stylesheet">
  <!-- modal styles moved to styles.css -->
<body>

  <!-- NAVBAR -->
  <?php include 'header.php'; ?>


  <!-- HERO -->
  <header class="site-hero text-center text-white py-5">
    <div class="container">
      <h1 class="display-4">Welcome to Neth Shop</h1>
      <p class="lead">Discover premium products for your business and lifestyle</p>
    </div>
  </header>

  <!-- PRODUCTS -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Featured Products</h2>
      <?php
        require_once __DIR__ . '/api/db.php';
        try {
          $pdo = get_pdo();
          $stmt = $pdo->query('SELECT product_id, product_name, description, price, image_path FROM products ORDER BY date_added DESC');
          $products = $stmt->fetchAll();
        } catch (Throwable $e) {
          $products = [];
        }
      ?>
      <div id="productsContainer" class="row g-4">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $p): ?>
            <div class="col-md-4">
              <div class="card h-100">
                <?php $img = !empty($p['image_path']) ? htmlspecialchars($p['image_path']) : ''; ?>
                <img src="<?php echo $img; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($p['product_name']); ?>">
                <div class="card-body p-4">
                  <div class="text-center">
                    <h5 class="card-title fw-bolder"><?php echo htmlspecialchars($p['product_name']); ?></h5>
                    <p class="card-text">₱<?php echo number_format((float)$p['price'], 0, '.', ','); ?></p>
                    <p class="card-description small"><?php echo htmlspecialchars($p['description']); ?></p>
                  </div>
                </div>
                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                  <div class="text-center">
                    <a href="#" class="btn btn-outline-secondary view-product" data-product-id="<?php echo (int)$p['product_id']; ?>">View</a>
                    <a href="#" class="btn btn-primary add-to-cart" data-product-id="<?php echo (int)$p['product_id']; ?>">Add to Cart</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-info">No products available.</div>
          </div>
        <?php endif; ?>
      </div>
        <!-- LOGIN SUCCESS MODAL -->
        <div class="modal fade" id="loginSuccessModal" tabindex="-1" aria-labelledby="loginSuccessModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header border-0">
                <h5 class="modal-title" id="loginSuccessModalLabel">Signed in</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p class="mb-0">You are now signed in.</p>
              </div>
              <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Continue</button>
              </div>
            </div>
          </div>
        </div>
            <!-- SIGNUP SUCCESS MODAL -->
            <div class="modal fade" id="signupSuccessModal" tabindex="-1" aria-labelledby="signupSuccessModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header border-0">
                    <h5 class="modal-title" id="signupSuccessModalLabel">Account created</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p class="mb-0">Your account was created and you are now signed in.</p>
                  </div>
                  <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Continue</button>
                  </div>
                </div>
              </div>
            </div>
      </div>
    </div>
    
     


  </section>

  <!-- FOOTER -->
  <?php include 'footer.php'; ?>

</body>
</html>

<!-- CONTACT MODAL -->
<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="loginModalLabel">Sign in to NETH SHOP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="loginForm">
          <div class="mb-3">
            <label for="loginUsername" class="form-label">Username</label>
            <input type="text" class="form-control" id="loginUsername" name="username" autocomplete="username" required>
          </div>
          <div class="mb-3">
            <label for="loginPassword" class="form-label">Password</label>
            <input type="password" class="form-control" id="loginPassword" name="password" autocomplete="current-password" required>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-between align-items-center">
        <div>
          <a href="#" class="small modal-note" data-bs-toggle="modal" data-bs-target="#signupModal">No account yet?</a>
        </div>
        <div>
          <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
          <button type="submit" form="loginForm" class="btn btn-primary">Login</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- SIGNUP MODAL (demo) -->
<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="signupModalLabel">Create an account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="signupForm">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="signupFirstName" class="form-label">First name</label>
              <input type="text" class="form-control" id="signupFirstName" name="firstName" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="signupLastName" class="form-label">Last name</label>
              <input type="text" class="form-control" id="signupLastName" name="lastName" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="signupAddress" class="form-label">Address (City / Province / Barangay)</label>
            <input type="text" class="form-control" id="signupAddress" name="address" placeholder="City / Barangay / Province" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="signupEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="signupEmail" name="email" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="signupContact" class="form-label">Contact</label>
              <input type="tel" class="form-control" id="signupContact" name="contact" placeholder="0917-xxx-xxxx" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="signupPassword" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="signupPassword" name="password" required aria-describedby="signupPasswordToggle">
                <button class="btn btn-outline-secondary password-toggle" type="button" id="signupPasswordToggle" data-target="signupPassword" aria-label="Show password">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="signupPasswordConfirm" class="form-label">Retype password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="signupPasswordConfirm" name="passwordConfirm" required aria-describedby="signupPasswordConfirmToggle">
                <button class="btn btn-outline-secondary password-toggle" type="button" id="signupPasswordConfirmToggle" data-target="signupPasswordConfirm" aria-label="Show password">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
              </div>
            </div>
          </div>
            <div class="mb-3">
              <label for="passwordStrength" class="form-label">Password Strength</label>
              <div id="passwordStrength" class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
        </form>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-between align-items-center">
        <div class="small text-muted">By creating an account you agree to our terms.</div>
        <div>
          <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
          <button type="submit" form="signupForm" class="btn btn-primary">Create account</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- login-ui is loaded below with other scripts -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contactModalLabel">Contact Us</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="contactForm">
          <div class="mb-3">
            <label for="contactName" class="form-label">Name</label>
            <input type="text" class="form-control" id="contactName" name="name" required>
          </div>
          <div class="mb-3">
            <label for="contactEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="contactEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="contactMessage" class="form-label">Message</label>
            <textarea class="form-control" id="contactMessage" name="message" rows="4" required></textarea>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- comfirmation modal -->
<!-- jQuery and jQuery Validation -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.21.0/dist/jquery.validate.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<div class="modal fade" id="contactSuccessModal" tabindex="-1" aria-labelledby="contactSuccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content text-center p-3">
      <div class="modal-body">
        <div class="mb-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-success">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h5 id="contactSuccessModalLabel" class="mb-1">Message sent</h5>
        <p class="small text-muted mb-0">Thanks — we will get back to you shortly.</p>
      </div>
    </div>
  </div>
</div>





<script src="./javascript/toast.js"></script>
<script src="./javascript/contact-modal.js"></script>
<script src="./javascript/cart.js"></script>
<script src="./javascript/products.js"></script>
<script src="./javascript/login-ui.js"></script>
<script>
  (function(){
    try {
      var params = new URLSearchParams(window.location.search);
      if (params.get('loginRequired') === '1') {
        // open login modal
        var modalEl = document.getElementById('loginModal');
        if (modalEl && window.bootstrap) {
          var modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
          modal.show();
        }
        // clean the query param without reloading
        params.delete('loginRequired');
        var newUrl = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '') + window.location.hash;
        window.history.replaceState({}, '', newUrl);
      }
    } catch(e) {}
  })();
</script>



<!-- Product view modal (used by main product grid) -->
<div class="modal fade" id="productViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><img id="pvImage" src="" alt="" class="img-fluid"></div>
          <div class="col-md-6">
            <h4 id="pvTitle"></h4>
            <p id="pvPrice" class="lead"></p>
            <p id="pvDesc"></p>
            <div class="d-flex gap-2 mt-3">
              <button id="pvAddBtn" class="btn btn-primary">Add to Cart</button>
              <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>