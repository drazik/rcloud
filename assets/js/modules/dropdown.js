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
    if (!this.$container.hasClass('is-open')) {
        clearDropdowns();
    }

    this.$container.toggleClass('is-open');
};

Dropdown.prototype.close = function() {
    this.$container.removeClass('is-open');
};

function clearDropdowns(e) {
    if (e && e.which === 3) {
        return;
    }

    $('.dropdown').each(function() {
        var $this = $(this);
        var dropdown = $this.data('dropdown');

        if (e && this === e.target.parentNode) {
            return;
        }

        if (dropdown) {
            dropdown.close();
        }
    });
}

$.fn.dropdown = function(options) {
    return this.each(function () {
        var $this = $(this);
        var data = $this.data('dropdown');

        if (!data) {
            $this.data('dropdown', new Dropdown(this));
        }
    });
};

$(document).on('click', clearDropdowns);