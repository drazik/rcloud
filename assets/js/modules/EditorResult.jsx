'use strict';

var React = require('react');

var EditorResult = React.createClass({
	render: function() {
		return (
			<div className="editor-result">
				<div className="editor-result-result" dangerouslySetInnerHTML={{__html: this.props.result}}></div>
				<div className="editor-result-graphs">
					{this.props.graphs.map(function(graph) {
						return (
							<img src={graph} alt="" />
						);
					})}
				</div>
			</div>
		);
	}
});

module.exports = EditorResult;