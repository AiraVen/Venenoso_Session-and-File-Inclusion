// Checkout page script: renders cart items, updates quantities, and places order. (jQuery)
(function($){
  'use strict';

  function formatPrice(n){ return '₱' + Number(n).toLocaleString(); }

  function readCart(){ try { return (window.Cart && window.Cart._read) ? window.Cart._read() : JSON.parse(localStorage.getItem('nethshop_cart_v1')||'[]'); } catch(e){ return []; } }
  function writeCart(cart){ localStorage.setItem('nethshop_cart_v1', JSON.stringify(cart)); }

  function render(){
    var $itemsEl = $('#checkoutItems');
    var $summaryList = $('#summaryList');
    var $subtotalEl = $('#subtotal');
    var $totalEl = $('#total');
    var $cardSummary = $('.card-summary');
    if (!$itemsEl.length || !$summaryList.length || !$subtotalEl.length || !$totalEl.length) return;

    var cart = readCart();
    $itemsEl.empty();
    $summaryList.empty();

    if (!cart.length){
      $itemsEl.html('<div class="list-group-item text-center py-4 muted">No products available in your cart.</div>');
      $cardSummary.hide();
      return;
    }
    $cardSummary.show();

    var subtotal = 0;
    cart.forEach(function(item, idx){
      var lineTotal = (item.price||0) * (item.qty||1);
      subtotal += lineTotal;
      var $row = $(
        '<div class="list-group-item d-flex gap-3 py-3 align-items-center">' +
          '<img src="'+(item.image||'https://via.placeholder.com/160')+'" alt="'+(item.title||'Product')+'" class="item-img item-img-lg">' +
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
        '</div>'
      );
      $itemsEl.append($row);

      var $li = $('<li class="d-flex justify-content-between"><span>'+item.title+' ×'+(item.qty||1)+'</span><strong>'+formatPrice(lineTotal)+'</strong></li>');
      $summaryList.append($li);
    });

    $subtotalEl.text(formatPrice(subtotal));

    // compute shipping from selected option if present
    var $shippingOption = $('#shippingOption');
    var shippingFee = 0;
    if ($shippingOption.length){ shippingFee = Number($shippingOption.find('option:selected').data('fee') || 0) || 0; }
    $('#shippingFee').text(formatPrice(shippingFee));
    $totalEl.text(formatPrice(subtotal + shippingFee));

    // attach handlers
    $itemsEl.find('.qty-input').off('change').on('change', function(){
      var i = Number($(this).data('idx'));
      var val = Math.max(1, Number($(this).val())||1);
      var cart = readCart();
      if (!cart[i]) return;
      cart[i].qty = val;
      writeCart(cart);
      render();
    });
    $itemsEl.find('.btn-remove').off('click').on('click', function(){
      var i = Number($(this).data('idx'));
      var cart = readCart();
      cart.splice(i,1);
      writeCart(cart);
      render();
    });

    // when shipping option changes, re-render totals
    $shippingOption.off('change').on('change', function(){ render(); });
  }

  // Place order: try POST to /api/checkout, fallback to localStorage 'nethshop_orders'
  function placeOrder(){
    var cart = readCart();
    if (!cart.length){ if (window.Toast && window.Toast.show) { window.Toast.show('Your cart is empty.'); } else try{ alert('Your cart is empty.'); }catch(e){} return; }

    // collect UI fields
    var address = ($('#shippingAddress').val() || '');
    var $shippingOption = $('#shippingOption');
    var shippingOption = $shippingOption.length ? $shippingOption.val() : 'standard';
    var shippingFee = $shippingOption.length ? Number($shippingOption.find('option:selected').data('fee') || 0) || 0 : 0;
    var paymentMethod = ($('input[name="paymentMethod"]:checked').val() || 'card');
    var notes = ($('#orderNotes').val() || '');

    var subtotal = cart.reduce(function(s,i){ return s + (i.price||0)*(i.qty||1); },0);
    var total = subtotal + shippingFee;

    var payload = {
      items: cart,
      subtotal: subtotal,
      shippingFee: shippingFee,
      shippingOption: shippingOption,
      shippingAddress: address,
      paymentMethod: paymentMethod,
      notes: notes,
      total: total,
      createdAt: new Date().toISOString()
    };

    // try to POST using jQuery.ajax
    $.ajax({
      url: '/api/checkout',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(payload)
    }).done(function(data){
      // success: clear cart and show confirmation modal (with order id if provided)
      writeCart([]);
      var orderId = data && data.orderId ? data.orderId : null;
      $('#orderSuccessOrderId').text(orderId ? ('Order ID: ' + orderId) : '');
      $('#orderSuccessMessage').text('Thank you — your order has been placed.');
      var $modalEl = $('#orderSuccessModal');
      if ($modalEl.length && typeof bootstrap !== 'undefined'){
        var bs = bootstrap.Modal.getOrCreateInstance($modalEl[0]);
        bs.show();
        setTimeout(function(){ try{ bs.hide(); }catch(e){}; window.location.href = 'index.php'; }, 1800);
      } else if (window.Toast && window.Toast.show) {
        window.Toast.show('Order placed successfully (demo).');
        setTimeout(function(){ window.location.href = 'index.php'; }, 1200);
      } else try{ alert('Order placed successfully (demo).'); window.location.href = 'index.php'; }catch(e){ window.location.href='index.php'; }
    }).fail(function(){
      // fallback: save to localStorage orders
      var orders = JSON.parse(localStorage.getItem('nethshop_orders')||'[]');
      orders.push(payload);
      localStorage.setItem('nethshop_orders', JSON.stringify(orders));
      writeCart([]);
      $('#orderSuccessOrderId').text('');
      var $modalEl = $('#orderSuccessModal');
      if ($modalEl.length && typeof bootstrap !== 'undefined'){
        var bs = bootstrap.Modal.getOrCreateInstance($modalEl[0]);
        $('#orderSuccessMessage').text('Order saved locally.');
        bs.show();
        setTimeout(function(){ try{ bs.hide(); }catch(e){}; window.location.href = 'index.php'; }, 1800);
      } else if (window.Toast && window.Toast.show) {
        window.Toast.show('Order saved locally (no server).');
        setTimeout(function(){ window.location.href = 'index.php'; }, 1200);
      } else try{ alert('Order saved locally (no server).'); window.location.href = 'index.php'; }catch(e){ window.location.href='index.php'; }
    });
  }

  $(document).ready(function(){
    render();
    $('#placeOrderBtn').off('click').on('click', placeOrder);
  });

})(window.jQuery);

