<?php

namespace simplerest\libs;

use simplerest\core\libs\ApiClient;
use simplerest\core\libs\Logger;

class FlowLu
{
    private static function baseUrl(): string
    {
        return env('FLOWLU_BASE_URL', 'https://ct9eab.flowlu.com/api/v1/module/');
    }

    private static function apiKey(): string
    {
        return env('FLOWLU_API_KEY', '');
    }

    private static function post(string $endpoint, array $data = []): ?array
    {
        $url = self::baseUrl() . $endpoint . '?api_key=' . urlencode(self::apiKey());

        Logger::log("FlowLu POST URL: $url\n", 'flowlu_api.txt');
        Logger::log("FlowLu POST body: " . json_encode($data) . "\n", 'flowlu_api.txt');

        $client = ApiClient::instance()
            ->setHeaders([
                'Accept: application/json',
                'Content-Type: application/json',
            ])
            ->disableSSL()
            ->setUrl($url)
            ->setBody($data)
            ->post();

        $res = $client->data();

        if (is_string($res)) {
            $res = json_decode($res, true);
        }

        Logger::log("FlowLu POST $endpoint response: " . json_encode($res) . "\n", 'flowlu_api.txt');

        return $res;
    }

    private static function postForm(string $endpoint, array $data = []): ?array
    {
        $url = self::baseUrl() . $endpoint . '?api_key=' . urlencode(self::apiKey());

        Logger::log("FlowLu POST-FORM URL: $url\n", 'flowlu_api.txt');
        Logger::log("FlowLu POST-FORM body: " . json_encode($data) . "\n", 'flowlu_api.txt');

        $client = ApiClient::instance()
            ->setHeaders([
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ])
            ->disableSSL()
            ->setUrl($url)
            ->setBody(http_build_query($data))
            ->post();

        $res = $client->data();

        if (is_string($res)) {
            $res = json_decode($res, true);
        }

        Logger::log("FlowLu POST-FORM $endpoint response: " . json_encode($res) . "\n", 'flowlu_api.txt');

        return $res;
    }

    static function getLead(int $leadId): ?array
    {
        return self::post("crm/lead/$leadId");
    }

    static function updateLead(int $leadId, array $fields): ?array
    {
        return self::postForm("crm/leads/update/$leadId", $fields);
    }

    /**
     * Search for a contact by tax code stored in cf_102.
     */
    static function searchContactByTaxCode(string $taxCode): ?array
    {
        $res = self::post('crm/contact/list', [
            'filter' => [
                [
                    'field'    => 'cf_102',
                    'operator' => 'equals',
                    'value'    => $taxCode,
                ]
            ],
            'page'     => 1,
            'per_page' => 1,
        ]);

        $items = $res['response']['items'] ?? [];

        return !empty($items) ? $items[0] : null;
    }

    /**
     * Create a new contact with name and tax code (cf_102).
     */
    static function createContact(string $name, string $taxCode): ?int
    {
        $res = self::post('crm/contact/create', [
            'name'  => $name,
            'cf_102' => $taxCode,
        ]);

        return isset($res['response']['id']) ? (int) $res['response']['id'] : null;
    }

    /**
     * Link a contact to an opportunity (lead).
     */
    static function linkContactToLead(int $contactId, int $leadId): void
    {
        self::post('crm/lead/link_contact', [
            'id'         => $leadId,
            'contact_id' => $contactId,
        ]);
    }

    /**
     * Get all record lists to find the "Land Owners" list ID.
     */
    static function getRecordLists(): ?array
    {
        return self::post('crm/record_list/list', []);
    }

    /**
     * Create a new entry in the Land Owners record list.
     *
     * @param int    $listId    The record list ID for "Land Owners"
     * @param int    $leadId    The opportunity ID to link to
     * @param array  $owner     Owner data from land registry
     * @param string $foglio
     * @param string $particella
     * @param string $comune
     */
    static function createLandOwnerRecord(int $listId, int $leadId, array $owner, string $foglio, string $particella, string $comune): ?array
    {
        $name = ($owner['denominazione'] ?? '') . ' - ' . $comune . ' ' . $foglio . '/' . $particella;

        $data = [
            'list_id'  => $listId,
            'cf_96'    => $name,
            'cf_97'    => $leadId,
            'cf_98'    => $comune . ' F.' . $foglio . ' P.' . $particella,
            'cf_99'    => $foglio,
            'cf_100'   => $particella,
            'cf_101'   => $owner['denominazione']  ?? '',
            'cf_102'   => $owner['cf']              ?? '',
            'cf_103'   => $owner['proprieta']       ?? '',
            'cf_104'   => $owner['quota']           ?? '',
            'cf_105'   => $owner['rendita']         ?? '',
            'cf_106'   => $owner['categoria']       ?? '',
            'cf_107'   => $owner['indirizzo']       ?? '',
            'cf_108'   => '',
            'cf_109'   => '',
        ];

        return self::post('crm/record_list/create', $data);
    }
}
