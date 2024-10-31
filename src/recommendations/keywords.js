import React from "react"
import "./css/keywords.css"
import Select from "../select"
import "whatwg-fetch"
import {getForm} from "../utils"
import jsonToFormData from "json-form-data"

export default class Keywords extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			isOpen : false,
			keywords : this.props.keywords,
			isEdit : false,
			selected : [],
			applying : false
		}

		this.MAX_SHOW = 4
	}

	edit(e) {
		e.preventDefault()

		this.setState({ isEdit : true })
	}

	unedit(e) {
		e.preventDefault()
		this.setState({ isEdit :false})
	}

	changeKeyword( index, value ) {
		let keywords = [...this.state.keywords]
		let selected = keywords[index].keyword.replace( /[\[\]\"]/g, "" )
		
		switch( value ) {
			case 2:
				keywords[index].keyword = `[${selected}]`
				break;
			case 3:
				keywords[index].keyword = `"${selected}"`
				break;
			default:
				keywords[index].keyword = selected
		}

		keywords[index].type = value

		let selectedIndexes = [...this.state.selected]
		if( !selectedIndexes.includes(index) ) 
			selectedIndexes.push(index)
		this.setState({ keywords, selected : selectedIndexes })
	}

	select( index ) {
		if( !this.state.selected.includes(index) ) {
			this.setState({ selected : [...this.state.selected, index ]})
		}
	}

	unselect(index) {
		if( this.state.selected.includes(index) )
			this.setState({ selected : this.state.selected.filter( i => i !== index ) })
	}

	apply() {
		if( this.state.applying )
			return;

		this.setState({ applying : true }, () => {
			let keywords = this.state.selected.map( index => this.state.keywords[index] )
			if( !this.state.isEdit ) {
				keywords = this.state.keywords 
				if( !confirm( `You are about to apply ${keywords.length} recommendations. Continue?` ) ) {
					this.setState({ applying : false})
					return;
				}
			}

			fetch( MUZAARA.ajax, {
				method : "POST",
				body : jsonToFormData({ action : "muzaara_apply", type : "keyword", recommendations : keywords }),
				credentials : "same-origin"
			})
			.then( response => response.json() )
			.then( json => {
				if( json.status ) {
					this.setState({ applying : false, isEdit : false, keywords: this.state.keywords.filter( ( k, i ) => !this.state.selected.includes( i ) ) })
				}
				else {
					this.setState({ applying : false, isEdit : false})
				}
			})
		})
	}

	render() {
		let keywords = [...this.state.keywords]
		keywords.splice(this.MAX_SHOW)
		let keywordsComp = keywords.map( ( keyword, index ) => {
			return <li className="keyword" key={index}>{keyword.keyword}</li>
		})

		return (
			<div className="">
				<div className="recommendation">
					<header>
						Add new keywords
					</header>
					<p>Show your ads more often to people searching for what your business offers </p>
					<span className="info">Recommended because you're not targeting searches that could be relevant to your business</span>

					<section>
						{
							this.state.isEdit ? <Edit keywords={this.state.keywords} select={this.select.bind(this)} unselect={this.unselect.bind(this)} selected={this.state.selected} changeKeyword={this.changeKeyword.bind(this)} /> : ( <ul className="keywords">{keywordsComp}{this.state.keywords.length > this.MAX_SHOW ? <li className="keyword more">+ more</li> : "" }</ul> )
						}
					</section>
					<div className={ "actions " + (this.state.isEdit ? "is-edit" : "") }>
						{ !this.state.isEdit ? <a className="view" onClick={ this.edit.bind(this)}>View {this.state.keywords.length} Recommendations</a> : (
							<a className="view" onClick={ this.unedit.bind(this)}>Go Back</a>
							) }
						<a className={"apply " + (this.state.applying ? "is-applying" : "" )} onClick={this.apply.bind(this)}>
							{
								this.state.applying ? "Applying..." : (
									!this.state.isEdit ? "Apply All" : `Apply ${this.state.selected.length} selected`
								)
							}
						</a>
					</div>
				</div>
			</div>
		)
	}
}

const Edit = ({ keywords, changeKeyword, selected, select, unselect }) => {
	let options = [
		{
			name : "Exact",
			value : 2
		},
		{
			name : "Phrase",
			value : 3
		},
		{
			name : "Broad",
			value : 4
		}
	]
	
	let k = keywords.map( ( keyword, index ) => {
		return(
			<tr key={index}>
				<td><input type="checkbox" onChange={ e => e.target.checked ? select(index) : unselect(index) } checked={ selected.includes(index) }/></td>
				<td>{keyword.keyword}</td>
				<td>{( parseFloat(keyword.cpc)/1000000 ).toLocaleString( undefined, { style : "currency", currency : MUZAARA.currency})}</td>
				<td>
					<Select options={ options } value={keyword.type} onChange={changeKeyword.bind(this, index)} />
				</td>
				<td>
					<span>{keyword.adgroup.name}</span><br />
					<small style={ { color : "#888" } }>{ keyword.campaign.name}</small>
				</td>
			</tr>
		)
	})

	return(
		<table style={ {width:"100%"}}>
			<thead>
				<tr>
					<th>
						<input type="checkbox" />
					</th>
					<th>Keyword</th>
					<th>Bid</th>
					<th>Match Type</th>
					<th>Ad Group</th>
				</tr>
			</thead>

			<tbody>
				{k}
			</tbody>
		</table>
	)
}