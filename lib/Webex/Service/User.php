<?php

class Webex_Service_User extends Webex_Service_Abstract
{
    const GET_LOGIN_TICKET = 'user.GetLoginTicket';
    const LST_SUMMARY_USER = 'user.LstsummaryUser';

    /**
     * Returns a host authentication ticket, which is a temporary
     * identifier string associated with a host.
     *
     * @return string
     */
    public function getLoginTicket() // {{{
    {
        $response = $this->_webex->transmit(self::GET_LOGIN_TICKET);
        $bodyContent = $this->_parseResponse($response);

        $ticket = (string) $bodyContent->children(self::SCHEMA_USER)->ticket;
        return $ticket;
    } // }}}

    /**
     * Queries WebEx User Service for summary information of the host users.
     *
     * @param  Webex_Model_UserQuery|array $query OPTIONAL
     * @return Webex_Collection_ResultsCollection<Webex_Model_UserSummary>
     */
    public function queryUsers($query = null)
    {
        $response = $this->_webex->transmit(
            self::LST_SUMMARY_USER,
            $query ? $this->_serializer->serializeUserQuery($query) : ''
        );

        try {
            $this->_parseResponse($response);
            $data = $this->_serializer->unserializeUserSummaries($response);

        } catch (Exception $e) {
            // API responds with 'validation: Null Point Error' if no user was found,
            // handle this gracefully, as empty results is no reason for throwing exceptions
            if (stripos($e->getMessage(), 'validation: Null Point Error') !== false) {
                $data = array(
                    'total'  => 0,
                    'offset' => 0,
                    'items'  => array(),
                );
            } else {
                throw $e;
            }
        }

        $results = new Webex_Collection_ResultCollection('Webex_Model_UserSummary');
        $results->setTotal($data['total']);
        $results->setOffset($data['offset']);

        foreach ($data['items'] as $item) {
            $results->add(new Webex_Model_UserSummary($item));
        }

        return $results;
    }
}
