import $ = require('jquery');

declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/BackendFormElementIcsSecret
 *
 * @exports TYPO3/CMS/BwBookingmanager/BackendFormElementIcsSecret
 */
class BackendFormElementIcsSecret {

  $resetButton: JQuery;
  $copyButton: JQuery;
  $refreshButton: JQuery;
  $inputUrl: JQuery;
  $inputSecret: JQuery;

  constructor() {
    this.cacheDom();
    this.bindEvents();
  }

  public cacheDom() {
    this.$resetButton = $('#resetButton');
    this.$copyButton = $('#copyButton');
    this.$refreshButton = $('#refreshButton');
    this.$inputUrl = $('#inputUrl');
    this.$inputSecret = $('#inputSecret');
  }

  public bindEvents() {
    this.$resetButton.on('click', this.onResetButtonClick.bind(this));
    this.$copyButton.on('click', this.onCopyButtonClick.bind(this));
    this.$refreshButton.on('click', this.onRefreshButtonClick.bind(this));
    this.$inputSecret.on('input', this.onInputSecretChange.bind(this));
  }

  public onResetButtonClick(e: Event) {
    e.preventDefault();
    this.$inputSecret.val('');
    this.$inputSecret.trigger('input');
  }

  public onCopyButtonClick(e: Event) {
    e.preventDefault();
    this.$inputUrl.select();
    document.execCommand("copy");
  }

  public onRefreshButtonClick(e: Event) {
    e.preventDefault();
    this.$inputSecret.val(this.makeSecret(32));
    this.$inputSecret.trigger('input');
  }

  public onInputSecretChange(e: Event) {
    e.preventDefault();

    let url = this.$inputUrl.attr('data-url-start');
    if (this.$inputSecret.val()) {
      url += '/' + this.$inputSecret.val();
    }
    url += '.ics';

    this.$inputUrl.val(url);

  }

  public makeSecret(length) {
    let result = '';
    const characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  }


}

export = new BackendFormElementIcsSecret();
