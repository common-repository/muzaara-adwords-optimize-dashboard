import React from "react"
import { Selects, getForm } from "../utils"
import "whatwg-fetch"
import Recommendations from "../recommendations"
import numeral from "numeral"


class App extends React.Component {
	constructor( props ) {
		super(props)

		this.state = {
			metrics : {},
			columns : {},
			range : [ moment().subtract(6, "days"), moment() ],
			loading_reports : true,
			total_days : 7,
			column_options : [
				{
					color : "#d23f31",
					selected : "impressions",
					total : 0,
					disabled : false
				},
				{
					color : "#3b78e7",
					selected : "clicks",
					total : 0,
					disabled : false
				},
				{
					color : "#f2a600",
					selected : "average_cpc",
					total : 0,
					divide: true,
					disabled : true
				},
				{
					color : "#0d904f",
					selected : "average_cost",
					total : 0,
					disabled : true
				}
			]
		}

		this.chartRef = React.createRef()
		this.datePicker = React.createRef()
		this.selectsRef = []
		this.getReports = this.getReports.bind(this)
	}

	componentDidMount() {
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(this.getReports);

		jQuery( this.datePicker.current ).daterangepicker({
			startDate: this.state.range[0],
	        endDate: this.state.range[1],
	        ranges: {
	           'Today': [moment(), moment()],
	           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	           'This Month': [moment().startOf('month'), moment().endOf('month')],
	           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	        }
		}, this.onChangeDate.bind(this) )
	}

	onChangeDate( start, end ) {
		if( !this.state.loading_reports )
			this.setState({ range : [ start, end ]}, this.getReports )
	}

	selectClick( index, event ) {
		if( !this.state.loading_reports ) {
			let column_options = [...this.state.column_options]
			column_options[index].disabled = !column_options[index].disabled
			this.setState({ column_options }, this.initChart )
		}
	}

	selectChange( index, value ) {
		if( !this.state.loading_reports ) {
			let column_options = [...this.state.column_options]
			column_options[index].selected = value
			this.setState({ column_options }, this.initChart )
		}
	}

	getReports() {
		this.setState({ loading_reports : true }, () => {
			fetch( MUZAARA.ajax, {
				method : "POST",
				body : getForm({ action : "muzaara_get_metrics", start_date : this.state.range[0].format("L"), stop_date: this.state.range[1].format("L") }),
				credentials : "same-origin"
			})
			.then( response => response.json() )
			.then( json => {
				if( json.status ) {
					this.setState({ loading_reports : false, metrics : json.data.metrics, columns : json.data.columns }, this.initChart )
				}
			})
		})
	}

	initChart() {
		let column_options = [...this.state.column_options ].map( c => {
			c.total = 0
			return c;
		})

		this.setState({ column_options }, () => {
			let dataTable = new google.visualization.DataTable();
			let addedColumns = false
			let rows=[]
			let columnsData = {}
			dataTable.addColumn({ type : "date", role : "domain" })
			let total_days=1;
			for( let time in this.state.metrics ) {
				let row = [ new Date( this.state.metrics[time].date) ]
				let colCount=0;
				let column_options = [...this.state.column_options]
				for(var i=0; i < column_options.length; i++ ) {
					let data = parseFloat( this.state.metrics[time][column_options[i].selected] )
					let tooltip = `${numeral(data).format( "0,0.00" )} ${this.state.columns[column_options[i].selected]}`
					
					column_options[i].total += data

					if( column_options[i].disabled ) 
						continue;

					if( !addedColumns ) {
						dataTable.addColumn( { role : "data", type : "number", }, this.state.columns[column_options[i].selected] )
						dataTable.addColumn({ role : "tooltip", type : "string"})
						columnsData[colCount] = {color : this.state.column_options[i].color }
						colCount++
					}

					row.push( data )
					row.push(tooltip)
				}
				this.setState({ column_options })
				addedColumns=true
				rows.push(row)
				total_days++;
			}

			dataTable.addRows(rows)
			this.setState({total_days})

			let chart = new google.visualization.LineChart(this.chartRef.current)
			var chartOptions = {
				legend : {
					position : "none"
				},
				series : columnsData,
				isStacked : true,
				curveType: "none",
				focusTarget : "category",
				crosshair : {
					trigger : "both",
					orientation : "vertical"
				},
				vAxis: {
				    gridlines: {
				        color: 'transparent'
				    },
				    format : "short"
				},
				hAxis: {
				    gridlines: {
				        color: 'transparent'
				    }
				}
			}
			
			chart.draw(dataTable, chartOptions  )
		})
	}

	render() {
		return(
			<div className="metrics">
				<div ref={this.datePicker} style={ { 
					background: "#fff", 
					cursor: "pointer", 
					padding: "5px 10px", 
					border: "1px solid #ccc", 
					display:"inline-block", 
					position : "absolute", 
					top : "-50px",
					right : "0" 
				} }>
					<i className="icon-calender"></i>&nbsp;
					<span>{this.state.range[0].format( 'MMMM D, YYYY' )} - {this.state.range[1].format( 'MMMM D, YYYY' )}</span> <i className="icon-chevron-down1"></i>
				</div>

				<Selects columns={ this.state.columns } selects={this.state.column_options} onClick={this.selectClick.bind(this)} onChange={this.selectChange.bind(this)} total_days={this.state.total_days} />

				<div style={ { width : "99.5%" } } ref={ this.chartRef }></div>
				<Recommendations />
				{ this.state.loading_reports ? <div className="loading-reports"></div> : null }
			</div>
		);
	}
}

export default App;