
ajax = function(url, data, method, dataType) {
    method = typeof method === 'undefined' ? 'POST' : method;
    return $.ajax({
        url: site.url + url,
        data: data,
        method: method,
        dataType: dataType,
        headers: {
            'X-CSRF-Token': $('meta[name=_token]').attr('content')
        }
    });
};

ajaxPostJson = function(url, data) {
    data = typeof data === 'undefined' ? {} : data;
    return ajax(url, data, 'post', 'json');
};


ajaxGetJson = function(url, data) {
    data = typeof data === 'undefined' ? {} : data;
    return ajax(url, data, 'get', 'json');
};

/**
 * Shows Pop Up
 * @type {{$el: (*|jQuery|HTMLElement), defaultSpeed: number, waitingTime: number, show: infoPopUp.show, hide: infoPopUp.hide}}
 */
var infoPopUp = {
    $el : $('.info'),
    defaultSpeed: 300,
    waitingTime: 5000,
    show: function(type, msg) {
        if(type == 'success') {
            this.$el.removeClass('error').addClass('success');
        } else if(type == 'error') {
            this.$el.removeClass('success').addClass('error');
        } else {
            return;
        }

        this.$el.html(msg);
        this.$el.animate({bottom: '50px'}, this.defaultSpeed);

        // auto hide
        window.setTimeout(function() {
            this.hide()
        }.bind(this), this.waitingTime);
    },
    hide: function()
    {
        this.$el.animate({bottom: '-50px'}, this.defaultSpeed);
    }
};

function utf8Decode(utf8String) {
    if (typeof utf8String != 'string') throw new TypeError('parameter ‘utf8String’ is not a string');
    // note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
    const unicodeString = utf8String.replace(
        /[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,  // 3-byte chars
        function(c) {  // (note parentheses for precedence)
            var cc = ((c.charCodeAt(0)&0x0f)<<12) | ((c.charCodeAt(1)&0x3f)<<6) | ( c.charCodeAt(2)&0x3f);
            return String.fromCharCode(cc); }
    ).replace(
        /[\u00c0-\u00df][\u0080-\u00bf]/g,                 // 2-byte chars
        function(c) {  // (note parentheses for precedence)
            var cc = (c.charCodeAt(0)&0x1f)<<6 | c.charCodeAt(1)&0x3f;
            return String.fromCharCode(cc); }
    );
    return unicodeString;
}


Vue.filter('decodeUTF8', function(value) {
    return  utf8Decode(value);
});

