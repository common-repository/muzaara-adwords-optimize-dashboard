import React from "react"
import "whatwg-fetch"
import "./css/index.css"
import {getForm} from "../utils"
import Keywords from "./keywords"
import Sitelinks from "./sitelinks"

export default class App extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			recommendations : {},
			loading : true
		}

		this.fetchRecommendations = this.fetchRecommendations.bind(this)
		this.interval = null;
	}

	componentDidMount() {
		this.fetchRecommendations()
		//this.interval = setInterval(this.fetchRecommendations, 1000*30)
	}

	componentWillUnmount() {
		clearInterval(this.interval)
	}

	fetchRecommendations() {
		this.setState({ loading : true }, () => {
			fetch( MUZAARA.ajax, {
				method : "POST",
				body : getForm({ action : "muzaara_get_recommendations" }),
				credentials : "same-origin"
			})
			.then( response => response.json() )
			.then( json => {
				if( json.status ) {
					this.setState({ recommendations : json.data, loading : false })
				}
				else
					this.setState({ loading : false })
			})
		})
	}

	render() {
		return(
			<div style={{position:"relative"}} className="">
			<h3 className="recommendation-title">Recommendations</h3>
				{
					Object.keys(this.state.recommendations).length ? (
						<div className="muzaara-recommendations row">
							{ typeof this.state.recommendations.KEYWORD !== "undefined" ? <Keywords keywords={this.state.recommendations.KEYWORD } /> : "" }
							{ typeof this.state.recommendations.SITELINK_EXTENSION !== "undefined" ? <Sitelinks links={this.state.recommendations.SITELINK_EXTENSION } /> : "" }
						</div>
					) : (
						this.state.loading ? "" : <NoRecommendations />
					)
				}
				
			{this.state.loading ? <div className="loading-reports"></div> : ""}
			</div>
		)
	}
}

const NoRecommendations = ({}) => {
	return(
		<div style={ { textAlign : "center", fontSize : "16px" } }>
			<span className="icon-settings is-loading" style={ { "fontSize" : "48px" } }></span> <br /><br />
			Hold on tight, we are still collecting and crunching your data. Please check back soon for recommendations
		</div>
	)
}