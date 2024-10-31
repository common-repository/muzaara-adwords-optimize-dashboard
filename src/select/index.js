import React from "react"
import "./select.css"

export default class Select extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			options : [],
			value : "",
			isOpen : false
		}
	}

	componentDidMount() {
		this.setState({ 
			options : this.props.options,
			value : this.props.value
		})

		document.body.onclick = this.close.bind(this)
	}

	static getDerivedStateFromProps( props, state ) {
		return {
			options : props.options,
			value : state.value,
			isOpen : state.isOpen
		}
	}

	getCurrentOption() {
		let option = this.state.options.filter( o => o.value === this.state.value )
		return option.length ? option[0] : {}
	}

	open( e ) {
		e.stopPropagation()
		this.setState({ isOpen : true })
	}

	close(e) {
		this.setState({ isOpen : false})
	}

	select(index, e) {
		e.stopPropagation()
		this.setState({ value : this.state.options[index].value, isOpen : false }, () => {
			this.props.onChange(this.state.value )
		})
	}

	render() {
		let optionsEl = this.state.options.map( ( option, index ) => (<li key={index} onClick={this.select.bind(this,index)}>{option.name}</li>) )
		let currentOption = this.getCurrentOption()

		return(
			<div className="muzaara-select" onClick={ this.open.bind(this) }>
				<div className="select">{currentOption.name}</div>
				<ul className={ "options " + (this.state.isOpen ? "is-open" : "" )}>{optionsEl}</ul>
			</div>
		)
	}
}