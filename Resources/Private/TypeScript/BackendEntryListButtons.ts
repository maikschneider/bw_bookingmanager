import $ = require('jquery');

class BackendEntryListButtons
{
  constructor() {
    $((): void => {
      this.initialize();
    });
  }

  public initialize()
  {
    // toggle container of settings
    $('a[data-togglelink="1"]').click(function (e) {
      e.preventDefault();
      $('#setting-container').toggle();
    });

    // print button
    $('.module-docheader-bar-buttons .btn.print').on('click', (e) => {
      e.preventDefault();
      window.print();
    });

    // filter form submit
    $('#entryListForm').on('submit', function (e) {
      e.preventDefault();
      const url = e.currentTarget.getAttribute('action');
      const form = e.currentTarget as HTMLFormElement;
      const formData = new FormData(form);
      const search = new URLSearchParams(formData as any);
      const queryString = search.toString();
      top.TYPO3.Backend.ContentContainer.setUrl(url + '&' + queryString);
    });
  }
}

export = new BackendEntryListButtons();
