'use strict';

var $ = require('jquery');
var arrayToObject = require('../helpers/arrayToObject');
var getCurrentDate = require('../helpers/getCurrentDate');

function NewScript() {
    this.$form = $('#new-script-form');
    this.$modal = $('#new-script-modal');
    this.$list = $('#scripts-list tbody');

    this.initEvents();
}

NewScript.prototype.initEvents = function() {
    this.$form.on('submit', this.handleSubmit.bind(this));
};

NewScript.prototype.handleSubmit = function(event) {
    event.preventDefault();

    $.ajax({
        data: arrayToObject(this.$form.serializeArray()),
        type: 'POST',
        url: window.urls.editor.save,
        beforeSend: function(jqXHR, settings) {

        }
    }).done(this.success.bind(this)).fail(this.error.bind(this));
};

NewScript.prototype.success = function(response) {
    this.$modal.data('modal').close();
    this.$modal.find('input[name="name"]').val('');
    this.newScript(response.data);
};

NewScript.prototype.newScript = function(script) {
    var $tr = $('<tr />');

    var $tdName = $('<td />');
    var $aName = $('<a target="_blank">' + script.scriptName + '</a>');
    $aName.attr('href', script.editHref);
    $aName.attr('title', 'Editer ' + script.scriptName);

    $tdName.append($aName);
    $tr.append($tdName);

    var $tdDate = $('<td>' + getCurrentDate() + '</td>');
    $tr.append($tdDate);

    var $tdEdit = $('<td class="scripts-list-action" />');
    var $aEdit = $('<a target="_blank"><i class="fa fa-edit"></i></a>');
    $aEdit.attr('href', script.editHref);
    $tdEdit.append($aEdit);
    $tr.append($tdEdit);

    var $tdShare = $('<td class="scripts-list-action" />');
    var $aShare = $('<a><i class="fa fa-share-square-o"></i></a>');
    $aShare.attr('href', script.shareHref);
    $tdShare.append($aShare);
    $tr.append($tdShare);

    var $tdRemove = $('<td class="scripts-list-action" />');
    var $aRemove = $('<a><i class="fa fa-trash-o"></i></a>');
    $aRemove.attr('href', script.removeHref);
    $tdRemove.append($aRemove);
    $tr.append($tdRemove);

    this.$list.append($tr);

    window.open(script.editHref);
};

NewScript.prototype.error = function(jqXHR, status, errorThrown) {
    console.log(status, errorThrown);
};

module.exports = NewScript;
