# blueways Booking Manager

## Todo

* better repeating configuration
* performance
* remove email button, use bw_email
* dashboard integration
* support for TYPO3 v8

## Upgrade notes to v2

Some validators for new Entry attributes have been removed:

* phone (```@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
* city (```@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
* zip (```* @validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
* street (```@@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
* prename (```@validate NotEmpty, StringLengthValidator(minimum=2, maximum=50)```)
