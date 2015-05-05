'use strict';

var React = require('React');

var EditorMenu = React.createClass({
	render: function() {
		return (
			<div className="editor-menu">
				<div className="editor-menu-left">
					<button className="editor-menu-item" onClick={this.props.handleRunClick} type="button">Run</button>
					<button className="editor-menu-item" onClick={this.props.handleSaveClick} type="button">Save</button>
				</div>
				<div className="editor-menu-right">
					<a href="/" className="editor-menu-item">Dashboard</a>
				</div>
			</div>
		);
	}
});

module.exports = EditorMenu;