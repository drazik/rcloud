'use strict';

var $ = require('jquery');

function Notification(options) {
    this.options = $.extend({}, this.options, options);

    this.init();
}

Notification.prototype.options = {
    wrapper: document.body,
    message: 'Hello world',
    type: 'error',
    ttl: 2000,
    onClose: function() { return false; },
    onOpen: function() { return false; }
};

Notification.prototype.init = function() {
    this.notification = document.createElement('div');
    this.notification.className = 'notification notification-' + this.options.type;

    var inner = document.createElement('div');
    inner.className = 'notification-inner';
    inner.innerHTML = this.options.message;

    var closeBtn = document.createElement('button');
    closeBtn.className = 'notification-close';

    inner.appendChild(closeBtn);

    this.notification.appendChild(inner);

    this.options.wrapper.insertBefore(this.notification, this.options.wrapper.firstChild);

    this.dismissTTL = setTimeout(function() {
        if (this.active) {
            this.dismiss();
        }
    }.bind(this), this.options.ttl);

    this.initEvents();
};

Notification.prototype.initEvents = function() {
    this.notification.querySelector('.notificaiton-close').addEventListener('click', this.dismiss.bind(this));
};

Notification.prototype.show = function() {
    this.active = true;
    this.notification.classList.remove('notification-hide');
    this.notification.classList.add('notification-show');
    this.options.onOpen();
};

Notification.prototype.dismiss = function() {
    this.active = false;
    clearTimeout(this.dismissTTL);

    this.notification.classList.remove('notification-show');
    setTimeout(function() {
        this.notification.classList.add('notification-add');
    }.bind(this), 25);

    // Remove this.notification from the DOM on animation end
};

module.exports = Notification;