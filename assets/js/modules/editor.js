'use strict';

var $ = require('jquery');
var ace = require('brace');
var Notification = require('./notification');

require('brace/mode/r');
require('brace/theme/monokai');

var Editor = function Editor() {
    if (!document.getElementById('editor-field')) {
        return;
    }

    this.editor = ace.edit('editor-field');
    this.$container = $('#editor');
    this.$runButton = $('#run-script');
    this.$saveButton = $('#save-script');
    this.$result = this.$container.find('.editor-result');
    this.$graphs = this.$container.find('.editor-graphs');

    this.script = null;

    this.initialize();
    this.initEvents();
};

Editor.prototype.initialize = function() {
    this.editor.getSession().setMode('ace/mode/r');
    this.editor.setTheme('ace/theme/monokai');

    var $editorScript = this.$container.find('#editor-script');
    this.script = JSON.parse($editorScript.val());
    $editorScript.remove();
};

Editor.prototype.initEvents = function() {
    this.$runButton.click(this.run.bind(this));
    this.$saveButton.click(this.save.bind(this));
    this.editor.on('change', this.handleEditorChange.bind(this));
};

Editor.prototype.run = function(event) {
    $.ajax({
        data: {
            script: this.getScriptContent(true)
        },
        type: 'POST',
        url: window.urls.editor.run,
        beforeSend: function(jqXHR, settings) {
            this.$result.empty();
            this.$result.append($('<img src="' + window.urls.general.images + 'ajax-loader.gif" alt="Chargement" />'));
        }.bind(this)
    }).done(function(data) {
        this.$result.empty();
        this.$result.html(data);
    }.bind(this)).fail(function(jqXHR, textStatus, errorThrown) {
        this.$result.empty();
        this.$result.html(textStatus);
    }.bind(this));
};

Editor.prototype.save = function(event) {
    if (!this.script.id) {
        do {
            this.script.name = prompt('Nom du script');
        } while (this.script.name === '');

        if (!this.script.name) {
            return;
        }
    }

    $.ajax({
        dataType: 'json',
        data: this.script,
        url: window.urls.editor.save,
        type: 'POST'
    }).done(function(data) {
        this.script.id = data.data.scriptId;

        if (data.meta.code === 201) {
            window.history.pushState({}, '', '/editor/' + data.data.scriptId);
        }
    }.bind(this)).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus);
    }.bind(this));
};

Editor.prototype.handleEditorChange = function(event) {
    this.script.content = this.getScriptContent(false);
};

Editor.prototype.getScriptContent = function(selectionOnly) {
    if (selectionOnly) {
        return this.editor.session.getTextRange(this.editor.getSelectionRange()) || this.editor.getValue();
    }

    return this.editor.getValue();
};

module.exports = Editor;