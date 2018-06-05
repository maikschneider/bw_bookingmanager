
plugin.tx_bwbookingmanager_pi1 {
    view {
        # cat=plugin.tx_bwbookingmanager_pi1/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:bw_bookingmanager/Resources/Private/Templates/
        # cat=plugin.tx_bwbookingmanager_pi1/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:bw_bookingmanager/Resources/Private/Partials/
        # cat=plugin.tx_bwbookingmanager_pi1/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:bw_bookingmanager/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_bwbookingmanager_pi1//a; type=string; label=Default storage PID
        storagePid =
    }
    settings {
        showPid = 
        calendarPid = 
        entryPid = 
        backPid = 
        javascript{
            #jquery = EXT:bw_bookingmanager/Resources/Public/JavaScript/jquery-3.3.1.min.js
            bookingmanager = EXT:bw_bookingmanager/Resources/Public/JavaScript/BookingManager.js
        }
        ajax{
            enable = 0
            typeNum = 555
        }
    }
}
