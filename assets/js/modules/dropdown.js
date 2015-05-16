'use strict';

var $ = require('jquery');

function Dropdown(element) {
    this.$container = $(element);

    this.initEvents();
}

Dropdown.prototype.initEvents = function() {
    this.$container.on('click', '.dropdown-toggle', this.toggle.bind(this));
};

Dropdown.prototype.toggle = function(event) {
    this.$container.toggleClass('is-open');
};

$.fn.dropdown = function(options) {
    return this.each(function () {
        var $this = $(this);
        var data = $this.data('dropdown');

        if (!data) {
            $this.data('dropdown', new Dropdown(this));
        }
    });
};