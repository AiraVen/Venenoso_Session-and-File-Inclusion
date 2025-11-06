// Simple cart manager using localStorage. Exposes addToCart(product) and renders cart in #cartItems (jQuery)
(function($){
  'use strict';
  var STORAGE_KEY = 'nethshop_cart_v1';

  function readCart(){
    try{ return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }catch(e){ return []; }
  }
  function writeCart(cart){ localStorage.setItem(STORAGE_KEY, JSON.stringify(cart)); }

  function formatPrice(n){ return '₱' + Number(n).toLocaleString(); }

  function render(){
    var $container = $('#cartItems');
    var $summary = $('.card-summary');
    if (!$container.length) return;
    var cart = readCart();
    $container.empty();
    if (!cart.length){
      $container.html('<div class="list-group-item text-center py-4 muted">Your cart is empty.</div>');
      $summary.hide();
      return;
    }
    $summary.show();

    cart.forEach(function(item, idx){
      var $el = $('<div class="list-group-item d-flex gap-3 py-3 align-items-center">' +
        '<img src="'+(item.image||'')+'" alt="'+(item.title||'Product')+'" class="item-img">' +
        '<div class="d-flex flex-column flex-grow-1">' +
          '<div class="d-flex w-100 justify-content-between">' +
            '<h6 class="mb-1">'+(item.title||'Product')+'</h6>' +
            '<small class="text-muted">'+formatPrice(item.price||0)+'</small>' +
          '</div>' +
          '<p class="mb-1 muted">'+(item.description||'')+'</p>' +
          '<div class="d-flex align-items-center gap-2">' +
            '<label class="mb-0">Qty</label>' +
            '<input data-idx="'+idx+'" type="number" class="form-control form-control-sm qty-input" value="'+(item.qty||1)+'" min="1" aria-label="Quantity">' +
            '<button data-idx="'+idx+'" class="btn btn-sm btn-outline-danger ms-2 btn-remove">Remove</button>' +
          '</div>' +
        '</div>' +
      '</div>');
      $container.append($el);
    });

    // attach handlers
    $container.find('.qty-input').off('change').on('change', function(){
      var i = Number($(this).data('idx'));
      var val = Math.max(1, Number($(this).val())||1);
      var cart = readCart();
      if (cart[i]) {
        cart[i].qty = val;
        writeCart(cart);
        render();
      }
    });
    $container.find('.btn-remove').off('click').on('click', function(){
      var i = Number($(this).data('idx'));
      var cart = readCart();
      cart.splice(i,1);
      writeCart(cart);
      render();
    });

    // update order summary (inject into #summaryList, #cartSubtotal, #cartTotal)
    var total = 0;
    var $list = $('#summaryList');
    if ($list.length) $list.empty();
    cart.forEach(function(item){
      total += (item.price||0) * (item.qty||1);
      if ($list.length){
        var $li = $('<li class="d-flex justify-content-between"><span>'+(item.title||'Product')+' ×'+(item.qty||1)+'</span><strong>'+formatPrice((item.price||0)*(item.qty||1))+'</strong></li>');
        $list.append($li);
      }
    });
    var $subtotalEl = $('#cartSubtotal'); if ($subtotalEl.length) $subtotalEl.text(formatPrice(total));
    var $totalEl = $('#cartTotal'); if ($totalEl.length) $totalEl.text(formatPrice(total));

    // enable/disable checkout button
    var $checkoutBtn = $('#checkoutSummaryBtn');
    if ($checkoutBtn.length) {
      if (total <= 0) { 
        $checkoutBtn.addClass('disabled').attr('aria-disabled','true'); 
      } else { 
        $checkoutBtn.removeClass('disabled').removeAttr('aria-disabled'); 
      }
    }
  }

  // Public API
  window.Cart = {
    addToCart: function(product){
      var cart = readCart();
      // merge by id if present
      var idx = -1; if (product && product.id){ idx = cart.findIndex(function(i){ return i.id == product.id; }); }
      if (idx > -1){ cart[idx].qty = (cart[idx].qty||1) + (product.qty||1); }
      else { cart.push({ id: product.id, title: product.title, price: product.price, image: product.image, description: product.description, qty: product.qty||1 }); }
      writeCart(cart); render();
    },
    clear: function(){ writeCart([]); render(); },
    _read: readCart
  };

  // init
  $(document).ready(function(){ render(); });
})(window.jQuery);