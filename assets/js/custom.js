$( 'ul.nav.nav-tabs  a' ).click( function ( e ) {
	e.preventDefault();
	$( this ).tab( 'show' );
  } );
  ( function( $ ) {
	  fakewaffle.responsiveTabs( [ 'xs', 'sm' ] );
  } )( jQuery );
  