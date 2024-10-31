import React from "react"
import "./css/sitelinks.css";
import "whatwg-fetch"
import jsonToFormData from "json-form-data"

export default class Sitelinks extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			links : this.props.links,
			isEdit : false,
			selected : {},
			applying : false
		}

		this.MAX_SHOW = 3
		this.unSelectEdit = this.unSelectEdit.bind(this)
	}

	edit(e) {
		e.preventDefault()

		this.setState({ isEdit : true })
	}

	unedit(e) {
		e.preventDefault()
		this.setState({ isEdit :false})
	}

	onChange( parentIndex, index, name, e ) {
		let links = [...this.state.links]
	
		if( typeof links[parentIndex][index] !== "undefined" ) {
			if( name == "final_url" ) {
				links[parentIndex][index][name][0] = e.target.value
			}
			else 
				links[parentIndex][index][name] = e.target.value
		}

		this.setState({links}, this.selectEdit.bind(this, parentIndex, index))
	}

	selectEdit( parentIndex, index ) {
		let links = [...this.state.links]
		let selected = Object.assign({}, this.state.selected )

		if( typeof selected[parentIndex] !== "undefined" ) {
			if( !selected[parentIndex].includes(index) )
				selected[parentIndex].push(index)
		}
		else 
			selected[parentIndex] = [index]

		this.setState({selected})
	}

	unSelectEdit(parentIndex,index) {
		if(typeof this.state.selected[parentIndex] !== "undefined") {
			let selected = Object.assign({},this.state.selected)
			selected[parentIndex] = selected[parentIndex].filter( i => i !== index )
			this.setState({ selected})
		}
	}

	toggleSelect( parentIndex, index ) {
		if( typeof this.state.selected[parentIndex] !== "undefined" && this.state.selected[parentIndex].includes(index) ) {
			this.unSelectEdit(parentIndex,index)
		}
		else 
			this.selectEdit(parentIndex,index)
	}

	apply() {
		if( this.state.applying )
			return;

		this.setState({ applying : true}, () => {
			let selectedLinks=[]
			for( let select in this.state.selected ) {
				let l = this.state.selected[select].map( ( index ) => {
					return this.state.links[select][index]
				})
				selectedLinks.push({
					links : l,
					resource : this.state.links[select].resource
				})
			}
			
			fetch( MUZAARA.ajax, {
				method : "POST",
				credentials : "same-origin",
				body : jsonToFormData({ action : "muzaara_apply", type : "sitelink", recommendations : selectedLinks }),
			}) 
			.then( response => response.json() )
			.then( json => {
				if( json.status) {
					this.setState({ isEdit:false, applying:false})
				}
			})
		})
	}

	render() {
		let recommendations = [...this.state.links]
		// recommendations.splice(this.MAX_SHOW)
		let links = recommendations.map( (links,index) => {
			return (
				<ul className="sitelinks" key={index}>
					<li className="sitelink campaign" ><strong>{links.campaign.name}</strong></li>
					<li className="sitelink">{links[0].text}</li>
					<li className="sitelink">{links[1].text}</li>
					<li className="sitelink">More...</li>
				</ul>
			)
		})

		let appliedCount =0;
		for(let selected in this.state.selected)
			appliedCount+=this.state.selected[selected].length

		return(
			<div className="">
				<div className="recommendation sitelink_ext">
					<header>
						Add sitelinks to your ads
					</header>

					<p>Your ads aren’t as prominent as they could be if you used sitelinks, which can improve your CTR  </p>
					<span className="info">Recommended because similar advertisers use sitelinks </span>

					<section>
						{
							this.state.isEdit ? <Edit links={this.state.links} toggleSelect={this.toggleSelect.bind(this)} selected={this.state.selected} change={ this.onChange.bind(this) } /> : links
						}
					</section>

					<div className={ "actions " + (this.state.isEdit ? "is-edit" : "") }>
						{ !this.state.isEdit ? <a className="view" onClick={ this.edit.bind(this)}>View Recommendations</a> : (
							<a className="view" onClick={ this.unedit.bind(this)}>Go Back</a>
							) }

						{this.state.isEdit ? (
							<a className={ "apply " + (this.state.applying ? "is-applying" : '' )} onClick={this.apply.bind(this)}>
								{ this.state.applying ? "Applying..." : `Apply ${appliedCount} selected` }
							</a>
						) : "" }
						
					</div>
				</div>
			</div>
		)
	}
}

const Edit = ({links, change, selected, toggleSelect}) => {
	let sitelinksCampaigns = []
	for(let i=0; i < links.length; i++) {
		let count = 1;
		let sitelinks=[]
		for( let index in links[i] ) {
			if( isNaN(index) )
				continue;
			let link = links[i][index]
			sitelinks.push( 
				<div className={`edit-sitelink ${(typeof selected[i] !== "undefined" && selected[i].includes(index) ? 'selected' : '')}`} onClick={toggleSelect.bind(this,i,index)} key={count}>
					<strong>Sitelink {count++}</strong>
					<label>Text</label>
					<input type="text" onClick={e => e.stopPropagation()} value={link.text} onChange={ change.bind(this, i, index, "text" )} />
					<label>Final URL</label>
					<input type="url" onClick={e => e.stopPropagation()}  onChange={change.bind(this, i, index, "final_url")} value={link.final_url.length ? link.final_url[0] : ""} />
					<label>Description line 1</label>
					<input type="text" onClick={e => e.stopPropagation()}  onChange={change.bind(this,i,index,"line1")} value={link.line1} />
					<label>Description line 2</label>
					<input type="text" onClick={e => e.stopPropagation()}  onChange={change.bind(this,i,index,"line2")} placeholder="Description line 2" value={link.line2} />
				</div>
			)
		}

		sitelinksCampaigns.push(
			<div className="edit-sitelinks">
				<div style={ { padding : "10px", fontSize : "1.1em" } }>Add sitelinks to <strong>{links[i].campaign.name}</strong></div>
				{sitelinks}
			</div>
		)
	}

	return(
		<div>
			{sitelinksCampaigns}
		</div>
	)
}