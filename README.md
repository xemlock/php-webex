PHP WebEx SDK

This is an object for WebEx XML API. No neet to bother about method
parameter names, justr instantiate object, fill in its properties and
store it in the suitable service.

Want to create a meeting?

$meeting = new Webex\_Model\_Meeting(array(
    'name'      => 'My first meeting',
    'startDate' => '2015-02-28 16:30',
    'password'  => '1234',
));

$meetingService = $webex->getService('meeting');
$meeting = $meetingService->saveMeeting($meeting);

