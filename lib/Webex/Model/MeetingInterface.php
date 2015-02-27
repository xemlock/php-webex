<?php

interface Webex_Model_MeetingInterface
{
    public function getId();
    public function setId($id);

    public function getName();
    public function setName($name);

    public function getType();
    public function setType($type);

    public function getStartDate();
    public function setStartDate($startDate);

    public function getDuration();
    public function setDuration($duration);

    public function getPassword();
    public function setPassword($password);

    public function isPublic();
    public function setPublic($flag);

    public function getJoinBeforeHost();
    public function setJoinBeforeHost($flag);

    public function getEnforcePassword();
    public function setEnforcePassword($flag);

    public function getMaxUsers();
    public function setMaxUsers($maxUsers);

    public function getAttendees();
}
