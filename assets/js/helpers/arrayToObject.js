'use strict';

module.exports = function(arr) {
    var obj = {};

    for (var i = 0; i < arr.length; ++i) {
        obj[arr[i].name] = arr[i].value;
    }

    return obj;
};
