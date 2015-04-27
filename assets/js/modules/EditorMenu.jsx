'use strict';

var React = require('React');

var EditorMenu = React.createClass({
	render: function() {
		return (
			<div className="editor-menu">
				<button className="editor-menu-item" onClick={this.props.handleRunClick} type="button">Run</button>
				<button className="editor-menu-item" onClick={this.props.handleSaveClick} type="button">Save</button>
				<button className="editor-menu-item" onClick={this.props.handleShareClick} type="button">Share</button>
			</div>
		);
	}
});

module.exports = EditorMenu;