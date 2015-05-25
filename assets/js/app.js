'use strict';

var $ = require('jquery');
var Editor = require('./modules/editor');
require('./modules/dropdown');

$(document).ready(function() {
    new Editor();

    $('.dropdown').dropdown();
});