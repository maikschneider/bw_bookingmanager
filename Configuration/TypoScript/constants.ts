
plugin.tx_bwbookingmanager_bookingmanager {
    view {
        # cat=plugin.tx_bwbookingmanager_bookingmanager/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:bw_bookingmanager/Resources/Private/Templates/
        # cat=plugin.tx_bwbookingmanager_bookingmanager/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:bw_bookingmanager/Resources/Private/Partials/
        # cat=plugin.tx_bwbookingmanager_bookingmanager/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:bw_bookingmanager/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_bwbookingmanager_bookingmanager//a; type=string; label=Default storage PID
        storagePid =
    }
}
