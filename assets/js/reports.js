( function( $ ) {
	var chartEl = $( '<div class="muzaara-chart" id="muzaara-chart"></div>' )
	var columnsEl = $( `
		<div class="columns">
			<div class="column blue">
				<select></select>
				<span class="total"></span>
			</div>
			<div class="column red">
				<select></select>
				<span class="total"></span>
			</div>
			<div class="column orange disabled">
				<select></select>
				<span class="total"></span>
			</div>
			<div class="column green disabled">
				<select></select>
				<span class="total"></span>
			</div>
			<div class="clearfix"></div>
		</div>
	`)
	var datePickerEl = $( `
		<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; display:inline-block;">
			<i class="icon-calender"></i>&nbsp;
			<span></span> <i class="icon-chevron-down1"></i>
		</div>` )
	var selects = columnsEl.find( "select" );
	var startDate = moment().subtract(6, 'days');
	var endDate = moment();
	var metricsData;

	$.fn.reports = function( options ) {
		var settings = $.extend({
			data : {
				columns : {},
				metrics : {},
				chartOptions : {}
			}
		}, options )

		this.empty()
			.addClass("metrics")
			.append(datePickerEl)
			.append(columnsEl)
			.append(chartEl)

		displayDatePicker()
		columnsEl.on( "click", ".selectric", function(e) {
			e.stopPropagation()
		})

		columnsEl.on( "change", "select", function() {
			loadReportsChart(settings.chartOptions)
		})

		columnsEl.on( "click", ".column", function() {
			$(this).toggleClass("disabled")
			loadReportsChart( settings.chartOptions)
		})

		return this
	}

	function loadSelectableColumns( columns ) {
		$.each( columns, function( value, name ) {
			selects.append( `<option value="${value}">${name}</option>` )
		})
		
		selects.each( function( i, el ) {
			$(el).prop( "selectedIndex", i ).selectric( "refresh" )
		})
	}

	function cb(start, end) {
        datePickerEl.find( "span" ).html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $.ajax({
        	url : MUZAARA.ajax,
        	data : { action : "muzaara_get_metrics", start_date : start.format("L"), stop_date : end.format("L") },
        	type : "post",
        	dataType : "json",
        	beforeSend : function() {
        		columnsEl.parentsUntil( ".wrap", ".metrics" ).css( "opacity", "0.3")
        	},
        	success : function( response ) {
        		columnsEl.parentsUntil( ".wrap", ".metrics" ).css( "opacity", "1")
        		if( response.status ) {
        			metricsData=response.data
        			loadSelectableColumns( response.data.columns )
					loadReportsChart({})
        		}
        	}
        })
    }

	function displayDatePicker() {
		datePickerEl.daterangepicker({
			startDate: startDate,
	        endDate: endDate,
	        ranges: {
	           'Today': [moment(), moment()],
	           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	           'This Month': [moment().startOf('month'), moment().endOf('month')],
	           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	        }
		}, cb )

		cb(startDate, endDate);
	}

	function loadReportsChart( options ) {
		var dataTable = new google.visualization.DataTable();
		var rows = []
		var data = metricsData
		var times = Object.keys(data.metrics)
		var columnData = {}
		var columnsTotals = {}

		dataTable.addColumn({ type : "date", role : "domain" })
		
		for(var i=0;i<times.length;i++) {
			var time = times[i]
			var report = data.metrics[time]
			
			var row = [ new Date(report.date) ]
			var colIndex=0;
			
			for(var ei=0;ei<selects.length;ei++) {
				var el = selects[ei]
				var parent = $(el).parentsUntil(".columns", ".column")
				
				if( parent.hasClass("disabled" ) ) {
					continue;
				}
				if(i==0) {
					dataTable.addColumn( { role : "data", type : "number", }, data.columns[el.value] )
					columnData[colIndex] = {color:parent.css("background-color")}
					colIndex++
				}
				
				var v = parseFloat( data.metrics[time][el.value] );
				
				v = isNaN(v) ? 0 : v
				columnsTotals[ei] = typeof columnsTotals[ei] !== "undefined" ? parseFloat( columnsTotals[ei] )+v : v;
				var totalDisplay = "0.0K"

				if( columnsTotals[ei] >= 1000 && columnsTotals[ei] <= 1000000 ) {
					totalDisplay = `${parseFloat(columnsTotals[ei]/1000).toFixed(2)}K`
				}
				else if( columnsTotals[ei] >= 1000000 ) {
					totalDisplay = `${parseFloat(columnsTotals[ei]/1000000).toFixed(2)}M`
				}
				else {
					totalDisplay =columnsTotals[ei]
				}
				parent.find( ".total" ).text(totalDisplay)
				row.push( v )
			}
			rows.push( row )
		}
	
		dataTable.addRows(rows);

		var chart = new google.charts.Line(chartEl.get(0))
		var chartOptions = {
			legend : {
				position : "none"
			},
			series : columnData,
			curveType: "function",
			focusTarget : "category"
		}

		chartOptions = $.extend( chartOptions, options )
		
		chart.draw(dataTable, google.charts.Line.convertOptions( chartOptions ) )
	} 
} (jQuery))