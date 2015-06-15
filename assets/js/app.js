'use strict';

var $ = require('jquery');
var Editor = require('./modules/editor');
var arrayToObject = require('./helpers/arrayToObject');
var NewFolder = require('./modules/new-folder');
require('./modules/dropdown');
require('./modules/modal');

$(document).ready(function() {
    new Editor();
    new NewFolder();
    $('.dropdown').dropdown();
    $('.modal').modal();
});
