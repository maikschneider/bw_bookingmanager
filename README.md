# blueways Booking Manager

## TODO

* configure urls
* allow direct bookings (add entry to calendar without tineslot)

## Upgrade notes

Some validators for new Entry attributes have been removed:

* phone (```@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
* city (```@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
* zip (```* @validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
* street (```@@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
* prename (```@validate NotEmpty, StringLengthValidator(minimum=2, maximum=50)```)
