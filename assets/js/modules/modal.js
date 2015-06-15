'use strict';

var $ = require('jquery');

function Modal(element) {
    this.$container = $(element);

    this.initEvents();
}

Modal.prototype.initEvents = function() {
    this.$container.on('click', '.modal-close', this.close.bind(this));
};

Modal.prototype.show = function() {
    this.$container.addClass('is-open');
};

Modal.prototype.close = function() {
    this.$container.removeClass('is-open');
};

$.fn.modal = function(options) {
    return this.each(function () {
        var $this = $(this);
        var data = $this.data('modal');

        if (!data) {
            $this.data('modal', new Modal(this));
        }
    });
};

$(document).on('click', '.modal-toggle', function(event) {
    var $this = $(this);
    var $target = $($this.attr('data-target'));

    var modal = $target.data('modal');
    modal.show();
});

$(window).keydown(function(event) {
    if (event.keyCode === 27) {
        closeAllModals();
    }
});

function closeAllModals() {
    var $modals = $('.modal');

    $modals.each(function(index, modal) {
        var $modal = $(modal);
        $modal.data('modal').close();
    });
}
