// toast.js - lightweight toast helper (jQuery)
(function($){
  'use strict';
  function ensureContainer(){
    var id = 'siteToastContainer';
    var $c = $('#' + id);
    if ($c.length) return $c[0];
    var c = document.createElement('div');
    c.id = id;
    c.style.position = 'fixed';
    c.style.zIndex = '1080';
    c.style.right = '1rem';
    c.style.bottom = '1rem';
    c.style.display = 'flex';
    c.style.flexDirection = 'column';
    c.style.gap = '0.5rem';
    document.body.appendChild(c);
    return c;
  }

  function createToast(msg, opts){
    opts = opts || {};
    var container = ensureContainer();
    var t = document.createElement('div');
    t.className = 'site-toast';
    t.style.minWidth = '180px';
    t.style.maxWidth = '320px';
    t.style.background = opts.background || 'rgba(0,0,0,0.85)';
    t.style.color = opts.color || '#fff';
    t.style.padding = '0.6rem 0.8rem';
    t.style.borderRadius = '6px';
    t.style.boxShadow = '0 6px 18px rgba(0,0,0,0.2)';
    t.style.opacity = '0';
    t.style.transition = 'opacity 180ms ease, transform 220ms ease';
    t.style.transform = 'translateY(8px)';
    t.innerText = msg || '';
    container.appendChild(t);
    // show
    requestAnimationFrame(function(){ t.style.opacity = '1'; t.style.transform = 'translateY(0)'; });
    var duration = (opts.duration && Number(opts.duration)) || 2200;
    var timeout = setTimeout(function(){ hide(); }, duration);
    function hide(){
      clearTimeout(timeout);
      t.style.opacity = '0'; t.style.transform = 'translateY(8px)';
      setTimeout(function(){ try{ container.removeChild(t); }catch(e){} }, 260);
    }
    // allow click to dismiss
    $(t).off('click').on('click', hide);
    return { hide: hide };
  }

  window.Toast = {
    show: function(msg, opts){ try { return createToast(msg, opts); } catch(e){ try{ alert(msg); }catch(e2){} } }
  };

})(window.jQuery);
