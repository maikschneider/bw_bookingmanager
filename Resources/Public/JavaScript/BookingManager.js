var BOOKINGMANAGER = BOOKINGMANAGER || {};

BOOKINGMANAGER.AJAX = {

    ajaxLinks: null,
    link: null,
    url: null,
    container: null,

    init: function(){
        this.initElements();
        this.initEvents();
    },

    initElements: function(){
        this.ajaxLinks = $('a.bw_bookingmanager__ajaxLink');
    },

    initEvents: function(){
        var self = this;

        $(self.ajaxLinks).on('click', function(e){
            e.preventDefault();
            self.onAjaxLinkClick(this);
        });
    },

    onAjaxLinkClick: function(link){

        var self = this;

        self.link = $(link);

        // abbort if link was already clicked
        if(self.link.hasClass('active')) return;

        var url = $(link).attr('href');
        self.container = $('#'+$(link).attr('data-ajax-container'));

        $(self.container).addClass('loading');

        // actual request
        $.get(url, self.onAjaxSucces.bind(self)).fail(self.onAjaxFail);
    },

    onAjaxSucces: function(data){
        this.container.html(data);
        this.container.removeClass('loading');
        this.ajaxLinks.removeClass('active');
        this.link.addClass('active');
    },

    onAjaxFail: function(){
        console.error('Ajax get request failed.');
    }

}

$(function () {
    BOOKINGMANAGER.AJAX.init();
});