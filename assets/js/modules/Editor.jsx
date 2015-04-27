'use strict';

var React = require('react');
var ace = require('brace');
require('brace/mode/javascript');
require('brace/theme/monokai');

var Editor = React.createClass({
	componentDidMount: function() {
		this.editor = ace.edit('editor-field');
		this.editor.getSession().setMode('ace/mode/javascript');
		this.editor.setTheme('ace/theme/monokai');
	},

	render: function() {
		return (
			<div className="editor-editor">
				<textarea id="editor-field" value={this.props.script} onChange={this.props.onChange}></textarea>
			</div>
		);
	}
});

module.exports = Editor;