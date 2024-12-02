function getAllEvents() {
  $.ajax({
    url: baseUrl_API + '/events',
    type: 'GET',
    success: function(events) {
      displayEvents(events);
    },
    error: function() {
      console.log('Error loading events');
    },
  });
}

function displayEvents(events) {
  let _html = `<table class='event-table'>
        <thead>
          <tr>
            <th class='event-title'>Title</th>
            <th class='event-scheduled_date'>Scheduled Date</th>
            <th class='event-status'>Status</th>
          </tr>
        </thead>
        <tbody>`;
  for (let event of events) {
    let cssClass = (events.indexOf(event) % 2 === 0) ? 'event-row' : 'event-row event-row-odd';
    _html += `<tr class='${cssClass}'>
            <td class='event-title'>${event.title}</td>
            <td class='event-scheduled_date'>${event.scheduled_date}</td>
            <td class='event-status'>${event.status}</td>
            </tr>`;
  }
  _html += `</tbody></table>`;
  $('main#div#main-heading').html('Events');
  $('main#div#sub-heading').html('2024 Event Calendar');
  $('#section-content').html(_html);
}
