/**
 * Validates select input to have a value of !=0 choosen
 *
 * @param {*} $el
 * @param {*} required
 * @param {*} parent
 */
function selectboxValidator(
  $el,      /* jQuery element to validate */
  required, /* is the element required according to the `[required]` attribute */
  parent    /* parent of the jQuery element `$el` */
) {
  return $('option:selected', $el).val() !== 0;
};

/**
 * Validates checkbox to be checked
 *
 * @param {*} $el
 * @param {*} required
 * @param {*} parent
 */
function checkboxValidator(
  $el,      /* jQuery element to validate */
  required, /* is the element required according to the `[required]` attribute */
  parent    /* parent of the jQuery element `$el` */
) {
  return $el.prop('checked')
};


if (Foundation && Foundation.Abide){

  Foundation.Abide.defaults.validators['option_selected'] = selectboxValidator;
  Foundation.Abide.defaults.validators['is_checked'] = checkboxValidator;

}
