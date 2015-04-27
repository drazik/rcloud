'use strict';

var React = require('react');
var EditorMenu = require('./EditorMenu.jsx');
var Editor = require('./Editor.jsx');
var EditorResult = require('./EditorResult.jsx');

var EditorApp = React.createClass({
	handleRunClick: function() {
		var xhr = new XMLHttpRequest();
		var params = 'script=' + this.state.script;
		var response;

		xhr.open('POST', '/script/run', true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					response = JSON.parse(xhr.responseText);

					this.setState({
						result: response.result
					});
				} else {
					console.log('Error : ' + xhr.responseText);
				}
			}
		}.bind(this);

		xhr.send(params);
	},

	handleSaveClick: function() {
		console.log('Saving the script...');
	},

	handleScriptChange: function(event, content) {
		this.setState({
			script: content
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
				<EditorMenu handleRunClick={this.handleRunClick} handleSaveClick={this.handleSaveClick} />
				<Editor script={this.state.script} onChange={this.handleScriptChange} />
				<EditorResult result={this.state.result} graphs={this.state.graphs} />
			</div>
		);
	}
});

module.exports = EditorApp;