/*
 * Author: InstaBuilder.com
 * Version: 1.0
 */

function ib2Conversion( page_id ) {
	var generateVisitorID = function ( length ) {
	    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
	
	    if ( !length ) {
	        length = Math.floor(Math.random() * chars.length);
	    }
	
	    var str = '';
	    for ( var i = 0; i < length; i++ ) {
	        str += chars[Math.floor(Math.random() * chars.length)];
	    }
	    
	    if ( $('#' + str).length )
	    	str = generateID(length);
	    	
    	return str;
	}
	
	var setCookie = function ( cookieName, cookieValue, exdays ) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cookieName + "=" + cookieValue + "; " + expires;
	} 

	var getCookie = function ( cookieName ) {
	    var name = cookieName + "=";
	    var ca = document.cookie.split(';');
	    for ( var i = 0; i < ca.length; i++ ) {
	        var c = ca[i];
	        while ( c.charAt(0) == ' ' ) c = c.substring(1);
	        if ( c.indexOf(name) != -1 ) return c.substring(name.length, c.length);
	    }
    	return "";
	}
	
	var visitorId = getCookie('__ib2vid');
	if ( visitorId == '' ) {
		visitorId = generateVisitorID(8);
		setCookie('__ib2vid', visitorId, 365);
	}
	
	var pageVariant = getCookie('__ib2pgvar_' + page_id);
	if ( pageVariant != '' ) {
		var image = new Image();
		image.src = "http://app.instapage.com/ajax/stats/conversion-pixel/"+ page[ 0 ] + "/" + page[ 1 ] + "/transparent.png";
	}
}


