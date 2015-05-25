'use strict';

var $ = require('jquery');

require('browsernizr/test/css/animations');
var prefixed = require('browsernizr/lib/prefixed');
var Modernizr = require('browsernizr');

var animEndEventNames = {
    'WebkitAnimation': 'webkitAnimationEnd',
    'OAnimation': 'oAnimationEnd',
    'msAnimation': 'MSAnimationEnd',
    'animation': 'animationend'
};
var animEndEventName = animEndEventNames[Modernizr.prefixed('animation')];

function Notification(options) {
    this.options = $.extend({}, this.options, options);

    this.init();
}

Notification.prototype.options = {
    wrapper: document.body,
    message: 'Hello world',
    type: 'info',
    ttl: 0,
    onClose: function() { return false; },
    onOpen: function() { return false; }
};

Notification.prototype.init = function() {
    this.notification = document.createElement('div');
    this.notification.className = 'notification notification-hide notification-' + this.options.type;

    var inner = document.createElement('div');
    inner.className = 'notification-inner';
    inner.innerHTML = this.options.message;

    var closeBtn = document.createElement('button');
    closeBtn.className = 'notification-close';
    closeBtn.innerHTML = 'Fermer';

    inner.appendChild(closeBtn);

    this.notification.appendChild(inner);

    this.options.wrapper.insertBefore(this.notification, this.options.wrapper.firstChild);

    this.show();

    if (this.options.ttl > 0) {
        this.dismissTTL = setTimeout(function() {
            if (this.active) {
                this.dismiss();
            }
        }.bind(this), this.options.ttl);
    }

    this.initEvents();
};

Notification.prototype.initEvents = function() {
    this.notification.querySelector('.notification-close').addEventListener('click', this.dismiss.bind(this));
};

Notification.prototype.show = function() {
    this.active = true;
    this.notification.classList.remove('notification-hide');
    this.notification.classList.add('notification-show');
    this.options.onOpen();
};

Notification.prototype.dismiss = function() {
    this.active = false;

    if (this.dismissTTL) {
        clearTimeout(this.dismissTTL);
    }

    this.notification.classList.remove('notification-show');
    setTimeout(function() {
        this.notification.classList.add('notification-hide');

        this.options.onClose();
    }.bind(this), 25);

    // Remove this.notification from the DOM on animation end
    var onEndAnimationFn = function(event) {
        if (Modernizr.cssanimations) {
            this.notification.removeEventListener(animEndEventName, onEndAnimationFn);
        }

        this.options.wrapper.removeChild(this.notification);
    };

    if (Modernizr.cssanimations) {
        this.notification.addEventListener(animEndEventName, onEndAnimationFn.bind(this));
    } else {
        onEndAnimationFn();
    }
};

module.exports = Notification;