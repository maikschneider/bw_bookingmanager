define("TYPO3/CMS/BwBookingmanager/SelectTimeslotDatesElement",[],(()=>{return t={984:(t,e,n)=>{var a;void 0===(a=function(t,e){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.BackendCalendarViewState=void 0,e.BackendCalendarViewState=class{constructor(t){if(!t.hasAttribute("data-view-state"))return void console.error("Element does not have view-state attribute!");const e=JSON.parse(t.getAttribute("data-view-state"));this.pid=e.pid,this.language="language"in e&&"default"!==e.language?e.language:"en",this.start="start"in e?e.start:new Date,this.calendarView=e.calendarView,this.pastEntries=e.pastEntries,this.pastTimeslots=e.pastTimeslots,this.notBookableTimeslots=e.notBookableTimeslots,this.futureEntries="futureEntries"in e&&"true"===e.futureEntries,this.entryUid="entryUid"in e?e.entryUid:null,this.calendar="calendar"in e?e.calendar:null,this.timeslot="timeslot"in e?e.timeslot:null,this.buttonSaveText="buttonSaveText"in e?e.buttonSaveText:"",this.buttonCancelText="buttonCancelText"in e?e.buttonCancelText:"",this.entryStart="entryStart"in e?e.entryStart:null,this.entryEnd="entryEnd"in e?e.entryEnd:null,this.currentCalendars=e.currentCalendars,this.warningTitle="warningTitle"in e?e.warningTitle:"",this.warningText="warningText"in e?e.warningText:"",this.warningButton="warningButton"in e?e.warningButton:"",this.calendarOptions="calendarOptions"in e?e.calendarOptions:{},this.events={url:TYPO3.settings.ajaxUrls.api_calendar_show,extraParams:()=>{const t=this.entryStart?new Date(this.entryStart).getTime()/1e3:null,e=this.entryEnd?new Date(this.entryEnd).getTime()/1e3:null;return{pid:this.pid,entryUid:this.entryUid,entryStart:t,entryEnd:e,calendar:this.calendar,timeslot:this.timeslot}}}}saveAsUserView(){this.saveRequest&&this.saveRequest.abort(),this.saveRequest=$.post(TYPO3.settings.ajaxUrls.api_user_setting,{viewState:{pid:this.pid,start:this.start,calendarView:this.calendarView,pastEntries:this.pastEntries,pastTimeslots:this.pastTimeslots,notBookableTimeslots:this.notBookableTimeslots,futureEntries:this.futureEntries}})}hasDirectBookingCalendar(){return null!==this.getFirstDirectBookableCalendar()}getFirstDirectBookableCalendar(){for(let t=0;t<this.currentCalendars.length;t++)if(this.currentCalendars[t].directBooking)return this.currentCalendars[t];return null}}}.apply(e,[n,e]))||(t.exports=a)},150:(t,e,n)=>{var a,i;a=[n,e,n(984)],void 0===(i=function(t,e,n){"use strict";return new class{constructor(){document.getElementById("entry-date-select-button"),$("#entry-date-select-button").on("click",this.onButtonClick.bind(this)),parent.window.BackendModalCalendar.onSave=this.onModalSave.bind(this)}onButtonClick(t){t.preventDefault();const e=t.currentTarget;parent.window.BackendModalCalendar.viewState=new n.BackendCalendarViewState(e),parent.window.BackendModalCalendar.openModal()}onModalSave(t,e){document.getElementById("entry-date-select-button").setAttribute("data-view-state",JSON.stringify(e));const n=e.entryUid;"Timeslot"===t.extendedProps.model&&$('input[name="data[tx_bwbookingmanager_domain_model_entry]['+n+'][timeslot]"]').val(t.extendedProps.uid),$('input[name="data[tx_bwbookingmanager_domain_model_entry]['+n+'][start_date]"]').val(t.start.getTime()/1e3),$('input[name="data[tx_bwbookingmanager_domain_model_entry]['+n+'][end_date]"]').val(t.end.getTime()/1e3),$('select[name="data[tx_bwbookingmanager_domain_model_entry]['+n+'][calendar]"]').val(t.extendedProps.calendar);const a={weekday:"short",month:"2-digit",day:"2-digit",year:"numeric",hour:"2-digit",minute:"2-digit",timeZone:"Europe/Berlin"},i=Intl.DateTimeFormat(e.language,a).format(t.start),r=Intl.DateTimeFormat(e.language,a).format(t.end);$("#savedStartDate").html(i),$("#savedEndDate").html(r)}}}.apply(e,a))||(t.exports=i)}},e={},function n(a){var i=e[a];if(void 0!==i)return i.exports;var r=e[a]={exports:{}};return t[a](r,r.exports,n),r.exports}(150);var t,e}));