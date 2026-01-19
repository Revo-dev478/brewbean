(function() {
  'use strict';
  
  // Setup sebelum main.js
  $(document).ready(function() {
    // Tangkap semua click pada .menu-img
    $(document).off('click', '.menu-img').on('click', '.menu-img', function(e) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
      
      var href = $(this).data('href');
      console.log('Navigating to: ' + href); // Debug
      
      if (href) {
        // Delay sedikit untuk memastikan event terhenti
        setTimeout(function() {
          window.location.href = href;
        }, 50);
      }
      return false;
    });
    
    console.log('Menu navigation initialized');
  });
})();
