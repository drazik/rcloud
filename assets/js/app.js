'use strict';

var $ = require('jquery');
var Editor = require('./modules/editor');
require('./modules/dropdown');

$(document).ready(function() {
    new Editor();

    $('.dropdown').dropdown();

    $('#new-folder').on('click', function() {
        $.ajax({
            data: {
                folder: {
                    name: 'test',
                    parentId: 4
                }
            },
            type: 'POST',
            url: window.urls.folder.add,
            beforeSend: function(jqXHR, settings) {

            }
        }).done(function(data) {
            console.log(data);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus);
        });
    });
});
