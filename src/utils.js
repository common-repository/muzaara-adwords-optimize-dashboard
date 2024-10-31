import React from "react"
import Select from "./select"
import numeral from "numeral"

export const Selects = ({ columns, selects, onClick, onChange, total_days }) => {
	let options = []
	let c = []
	let i=0;
	for( let column in columns ) {
		options.push(<option value={column} key={i++}>{columns[column]}</option>)
		c.push({name : columns[column], value : column })
	}

	let s = selects.map( (value,key) => {
		if( typeof value.divide !== "undefined" ) 
			value.total = value.total/total_days
		
		return(
			<div key={ key } className={ `column ` + ( value.disabled ? 'disabled' : null ) } style={ { backgroundColor : ( value.disabled ? "#fff" : value.color )} }onClick={onClick.bind(this,key)}>
				<Select options={c} value={value.selected} onChange={onChange.bind(this,key)} />
				<span className="total">
				{ 
					numeral(value.total).format( "0.00a").toUpperCase()

				}
				</span>
			</div>
		)
	})

	return(
		<div className="columns">
			{s}
			<div className="clearfix"></div>
		</div>
	)
}

export const getForm = ( data, form, name ) => {
	let f = typeof form !== "undefined" ? form : new FormData()
	
	for( let field in data ) {
		if( typeof data[ field ] == "object" ) {
			getForm( data[ field ], f, field )
		}
		else {
			f.append( ( typeof name !== "undefined" ? name + "[" + field + "]" : field ), data[ field ] )
		}
	}

	return f;
}