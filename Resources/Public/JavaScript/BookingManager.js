var BOOKINGMANAGER = BOOKINGMANAGER || {};

BOOKINGMANAGER.AJAX = {

    ajaxLinks: null,
    link: null,
    url: null,
    container: null,

    init: function () {
        this.initElements();
        this.initEvents();
    },

    initElements: function () {
        this.ajaxLinks = $('a.bw_bookingmanager__ajaxLink');
    },

    initEvents: function () {
        var self = this;

        $(self.ajaxLinks).on('click', function (e) {
            e.preventDefault();
            self.onAjaxLinkClick(this);
        });

        if (BOOKINGMANAGER.LOAD_THIRD_LINK) self.loadThirdLink();
    },

    onAjaxLinkClick: function (link) {

        var self = this;

        self.link = $(link);

        // abbort if link was already clicked
        if (self.link.hasClass('active')) return;

        var url = $(link).attr('href');
        self.container = $('#' + $(link).attr('data-ajax-container'));

        $(self.container).addClass('loading');

        // actual request
        $.get(url, self.onAjaxSucces.bind(self))
            .fail(self.onAjaxFail.bind(self))
            .done(self.afterAjaxCall.bind(self));
    },

    onAjaxSucces: function (data) {
        this.container.replaceWith(data);
        this.container.removeClass('loading');
        this.ajaxLinks.removeClass('active');
        this.link.addClass('active');
    },

    onAjaxFail: function () {
        console.error('Ajax get request failed.');
    },

    afterAjaxCall: function () {
        if (BOOKINGMANAGER.LOAD_THIRD_LINK) this.init();
    },

    loadThirdLink: function () {
        // load the third link (1,2 are month navigation)
        var firstLink = this.ajaxLinks[2] || false;
        if (firstLink) this.onAjaxLinkClick(firstLink);

        BOOKINGMANAGER.LOAD_THIRD_LINK = false;
    }

}

$(function () {
    BOOKINGMANAGER.AJAX.init();
});