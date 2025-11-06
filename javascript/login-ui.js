// login-ui.js - handles the login & signup modal behavior with jQuery validation
(function($){
  'use strict';

  // helper: safely get jQuery and return early if missing
  if (typeof $ === 'undefined') return;

  
  function showSuccessModalAndRedirect(elOrId){
    var $successEl = (typeof elOrId === 'string') ? $('#' + elOrId) : $(elOrId);
    if (!$successEl || !$successEl.length) return;
    var successEl = $successEl[0];
    var successModal = (typeof bootstrap !== 'undefined') ? (bootstrap.Modal.getInstance(successEl) || new bootstrap.Modal(successEl)) : null;
    if (!successModal) return;
    successModal.show();
    setTimeout(function(){
      try{ successModal.hide(); } catch(e){}
      try{ window.location.reload(); } catch(e){}
    }, 1800);
  }

  // Custom validation methods for jQuery validation
  $.validator.addMethod("philippineMobile", function(value, element) {
    var digits = value.replace(/\D/g, '');
    return this.optional(element) || /^09\d{9}$/.test(digits);
  }, "Please enter a valid Philippine mobile number (11 digits, e.g. 09171234567)");

  $.validator.addMethod("nameFormat", function(value, element) {
    var nameRe = /^[A-Za-zÀ-ÖØ-öø-ÿ'\- ]{1,60}$/;
    return this.optional(element) || nameRe.test(value);
  }, "Please enter a valid name (letters, spaces, hyphens, and apostrophes only)");

  $.validator.addMethod("passwordStrength", function(value, element) {
    var score = 0;
    if (!value) return false;
    if (value.length >= 8) score += 25;
    if (/[A-Z]/.test(value)) score += 20;
    if (/[0-9]/.test(value)) score += 20;
    if (/[^A-Za-z0-9]/.test(value)) score += 20;
    if (value.length >= 12) score += 15;
    return score >= 40; // Minimum acceptable strength
  }, "Password must be at least 8 characters with uppercase, lowercase, number, and special character");

  // ------------------ LOGIN ------------------
  $(function(){
    var $loginModal = $('#loginModal');
    if (!$loginModal.length) return;

    $loginModal.on('shown.bs.modal', function(){
      var $u = $('#loginUsername'); if ($u.length) $u.trigger('focus');
    });

    var $form = $('#loginForm');
    if (!$form.length) return;

    // Initialize jQuery validation for login form
    $form.validate({
      rules: {
        username: {
          required: true,
          email: true
        },
        password: {
          required: true,
          minlength: 6
        }
      },
      messages: {
        username: {
          required: "Please enter your email",
          email: "Please enter a valid email address"
        },
        password: {
          required: "Please enter your password",
          minlength: "Password must be at least 6 characters"
        }
      },
      errorElement: 'div',
      errorClass: 'invalid-feedback',
      validClass: 'is-valid',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback d-block');
        element.addClass('is-invalid');
        // Prefer appending to the nearest field wrapper to avoid collapsing inputs
        var $wrapper = element.closest('.mb-3, .col-md-6, .form-group');
        if ($wrapper.length) {
          $wrapper.append(error);
        } else {
          var $group = element.closest('.input-group');
          if ($group.length) $group.after(error); else element.after(error);
        }
      },
      highlight: function(element){
        $(element).addClass('is-invalid').removeClass('is-valid');
      },
      unhighlight: function(element){
        $(element).removeClass('is-invalid').addClass('is-valid');
      },
      success: function(label, element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
        label.remove();
      },
      submitHandler: function(form) {
        var email = $.trim($form.find('[name="username"]').val() || '');
        var password = $form.find('[name="password"]').val() || '';

        // AJAX login
        $.ajax({
          url: 'api/login.php',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify({ email: email, password: password })
        }).done(function(json){
          // success
          var modal = $loginModal[0];
          try{ var bs = bootstrap.Modal.getInstance(modal); if (bs) bs.hide(); }catch(e){}
          try { localStorage.setItem('nethshop_session', JSON.stringify({ user: json.user, ts: Date.now() })); } catch(e){}
          showSuccessModalAndRedirect('loginSuccessModal');
          $form[0].reset();
          $form.find('.is-valid').removeClass('is-valid');
        }).fail(function(xhr){
          console.error('Login failed', xhr);
          alert('Login failed: ' + (xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Server error'));
        });
        return false; // Prevent default form submission
      }
    });
  });

  // ------------------ SIGNUP ------------------
  $(function(){
    var $signupModal = $('#signupModal');
    if (!$signupModal.length) return;
    $signupModal.on('shown.bs.modal', function(){ var $f = $('#signupFirstName'); if ($f.length) $f.trigger('focus'); });

    var $sform = $('#signupForm');
    if (!$sform.length) return;

    function gradePassword(pw){
      var score = 0; if (!pw) return 0; if (pw.length >= 8) score += 25; if (/[A-Z]/.test(pw)) score += 20; if (/[0-9]/.test(pw)) score += 20; if (/[^A-Za-z0-9]/.test(pw)) score += 20; if (pw.length >= 12) score += 15; return Math.min(100, score);
    }

    var $pwInput = $('#signupPassword');
    var $pwBar = $('#passwordStrength .progress-bar');
    if ($pwInput.length && $pwBar.length){
      $pwInput.on('input', function(){
        var v = gradePassword($pwInput.val());
        $pwBar.css('width', v + '%').attr('aria-valuenow', v).removeClass('bg-danger bg-warning bg-success');
        if (v < 40) $pwBar.addClass('bg-danger'); else if (v < 75) $pwBar.addClass('bg-warning'); else $pwBar.addClass('bg-success');
      });
    }

    // Force digits only
    var $contactInput = $('#signupContact');
    if ($contactInput.length){ $contactInput.on('input', function(){ var d = $(this).val().replace(/\D/g,''); $(this).val(d); }); }

    // Password toggle
    function togglePasswordField($button){ if (!$button || !$button.length) return; var targetId = $button.data('target'); if (!targetId) return; var $input = $('#' + targetId); if (!$input.length) return; if ($input.attr('type') === 'password'){ $input.attr('type','text'); $button.attr('aria-label','Hide password'); $button.html('<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a20.3 20.3 0 0 1 5.06-5.94"/><path d="M1 1l22 22"/></svg>'); } else { $input.attr('type','password'); $button.attr('aria-label','Show password'); $button.html('<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>'); } }

    $('.password-toggle').off('click').on('click', function(e){ e.preventDefault(); togglePasswordField($(this)); });

    // Initialize jQuery validation for signup form
    $sform.validate({
      rules: {
        firstName: {
          required: true,
          nameFormat: true,
          minlength: 2
        },
        lastName: {
          required: true,
          nameFormat: true,
          minlength: 2
        },
        address: {
          required: true,
          minlength: 5
        },
        email: {
          required: true,
          email: true
        },
        contact: {
          required: true,
          philippineMobile: true
        },
        password: {
          required: true,
          passwordStrength: true
        },
        passwordConfirm: {
          required: true,
          equalTo: "#signupPassword"
        }
      },
      messages: {
        firstName: {
          required: "Please enter your first name",
          nameFormat: "Please enter a valid first name",
          minlength: "First name must be at least 2 characters"
        },
        lastName: {
          required: "Please enter your last name",
          nameFormat: "Please enter a valid last name",
          minlength: "Last name must be at least 2 characters"
        },
        address: {
          required: "Please enter your address",
          minlength: "Address must be at least 5 characters"
        },
        email: {
          required: "Please enter your email address",
          email: "Please enter a valid email address"
        },
        contact: {
          required: "Please enter your contact number",
          philippineMobile: "Please enter a valid Philippine mobile number (11 digits, e.g. 09171234567)"
        },
        password: {
          required: "Please enter a password",
          passwordStrength: "Password must be at least 8 characters with uppercase, lowercase, number, and special character"
        },
        passwordConfirm: {
          required: "Please confirm your password",
          equalTo: "Passwords do not match"
        }
      },
      errorElement: 'div',
      errorClass: 'invalid-feedback',
      validClass: 'is-valid',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback d-block');
        element.addClass('is-invalid');
        // Prefer appending to the nearest field wrapper to keep layout intact
        var $wrapper = element.closest('.mb-3, .col-md-6, .form-group');
        if ($wrapper.length) {
          $wrapper.append(error);
        } else {
          var $group = element.closest('.input-group');
          if ($group.length) $group.after(error); else element.after(error);
        }
      },
      highlight: function(element){
        $(element).addClass('is-invalid').removeClass('is-valid');
      },
      unhighlight: function(element){
        $(element).removeClass('is-invalid').addClass('is-valid');
      },
      success: function(label, element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
        label.remove();
      },
      submitHandler: function(form) {
        var firstName = $.trim($sform.find('[name="firstName"]').val() || '');
        var lastName = $.trim($sform.find('[name="lastName"]').val() || '');
        var address = $.trim($sform.find('[name="address"]').val() || '');
        var email = $.trim($sform.find('[name="email"]').val() || '');
        var contact = $.trim($sform.find('[name="contact"]').val() || '');
        var password = $sform.find('[name="password"]').val() || '';

        var fullName = firstName + (lastName ? (' ' + lastName) : '');
        var payload = { firstName: firstName, lastName: lastName, fullName: fullName, address: address, email: email, contact: contact, password: password };

        $.ajax({ url: 'api/signup.php', method: 'POST', contentType: 'application/json', data: JSON.stringify(payload) })
          .done(function(json){
            try{ var bs = bootstrap.Modal.getInstance($signupModal[0]); if (bs) bs.hide(); }catch(e){}
            // attempt auto-login
            $.ajax({ url: 'api/login.php', method: 'POST', contentType: 'application/json', data: JSON.stringify({ email: email, password: password }) })
              .done(function(loginJson){
                try{
                  var session = { ts: Date.now() };
                  if (loginJson && loginJson.token) session.token = loginJson.token;
                  if (loginJson && loginJson.user) session.user = loginJson.user; else session.user = { email: email, firstName: firstName, lastName: lastName };
                  localStorage.setItem('nethshop_session', JSON.stringify(session));
                }catch(e){}
                showSuccessModalAndRedirect('signupSuccessModal');
                $sform[0].reset();
                $sform.find('.is-valid').removeClass('is-valid');
              }).fail(function(xhr){
                console.error('Auto-login after signup failed', xhr);
                alert('Account created, but auto-login failed. Please login manually.');
              });
          }).fail(function(err){
            console.error('Signup failed', err);
            alert('Signup failed: ' + (err.responseJSON && err.responseJSON.error ? err.responseJSON.error : 'Server error'));
          });
        return false; // Prevent default form submission
      }
    });
  });

})(window.jQuery);


