
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
    console.log('post json');
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
