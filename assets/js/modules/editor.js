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
        dataType: 'json',
        data: {
            script: this.editor.session.getTextRange(this.editor.getSelectionRange()) || this.editor.getValue()
        },
        type: 'POST',
        url: window.urls.editor.run,
        beforeSend: function(jqXHR, settings) {
            this.$result.empty();
            this.$result.append($('<img src="' + window.urls.general.images + 'ajax-loader.gif" alt="Chargement" />'));
        }.bind(this)
    }).done(function(data) {
        this.$result.empty();
        this.$result.html(data.result);
    }.bind(this)).fail(function(jqXHR, textStatus, errorThrown) {
        this.$result.empty();
        this.$result.html(textStatus);
    }.bind(this));
};

Editor.prototype.save = function(event) {
    $.ajax({
        dataType: 'json',
        data: {},
        url: window.urls.script.save,
        type: 'POST'
    }).done(function(data) {

    }).fail(function(jqXHR, textStatus, errorThrown) {

    });
};

module.exports = Editor;