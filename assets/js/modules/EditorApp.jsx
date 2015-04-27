'use strict';

var React = require('react');
var EditorMenu = require('./EditorMenu.jsx');
var Editor = require('./Editor.jsx');
var EditorResult = require('./EditorResult.jsx');

var EditorApp = React.createClass({
	handleRunClick: function() {
		var xhr = new XMLHttpRequest();

		// xhr.open('GET', 'http://localhost:8000');
		// xhr.onreadystatechange = function() {
		// 	var response;

		// 	if (xhr.readyState === 4) {
		// 		if (xhr.status === 200) {
		// 			response = JSON.parse(xhr.responseText);

		// 			this.setState({
		// 				result: response.result,
		// 				graphs: result.graphs
		// 			});
		// 		} else {
		// 			console.log('Error : ' + xhr.responseText);
		// 		}
		// 	}
		// }.bind(this);

		// xhr.send(null);
	},

	handleSaveClick: function() {
		console.log('Saving the script...');
	},

	handleShareClick: function() {
		console.log('Saving this scripts...');
	},

	handleScriptChange: function(event) {
		this.setState({
			script: event.target.value
		});
	},

	getInitialState: function() {
		return {
			script: '',
			result: '',
			graphs: []
		};
	},

	render: function() {
		return (
			<div id="editor">
				<EditorMenu handleRunClick={this.handleRunClick} handleSaveClick={this.handleSaveClick} handleShareClick={this.handleShareClick} />
				<Editor script={this.state.script} onChange={this.handleScriptChange} />
				<EditorResult result={this.state.result} graphs={this.state.graphs} />
			</div>
		);
	}
});

module.exports = EditorApp;