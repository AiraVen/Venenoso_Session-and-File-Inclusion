// products.js - wire Add to Cart and View behavior on the main product grid (jQuery)
(function($){
  'use strict';

  // Try to normalize price string like '₱1,599' or '$149.99' to number
  function parsePrice(text){ if(!text) return 0; var t = text.replace(/[₱$,\s]/g,'').replace(/,/g,''); var n = Number(t); return isNaN(n)?0:n; }

  // Login helpers: check session set by login-ui.js and open the login modal
  function isLoggedIn(){
    try{
      var s = localStorage.getItem('nethshop_session');
      if (!s) return false;
      var obj = JSON.parse(s);
      return !!(obj && (obj.user || obj.token));
    }catch(e){ return false; }
  }

  function showLoginModal(){
    try{
      var el = document.getElementById('loginModal');
      if (!el){
        // fallback: trigger nav login button if present
        var nav = document.getElementById('navLoginBtn'); if (nav) { try{ nav.click(); }catch(e){} };
        return;
      }
      var modal = (typeof bootstrap !== 'undefined') ? (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)) : null;
      if (modal) modal.show();
    }catch(e){
      try{ var nav = document.getElementById('navLoginBtn'); if (nav) nav.click(); }catch(_){}
    }
  }

  // pending action helpers (used when user clicks Buy while logged out)
  function setPendingAction(action, product){
    try{ sessionStorage.setItem('nethshop_pending', JSON.stringify({ action: action, product: product })); }catch(e){}
  }
  function getAndClearPendingAction(){
    try{ var p = sessionStorage.getItem('nethshop_pending'); if (!p) return null; sessionStorage.removeItem('nethshop_pending'); return JSON.parse(p); }catch(e){ return null; }
  }

  // Build a product object from a card element
  function productFromCard(card){
    var $card = $(card);
    var titleEl = $card.find('.card-title');
    var title = titleEl.length ? titleEl.text().trim() : $card.find('h5').text().trim() || 'Product';
    var priceEl = $card.find('.card-text');
    var price = priceEl.length ? parsePrice(priceEl.text()) : 0;
    var img = $card.find('img').attr('src') || '';
    // short description: try data-desc or alt text
    var desc = $card.find('p').text().trim() || $card.find('img').attr('alt') || '';
    // id: prefer explicit data-product-id on add button, then view link href or title
    var addBtn = $card.find('a.add-to-cart');
    var id = addBtn.length && addBtn.attr('data-product-id') ? addBtn.attr('data-product-id') : null;
    if (!id){ var viewLink = $card.find('a.text-decoration-none, a.btn-outline-secondary'); id = viewLink.length ? (viewLink.attr('href')||title) : title; }
    return { id: id, title: title, price: price, image: img, description: desc, qty: 1 };
  }

  function showProduct(product){
    var $pv = $('#productViewModal');
    if (!$pv.length) return;
    $('#pvImage').attr('src', product.image || '');
    $('#pvTitle').text(product.title || '');
    $('#pvPrice').text(product.price ? ('₱' + product.price.toLocaleString()) : '');
    $('#pvDesc').text(product.description || '');
    var bsModal = new bootstrap.Modal($pv[0]);
    bsModal.show();

    // attach add handler
    var $addBtn = $('#pvAddBtn');
    $addBtn.off('click').on('click', function(){
      if (!isLoggedIn()) { showLoginModal(); return; }
      if(window.Cart && window.Cart.addToCart){ 
        window.Cart.addToCart(product); 
        if (window.Toast && window.Toast.show) window.Toast.show(product.title + ' added to cart'); 
        else try{ alert(product.title + ' added to cart'); }catch(e){} 
        bsModal.hide(); 
      } else { 
        // fallback
        var cart = JSON.parse(localStorage.getItem('nethshop_cart_v1')||'[]'); 
        cart.push(product); 
        localStorage.setItem('nethshop_cart_v1', JSON.stringify(cart)); 
        if (window.Toast && window.Toast.show) window.Toast.show(product.title + ' added to cart'); 
        else try{ alert(product.title + ' added to cart'); }catch(e){} 
        bsModal.hide(); 
      }
    });

    // create a Buy Now button inside the product view modal to go directly to checkout
    var $pvBuy = $('#pvBuyBtn');
    if (!$pvBuy.length){
      $pvBuy = $('<button id="pvBuyBtn" class="btn btn-success ms-2">Buy now</button>');
      $addBtn.after($pvBuy);
    }
    // accessibility: label the modal Buy button with product title
    try{ $pvBuy.attr('aria-label', 'Buy ' + (product && product.title ? product.title : 'product')); }catch(e){}
    $pvBuy.off('click').on('click', function(){
      // require login before buying
      if (!isLoggedIn()) { showLoginModal(); return; }
      // add then navigate to checkout
      try { 
        if (window.Cart && window.Cart.addToCart) window.Cart.addToCart(product); 
        else { 
          var cart = JSON.parse(localStorage.getItem('nethshop_cart_v1')||'[]'); 
          cart.push(product); 
          localStorage.setItem('nethshop_cart_v1', JSON.stringify(cart)); 
        } 
      } catch(e){}
      // determine checkout path relative to current page
      var checkoutPath = (location.pathname.indexOf('/products/') !== -1) ? '../checkout.php' : 'checkout.php';
      window.location.href = checkoutPath;
    });
  }

  $(document).ready(function(){
    // Find all product cards on the page
    $('.card.h-100').each(function(){
      var $card = $(this);
      var prod = productFromCard(this);
      // wire View (buttons/link)
      var $viewBtn = $card.find('a.btn-outline-secondary').length ? $card.find('a.btn-outline-secondary') : $card.find('a.card-title');
      if ($viewBtn.length){
        $viewBtn.off('click').on('click', function(e){ 
          e.preventDefault(); 
          e.stopPropagation(); 
          showProduct(prod); 
        });
      }
      // wire Add to Cart (prefer explicit .add-to-cart class to avoid accidental navigation)
      var $addBtn = $card.find('a.add-to-cart').length ? $card.find('a.add-to-cart') : $card.find('a.btn-primary');
      if ($addBtn.length){
        $addBtn.off('click').on('click', function(e){ 
          e.preventDefault(); 
          e.stopPropagation(); 
          // prefer data-product-id for stable id
          var btn = $(this);
          var pid = btn.attr('data-product-id'); 
          if (pid) prod.id = pid;
          if (!isLoggedIn()) { showLoginModal(); return; }
          if(window.Cart && window.Cart.addToCart){ 
            window.Cart.addToCart(prod); 
            if (window.Toast && window.Toast.show) window.Toast.show(prod.title + ' added to cart'); 
            else try{ alert(prod.title + ' added to cart'); }catch(e){} 
          } else { 
            var cart = JSON.parse(localStorage.getItem('nethshop_cart_v1')||'[]'); 
            cart.push(prod); 
            localStorage.setItem('nethshop_cart_v1', JSON.stringify(cart)); 
            if (window.Toast && window.Toast.show) window.Toast.show(prod.title + ' added to cart'); 
            else try{ alert(prod.title + ' added to cart'); }catch(e){} 
          } 
        });
      }
      // inject a Buy Now button for quick checkout
      var $actions = $card.find('.card-body');
      if ($actions.length){
        // only add if not present
        if (!$card.find('.buy-now').length){
          var $buy = $('<button type="button" class="btn btn-success w-100 mt-2 buy-now">Buy now</button>');
          // if addBtn has data-product-id, copy it
          try { 
            var copyId = ($addBtn.length && $addBtn.attr) ? $addBtn.attr('data-product-id') : null; 
            if (copyId) $buy.attr('data-product-id', copyId); 
          } catch(e){}
          // accessibility: label with product title when available
          try{ $buy.attr('aria-label', 'Buy ' + (prod && prod.title ? prod.title : 'product')); }catch(e){}
          $actions.append($buy);
          $buy.off('click').on('click', function(ev){ 
            ev.preventDefault(); 
            ev.stopPropagation(); 
            var btn = $(this);
            var pid = btn.attr('data-product-id'); 
            if (pid) prod.id = pid; 
            if (!isLoggedIn()) { try{ setPendingAction('buy', prod); }catch(e){} showLoginModal(); return; }
            // show product details modal for confirmation before checkout
            try{ showProduct(prod); }catch(e){}
            setTimeout(function(){ try{ $('#pvBuyBtn').focus(); }catch(e){} }, 250);
          });
        }
      }
    });

    // If this is a standalone product page (has .product-img and h1), wire the Add to Cart button
    var $productPageImg = $('.product-img');
    if ($productPageImg.length){
      var title = $('h1').length ? $('h1').text().trim() : document.title;
      var priceText = $('.lead').length ? $('.lead').text() : '';
      var price = parsePrice(priceText);
      // gather description paragraphs
      var descNodes = $('main p');
      var desc = descNodes.map(function(){ return $(this).text().trim(); }).get().join('\n');
      var $addBtn = $('.add-to-cart').length ? $('.add-to-cart') : $('a.btn-primary');
      if ($addBtn.length){
        var product = { id: location.pathname + '#' + title, title: title, price: price, image: $productPageImg.attr('src') || '', description: desc, qty: 1 };
        // prefer explicit data-product-id on the add button
        var pid = $addBtn.attr('data-product-id'); 
        if (pid) product.id = pid;
        $addBtn.off('click').on('click', function(e){ 
          e.preventDefault(); 
          if (!isLoggedIn()) { showLoginModal(); return; }
          if(window.Cart && window.Cart.addToCart){ 
            window.Cart.addToCart(product); 
            if (window.Toast && window.Toast.show) window.Toast.show(product.title + ' added to cart'); 
            else try{ alert(product.title + ' added to cart'); }catch(e){} 
          } else { 
            var cart = JSON.parse(localStorage.getItem('nethshop_cart_v1')||'[]'); 
            cart.push(product); 
            localStorage.setItem('nethshop_cart_v1', JSON.stringify(cart)); 
            if (window.Toast && window.Toast.show) window.Toast.show(product.title + ' added to cart'); 
            else try{ alert(product.title + ' added to cart'); }catch(e){} 
          } 
        });
        // create a Buy Now button on product pages to add and go to checkout
        if (!$('.buy-now[data-product-id="'+product.id+'"]').length){
          var $buyNowBtn = $('<button type="button" class="btn btn-success ms-2 buy-now">Buy now</button>');
          // Place after addBtn
          $addBtn.after($buyNowBtn);
          try{ $buyNowBtn.attr('aria-label','Buy ' + product.title); }catch(e){}
          $buyNowBtn.off('click').on('click', function(ev){ 
            ev.preventDefault(); 
            if (!isLoggedIn()) { try{ setPendingAction('buy', product); }catch(e){} showLoginModal(); return; }
            try{ showProduct(product); }catch(e){}
            setTimeout(function(){ try{ $('#pvBuyBtn').focus(); }catch(e){} }, 250);
          });
        }
      }
    }
    // check for pending actions from login flow (e.g., attempted cart add while logged out)
    try{
      var pending = getAndClearPendingAction();
      if (pending && isLoggedIn() && pending.action === 'cart' && pending.product){
        try{ showProduct(pending.product); }catch(e){}
      }
    }catch(e){}
  });

})(window.jQuery);
