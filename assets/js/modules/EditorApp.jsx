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
		var xhr = new XMLHttpRequest();
		var params = 'scriptContent=' + this.state.script;
		var scriptName = '';
		var response;

		if (React.findDOMNode(this.refs.scriptId).value !== '') {
			params += '&scriptId=' + React.findDOMNode(this.refs.scriptId).value;
		} else {
			while (scriptName === '') {
				scriptName = prompt('Nom du script : ');
			}

			params += '&scriptName=' + scriptName;
		}

		if (this.state.scriptId || scriptName) {
			xhr.open('POST', '/script/save', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4) {
					if (xhr.status === 200) {
						response = JSON.parse(xhr.responseText);
						if (response.meta.code === 201) {
							history.pushState({}, 'editor', '/editor/' + response.data.scriptId);
							this.setState({
								scriptId: response.data.scriptId
							});
						} else if (response.meta.code === 200) {
							console.log('saved');
							// ajouter une notification
						}
					} else {
						console.log('Error : ' + xhr.responseText);
						// ajouter une notification
					}
				}
			}.bind(this);

			xhr.send(params);
		}
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
			graphs: [],
			scriptId: null,
			scriptName: null
		};
	},

	render: function() {
		return (
			<div id="editor">
				<input type="hidden" value={this.state.scriptId} ref="scriptId" />
				<EditorMenu handleRunClick={this.handleRunClick} handleSaveClick={this.handleSaveClick} />
				<Editor script={this.state.script} onChange={this.handleScriptChange} />
				<EditorResult result={this.state.result} graphs={this.state.graphs} />
			</div>
		);
	}
});

module.exports = EditorApp;