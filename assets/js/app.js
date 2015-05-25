'use strict';

var $ = require('jquery');
var Editor = require('./modules/editor');
var Notification = require('./modules/notification');
require('./modules/dropdown');

$(document).ready(function() {
    new Editor();

    new Notification();

    $('.dropdown').dropdown();
});