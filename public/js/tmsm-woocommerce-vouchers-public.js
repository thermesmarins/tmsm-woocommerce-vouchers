jQuery( document ).ready( function( $ ) {

  // on variation select dropdown, show woo-vou-fields wrapper
  $( ".single_variation_wrap" ).on( "show_variation", function ( b, c ) {
    $(".vouchers-fields-wrapper-variation").hide();
    $("#vouchers-fields-wrapper-"+c.variation_id ).show();
  });

  // on clear selection, hide woo-vou-fields wrapper
  $( ".single_variation_wrap" ).on( "hide_variation", function ( event ) {
    $(".vouchers-fields-wrapper-variation").hide();
  });


});
