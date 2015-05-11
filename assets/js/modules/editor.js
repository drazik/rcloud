'use strict';

var $ = require('jquery');
var ace = require('brace');
require('brace/mode/r');
require('brace/theme/monokai');

var Editor = function Editor() {
    this.editor = ace.edit('editor-field');
    this.$container = $('#editor');
    this.$runButton = this.$container.find('#run-button');
    this.$saveButton = this.$container.find('#save-button');
    this.$result = this.$container.find('.editor-result');
    this.$graphs = this.$container.find('.editor-graphs'); // écrire ou utiliser un plugin de galerie d'image

    this.initialize();
    this.initEvents();
};

Editor.prototype.initialize = function() {
    this.editor.getSession().setMode('ace/mode/r');
    this.editor.setTheme('ace/theme/monokai');
};

Editor.prototype.initEvents = function() {
    this.$runButton.click(this.run.bind(this));
    this.$saveButton.click(this.save.bind(this));
};

Editor.prototype.run = function(event) {
    $.ajax({
        url: window.urls.script.run,
        type: 'POST'
    }).done(function() {

    }).fail(function() {

    });
};

Editor.prototype.save = function(event) {
    $.ajax({
        url: window.urls.script.save,
        type: 'POST'
    }).done(function() {

    }).fail(function() {

    });
};

module.exports = Editor;