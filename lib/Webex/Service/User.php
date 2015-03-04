<?php

class Webex_Service_User extends Webex_Service_Abstract
{
    /**
     * Queries WebEx User Service for summary information of the host users.
     *
     * @param  Webex_Model_UserQuery $query OPTIONAL
     * @return Webex_Collection_ResultsCollection<Webex_Model_UserSummary>
     */
    public function queryUsers(Webex_Model_UserQuery $query = null)
    {
        $response = $this->_webex->transmit(
            'user.LstsummaryUser',
            $query ? $this->_serializer->serializeUserQuery($query) : ''
        );
        $this->_parseResponse($response);

        $data = $this->_serializer->unserializeUserSummaries($response);

        $results = new Webex_Collection_ResultCollection('Webex_Model_UserSummary');
        $results->setTotal($data['total']);
        $results->setOffset($data['offset']);

        foreach ($data['items'] as $item) {
            $results->add(new Webex_Model_UserSummary($item));
        }

        assert('count($results) === $data["returned"]');
        return $results;
    }
}
