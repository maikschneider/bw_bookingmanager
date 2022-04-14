@import 'EXT:bw_email/Configuration/TypoScript/constants.typoscript'

plugin.tx_bwbookingmanager {
  persistence {
    storagePid = 
  }
  view {
    templateRootPath = 
    partialRootPath = 
    layoutRootPath = 
  }
  settings {
    cancelTime = 1440
    mail {
      senderAddress = noreply@example.com
      senderName = Example sender name
      replytoAddress = noreply@example.com
      subject = Example subject
      template = Default
      showUid = 
      doSendConfirmation = 0
    }
    userStoragePid = 

    directBooking {
      defaultStartTime = 
      defaultEndTime = 
      minOffset = 
      minLength = 
    }
  }
}

plugin.tx_bwbookingmanager_pi1 {
  settings {
    showPid = 
    calendarPid = 
    entryPid = 
    backPid = 
  }
}

plugin.tx_bwemail {
  view {
    templateRootPath = EXT:bw_bookingmanager/Resources/Private/Templates/Email
    partialRootPath = EXT:bw_bookingmanager/Resources/Private/Templates/Email
    layoutRootPath = EXT:bw_bookingmanager/Resources/Private/Templates/Email
  }
  settings {
    senderAddress = {$plugin.tx_bwbookingmanager.settings.mail.senderAddress}
    senderName = {$plugin.tx_bwbookingmanager.settings.mail.senderName}
    replytoAddress = {$plugin.tx_bwbookingmanager.settings.mail.replytoAddress}
    subject = {$plugin.tx_bwbookingmanager.settings.mail.subject}
    template = {$plugin.tx_bwbookingmanager.settings.mail.template}
    showUid = {$plugin.tx_bwbookingmanager.settings.mail.showUid}
  }
}
