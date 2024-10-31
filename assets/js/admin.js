var m_interval;
var auth_window;
var selectrics;
var muzaaraChartData=[];
jQuery( document ).ready( function( $ ) {

	$( ".login-btn" ).click( function( e ) {
		e.preventDefault()

		auth_window = window.open( MUZAARA.auth_url, "auth_window", "status=1,width=500,height=700" );
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

	$( "#muzaara_edit" ).click( function() {
		$( ".account-summary" ).removeClass( "show" )
		$( ".account-summary" ).fadeOut(500)
	})

	var metricsEl = $( ".muzaara-widget .metrics" )
	if( metricsEl.length ) {
		// selectrics = $( ".muzaara-widget .metrics .column select" ).selectric();

		google.charts.load('current', {'packages':['line']});
		google.charts.setOnLoadCallback(function(){
			metricsEl.reports()
		});

		$( "table.keywords tbody" ).on( "change", "select", function() {
			var key = $(this).attr( "data-key" )
			var title = $( "table.keywords tbody" ).find( `td#keyword_${key}` )
			if( $(this).val() == 1 )
				title.text( `[${title.attr( "data-keyword" )}]` )
			else if( $(this).val() == 2 )
				title.text( `"${title.attr( "data-keyword" )}"` )
			else 
				title.text(title.attr("data-keyword"))
		})
	}
})


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

function loadMetrics(  success ) {
	jQuery.ajax({
		url : MUZAARA.ajax,
		data : { action : "muzaara_get_metrics" },
		type : "post",
		dataType : "json",
		beforeSend : function() {
			// jQuery( ".muzaara-widget .metrics .columns" ).fadeOut()
			// jQuery( ".dot-container" ).fadeIn()
		},
		success : function( response ) {
			// jQuery( ".dot-container" ).fadeOut(function() {
			// 	jQuery( ".muzaara-widget section" ).fadeIn()
			// })
			// jQuery( ".muzaara-widget .metrics .columns" ).fadeIn()
			success( response )
			// loadRecommendations()
		}
	})
}

function loadRecommendations() {
	jQuery.ajax({
		url : MUZAARA.ajax,
		data : { action : "muzaara_get_recommendations" },
		type : "post",
		dataType : "json",
		beforeSend : function() {

		},
		success : function( response ) {
			if( response.status ) {
				jQuery( "table.keywords tbody" ).empty()
				for(var i=0;i<response.data.keywords.length;i++) {
					var tRow = `
					<tr>
						<td><input type="checkbox" name="keywords[]" value="${response.data.keywords[i].resource_name}"></td>
						<td id="keyword_${i}" data-keyword="${response.data.keywords[i].keyword}">[${response.data.keywords[i].keyword}]</td>
						<td>${response.data.keywords[i].cpc_bid.toLocaleString( undefined, { style : "currency", currency: MUZAARA.currency })}</td>
						<td>
							<select class="" data-key="${i}">
								<option value="1">Exact</option>
								<option value="2">Phrase</option>
								<option value="3">Broad</option>
							</select>
						</td>
						<td>
							<span>${response.data.keywords[i].adgroup.name}</span><br />
							<small>${response.data.keywords[i].campaign.name}</small>
						</td>
					`
					jQuery( "table.keywords tbody" ).append(tRow)
				}
			}
		}
	})
}