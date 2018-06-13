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
  return $('option:selected', $el).val() > 0;
};


if (Foundation && Foundation.Abide){

  Foundation.Abide.defaults.validators['option_selected'] = selectboxValidator;

}
