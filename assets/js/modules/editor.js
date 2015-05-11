'use strict';

var $ = require('jquery');
var ace = require('brace');
require('brace/mode/r');
require('brace/theme/monokai');

var Editor = function Editor() {
    this.editor = ace.edit('editor-field');
    this.$runButton = $('#run-button');
    this.$saveButton = $('#save-button');

    this.initialize();
    this.initEvents();
};

Editor.prototype.initialize = function() {
    this.editor.getSession().setMode('ace/mode/r');
    this.editor.setTheme('ace/theme/monokai');
};

Editor.prototype.initEvents = function() {
    this.$runButton.click(function() {});
    this.$saveButton.click(function() {});
};

module.exports = Editor;