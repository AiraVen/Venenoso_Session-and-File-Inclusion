<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - NETH SHOP</title>

  <link href="./dist/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <?php include 'header.php'; ?>

  <section class="container py-5">
    <h2 class="text-center mb-4">Contact Us</h2>
    <div class="row">
      <div class="col-md-6">
        <form id="contactForm">
          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Your name" required minlength="2">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required>
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea id="message" name="message" rows="4" class="form-control" placeholder="Write your message" required minlength="10"></textarea>
          </div>
          <button type="submit" class="btn btn-success">Send Message</button>
        </form>
      </div>
      <div class="col-md-6">
        <h5>Visit Us</h5>
        <p>123 Main Street, Manila, Philippines</p>
        <h5>Call Us</h5>
        <p>+63 912 302 4591</p>
        <h5>Email</h5>
        <p>nethshop@yahoo.com</p>
      </div>
    </div>
  </section>

  <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1055">
    <div id="successToast" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          Message sent successfully! We'll get back to you soon.
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; 2024 Daisy Daze. All rights reserved.</p>
  </footer>

  
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
  <script>
    $(document).ready(function () {
      $.validator.addMethod("noSpace", function(value, element) { return value.trim().length > 0; }, "This field cannot be empty or spaces only.");
      $.validator.addMethod("emailcheck", function(value, element) {
        return /^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/.test(value);
      }, "Please enter a valid email address.");

      $("#contactForm").validate({
        rules: {
          name: { required: true, minlength: 2, noSpace: true },
          email: { required: true, email: true, noSpace: true, emailcheck: true },
          message: { required: true, minlength: 10, noSpace: true }
        },
        messages: {
          name: { required: "Please enter your name", minlength: "Name must be at least 2 characters" },
          email: { required: "Please enter your email", email: "Please enter a valid email", emailcheck: "Please enter a valid email" },
          message: { required: "Please enter your message", minlength: "Message must be at least 10 characters" }
        },
        errorClass: "text-danger",
        errorElement: "div",
        submitHandler: function(form) {
          form.reset();
          const toast = new bootstrap.Toast($("#successToast")[0], { autohide: true });
          toast.show();
          return false;
        }
      });
    });
  </script>
  <script src="./js/auth.js"></script>

</body>
</html>
