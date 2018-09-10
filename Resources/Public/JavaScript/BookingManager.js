var BOOKINGMANAGER = BOOKINGMANAGER || {};

// register hooks
BOOKINGMANAGER.afterAjaxCallHooks = BOOKINGMANAGER.afterAjaxCallHooks || [];

BOOKINGMANAGER.TIMESELECT = {

  timeslotLinks: null,
  dayDivs: null,

  init: function () {
    this.initElements();
    this.initEvents();
  },

  initElements: function () {
    this.timeslotLinks = $('a.bw_bookingmanager__timeslotsLink');
    this.dayDivs = $('.bw_bookingmanager__dayList__day');
  },

  initEvents: function () {
    var self = this;

    $(self.timeslotLinks).on('click', function (e) {
      e.preventDefault();
      self.onTimeslotLinkClick(this);
    });

    if (BOOKINGMANAGER.LOAD_FIRST_TIMESLOT) self.loadFirstTimeslot();
  },

  onTimeslotLinkClick: function (link) {
    $(this.timeslotLinks).removeClass('active');
    $(link).addClass('active');
    var dayId = $(link).attr('href');
    $(this.dayDivs).hide();
    $(dayId).show();
    $(dayId).find('a').first().trigger('click');
  },

  loadFirstTimeslot: function () {
    // load the third link (1,2 are month navigation)
    var firstLink = this.timeslotLinks[0] || false;
    if (firstLink) this.onTimeslotLinkClick(firstLink);

    BOOKINGMANAGER.LOAD_FIRST_TIMESLOT = false;
  },

}

BOOKINGMANAGER.AJAX = {

  ajaxLinks: null,
  link: null,
  url: null,
  container: null,
  containerName: null,
  replacedHtml: [],
  loadingContainer: '#bw_bookingmanager',

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
    if (BOOKINGMANAGER.LOAD_FIRST_LINK) self.loadFirstLink();
  },

  onAjaxLinkClick: function (link) {

    var self = this;

    self.link = $(link);

    // abbort if link was already clicked
    if (self.link.hasClass('active')) return;

    var url = $(link).attr('href');
    self.containerName = '#' + $(link).attr('data-ajax-container');
    self.container = $(self.containerName);

    $(self.loadingContainer).addClass('loading');

    // actual request
    $.get(url, self.onAjaxSucces.bind(self))
      .fail(self.onAjaxFail.bind(self))
      .done(self.afterAjaxCall.bind(self));
  },

  onAjaxSucces: function (data) {
    var replacedHtml = this.container.replaceWith(data);
    if (Foundation) $(document).foundation();
    this.handleReplacedHtml(replacedHtml);
    $(this.loadingContainer).removeClass('loading').removeClass('error');
    this.ajaxLinks.removeClass('active');
    this.link.addClass('active');
  },

  onAjaxFail: function () {
    $(this.loadingContainer).addClass('error');
    console.error('Ajax get request failed.');
  },

  afterAjaxCall: function () {
    if (BOOKINGMANAGER.LOAD_THIRD_LINK) this.init();
    if (BOOKINGMANAGER.LOAD_FIRST_LINK) this.init();
    if (BOOKINGMANAGER.LOAD_FIRST_TIMESLOT) {
      BOOKINGMANAGER.TIMESELECT.init();
    }
    if(BOOKINGMANAGER.afterAjaxCallHooks.length){
      for(var i=0; i< BOOKINGMANAGER.afterAjaxCallHooks.length; i++){
        BOOKINGMANAGER.afterAjaxCallHooks[i]();
      }
    }
  },

  loadThirdLink: function () {
    // load the third link (1,2 are month navigation)
    // if there is no third link (e.g. whole month is booked out) the second
    // link is clicked (= next month) to switch to load next month view and start all over
    var firstLink = this.ajaxLinks[2] || false;
    if (firstLink) this.onAjaxLinkClick(firstLink);
    else this.onAjaxLinkClick(this.ajaxLinks[1]);

    BOOKINGMANAGER.LOAD_THIRD_LINK = false;
  },

  loadFirstLink: function () {
    var firstLink = this.ajaxLinks[0] || false;
    if (firstLink) this.onAjaxLinkClick(firstLink);

    BOOKINGMANAGER.LOAD_FIRST_LINK = false;
  },

  handleReplacedHtml: function (replacedHtml) {

    // save html only if it has form inside
    if ($('form', replacedHtml).length) {
      this.replacedHtml = replacedHtml;
    }

    var oldForm = $('form', this.replacedHtml);
    // remove hidden fields in old html
    $('input[type="hidden"]', oldForm).remove();
    // transform input data to array
    var oldFormData = $(oldForm).serializeArray();
    // insert input data in new form
    this.populateFormData($(this.containerName).find('form'), oldFormData);

  },

  populateFormData: function (frm, oldFormData) {
    for (var i = 0; i < oldFormData.length; i++) {
      var data = oldFormData[i];
      var ctrl = $('[name="' + data.name + '"]', frm);
      ctrl = ctrl.length > 1 ? $(ctrl[1]) : ctrl;
      switch (ctrl.prop("type")) {
        case "radio": case "checkbox":
          ctrl.each(function () {
            if ($(this).attr('value') == data.value) $(this).attr("checked", data.value);
          });
          break;
        default:
          ctrl.val(data.value);
      }
    }
  }

}

$(function () {
  BOOKINGMANAGER.AJAX.init();
  BOOKINGMANAGER.TIMESELECT.init();
});
