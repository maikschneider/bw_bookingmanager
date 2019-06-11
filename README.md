# blueways Booking Manager

## Todo

### High priority

* better repeating configuration (nearly finished)
* support for TYPO3 v8
    * Backend filter module
    * check TimeslotWizard in backend module

### Low priority
* API documentation
* validation of direct bookings
* remove email button, use bw_email
* dashboard integration

## Upgrade notes from v1.x to v2

* Database changes:
    * Run database analyser compare to add new fields

* TypoScript changes:
    * Rename constants **plugin.tx_bwbookingmanager_pi1** to **plugin.tx_bwbookingmanager**. This is due to the new api plugin which should use the same templates
    
* FlexForms changes: 
    * Open and save **all** tt_content elements containing the **tx_bwbookingmanager_pi1** plugin in order to override the old FlexForm paths.
    
* JavaScript changes:
    * jQuery is no longer included
    * BookingManager.js is no longer included:
        * use new API controller
        * see jQuery plugin from minigolf project
    * Bookingmanager.Abide.js is no longer included
        * copy from history and include be yourself

* Some validators for new Entry attributes have been removed:
    * phone (```@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
    * city (```@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
    * zip (```* @validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
    * street (```@@validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)```)
    * prename (```@validate NotEmpty, StringLengthValidator(minimum=2, maximum=50)```)
