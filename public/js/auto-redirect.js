(function($) {

  $('body').append('<div class="fixed w-full h-full left-0 top-0 bg-white/[1] z-10">' +

    '<div class="grid place-content-center h-full">' +

      '<div class="text-center max-w-md">' + 

        '<h4 class="text-lg mb-2 font-semibold">This process is automatic. Your browser will be redirected to the Midtrans partner payment page.</h4>' + 

        '<p class="text-gray-900">Please wait a moment..<p>' + 

        '<p class="text-sm text-gray-900 font-semibold mt-4">' +

          'Paymen link: <a class="text-blue-600 hover:text-blue-900" href="' + ajaxObj.payment_link + '" rel="noopener nofollow" target="_blank">' + ajaxObj.payment_link + '</a>' +

        '</p>' +

        '<p class="text-gray-600 text-sm">Powered by Memberpay<p>' +

      '</div>' +

    '</div>' +

  '</div>');



  $(window).load(function() {

    setTimeout(function() {

      var payment_page = window.open(ajaxObj.payment_link, '_blank');

  

      if(payment_page) {

        payment_page.focus(); 

        document.location.href = ajaxObj.subscription_link;

      } else {

        document.location.href = ajaxObj.payment_link;

      }

    }, 1500)

  })

})(jQuery);