import React from "react"
import {render} from "react-dom"
import "core-js/stable"

import Metrics from "./metrics"

let metricsEl = document.getElementById( "muzaara-metrics" )

if( metricsEl )
	render( <Metrics />, metricsEl )

jQuery(document).ready(function($) {
	let m_interval;
	$( ".login-btn" ).click( function( e ) {
		e.preventDefault()

		let auth_window = window.open( MUZAARA.auth_url, "auth_window", "status=1,width=500,height=700" );
		$(this).fadeOut(function() {
			$( ".dot-container" ).fadeIn()
		})
		m_interval = setInterval( function() {
			if( auth_window.closed ) {
				clearInterval( m_interval )
				check_linking()
			}
		}, 500 )
	})

	$( ".account-listing" ).on( "click", "li", function() {
		var $this = $( this )
		if( $this.hasClass( "is-selected" ) ) 
			return;
		$this.parent().find( ".is-selected" ).removeClass( "is-selected" )
		$this.addClass( "is-loading" )
		muzaara_select_customer( $this.attr( "data-id" ), $this.attr( "data-timezone" ), $this.attr( "data-currency" ), $this )
	})

	$( "#muzaara_save" ).click( function() {
		$( ".account-summary" ).fadeIn(500).addClass( "show" )
		var selected = $( ".account-listing li.is-selected" )
		if( selected.length ) {
			$( "#account_id" ).text( selected.attr( "data-id" ) )
			$( "#account_name" ).text( selected.attr( "data-name" ) )
		}
	})

	$( "#muzaara-unlink" ).click( function( e ) {
		e.preventDefault()
		var $this = $(this)
		if( !confirm( "You are about to unlink your account. Proceed?" ) )
			return;
		
		$.ajax({
			url : MUZAARA.ajax,
			data : { action : "muzaara_unlink" },
			method : "POST",
			beforeSend : function() {
				$this.html( "Unlinking..." )
			},
			success : function() {
				window.location.reload()
			}
		})
	})

	$( "#muzaara_edit" ).click( function() {
		$( ".account-summary" ).removeClass( "show" )
		$( ".account-summary" ).fadeOut(500)
	})

	$( "#muzaara-settings" ).on( "change", "select, input", function( e ) {
		let type = $(this).attr("type")
		if(type=="checkbox") {
			change_muzaara_settings($(this).attr("name"), $(this).is(":checked")?1:0)
		}
		else {
			change_muzaara_settings($(this).attr("name"),$(this).val())
		}
	})
})

function change_muzaara_settings(setting,value) {
	jQuery.ajax({
		url :MUZAARA.ajax,
		data : { action : "muzaara_save_setting", setting_name : setting, setting_value : value },
		type : "post",
		dataType : "json",
		success : function( response ) {
			console.log( response )
		}
	})
}

function check_linking() {
	jQuery.ajax({
		url : MUZAARA.ajax,
		data : { action : "muzaara_link_status" },
		type : "post",
		dataType : "json",
		success : function( response ) {
			if( response.status ) {
				window.location.reload()
			}
			else {
				jQuery( ".dot-container" ).fadeOut(function() {
					jQuery( ".login-btn" ).fadeIn()
				})
				console.log( "not linked")
			}
		}
	})
}

function muzaara_select_customer( id, tz, currency, el ) {
	jQuery.ajax({
		url : MUZAARA.ajax,
		data : { action : "muzaara_choose_account", account_id : id, account_tz : tz, currency : currency, account_name : el.attr( "data-name" ) },
		type : "post",
		dataType : "json",
		success : function( response ) {
			el.toggleClass( "is-selected is-loading" )
		}
	})
}