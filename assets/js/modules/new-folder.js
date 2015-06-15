'use strict';

var $ = require('jquery');
var arrayToObject = require('../helpers/arrayToObject');

function NewFolder() {
    this.$form = $('#new-folder-form');
    this.$modal = $('#new-folder-modal');
    this.$list = $('#folders-list');

    this.initEvents();
}

NewFolder.prototype.initEvents = function() {
    this.$form.on('submit', this.handleSubmit.bind(this));
};

NewFolder.prototype.handleSubmit = function(event) {
    event.preventDefault();

    $.ajax({
        data: {
            folder: arrayToObject(this.$form.serializeArray())
        },
        type: 'POST',
        url: window.urls.folder.add,
        beforeSend: function(jqXHR, settings) {

        }
    }).done(this.success.bind(this)).fail(this.error.bind(this));
};

NewFolder.prototype.success = function(response) {
    this.$modal.data('modal').close();
    this.newFolder(response.data);
};

NewFolder.prototype.newFolder = function(folder) {
    var $li = $('<li class="folder" />');
    var $a = $('<a href="' + folder.href + '" />');
    var $ul = $('<ul class="folder-infos" />');
    $ul.append($('<li class="folder-name">' + folder.name + '</li>'));
    $ul.append($('<li>0 dossier(s)</li>'));
    $ul.append($('<li>0 scripts(s)</li>'));

    $a.append($ul);
    $li.append($a);
    this.$list.append($li);
};

NewFolder.prototype.error = function(jqXHR, status) {
    conosle.log(status);
};

module.exports = NewFolder;
