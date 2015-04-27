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

		this.editor.getSession().on('change', function(event) {
			this.props.onChange(event, this.editor.getSession().getValue());
		}.bind(this));
	},

	render: function() {
		return (
			<div className="editor-editor">
				<textarea id="editor-field"></textarea>
			</div>
		);
	}
});

module.exports = Editor;