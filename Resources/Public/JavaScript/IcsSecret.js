define(["require", "exports", "jquery"], function (require, exports, $) {
    "use strict";
    /**
     * Module: TYPO3/CMS/BwBookingmanager/IcsSecret
     *
     * @exports TYPO3/CMS/BwBookingmanager/IcsSecret
     */
    var IcsSecret = /** @class */ (function () {
        function IcsSecret() {
            this.cacheDom();
            this.bindEvents();
        }
        IcsSecret.prototype.cacheDom = function () {
            this.$resetButton = $('#resetButton');
            this.$copyButton = $('#copyButton');
            this.$refreshButton = $('#refreshButton');
            this.$inputUrl = $('#inputUrl');
            this.$inputSecret = $('#inputSecret');
        };
        IcsSecret.prototype.bindEvents = function () {
            this.$resetButton.on('click', this.onResetButtonClick.bind(this));
            this.$copyButton.on('click', this.onCopyButtonClick.bind(this));
            this.$refreshButton.on('click', this.onRefreshButtonClick.bind(this));
            this.$inputSecret.on('input', this.onInputSecretChange.bind(this));
        };
        IcsSecret.prototype.onResetButtonClick = function (e) {
            e.preventDefault();
            this.$inputSecret.val('');
            this.$inputSecret.trigger('input');
        };
        IcsSecret.prototype.onCopyButtonClick = function (e) {
            e.preventDefault();
            this.$inputUrl.select();
            document.execCommand("copy");
        };
        IcsSecret.prototype.onRefreshButtonClick = function (e) {
            e.preventDefault();
            this.$inputSecret.val(this.makeSecret(32));
            this.$inputSecret.trigger('input');
        };
        IcsSecret.prototype.onInputSecretChange = function (e) {
            e.preventDefault();
            var url = this.$inputUrl.attr('data-url-start');
            if (this.$inputSecret.val()) {
                url += '/' + this.$inputSecret.val();
            }
            url += '.ics';
            this.$inputUrl.val(url);
        };
        IcsSecret.prototype.makeSecret = function (length) {
            var result = '';
            var characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        };
        return IcsSecret;
    }());
    return new IcsSecret();
});
