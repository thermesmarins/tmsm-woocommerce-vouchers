jQuery( document ).ready( function( $ ) {

  console.log('init');
  // on variation select dropdown, show woo-vou-fields wrapper
  $( ".single_variation_wrap" ).on( "show_variation", function ( b, c ) {
    console.log('show_variation');
    $(".vouchers-fields-wrapper-variation").hide();
    $("#vouchers-fields-wrapper-"+c.variation_id ).show();
  });

  // on clear selection, hide woo-vou-fields wrapper
  $( ".single_variation_wrap" ).on( "hide_variation", function ( event ) {
    console.log('hide_variation');
    $(".vouchers-fields-wrapper-variation").hide();
  });


});
