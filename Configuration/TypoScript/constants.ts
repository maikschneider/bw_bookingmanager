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

    ajax {
      typeNum = 555
    }

    email {
      senderAddress = noreply@example.com
      senderName = Example sender name
      replytoAddress = noreply@example.com
      subject = Example subject
      template = Default
      showUid =
      doSendConfirmation = 0
    }
  }
}
