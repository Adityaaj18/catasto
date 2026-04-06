<?php

namespace simplerest\controllers;

use simplerest\libs\OpenApi;
use simplerest\libs\FlowLu;
use simplerest\core\libs\Logger;
use simplerest\controllers\MyController;

class WebhooksController extends MyController
{
    /*
        Webhook endpoint for FlowLu CRM (Carlo's account: ct9eab.flowlu.com)

        URL: POST /webhooks/flowlu

        Triggered when an Opportunity moves to the "Fetch Owners" pipeline stage (ID: 48).

        Opportunity custom field mapping (INPUT):
            cf_60  = Province (2-letter code, e.g. "SA", "LE")
            cf_28  = Tipo Catasto ("F" = Fabbricati, "T" = Terreni)

            Comune 1 group:
                cf_73  = Comune Catastale 1
                cf_74  = Foglio 1    cf_75  = Particella 1
                cf_76  = Foglio 2    cf_78  = Particella 2
                cf_79  = Foglio 3    cf_80  = Particella 3
                cf_81  = Foglio 4    cf_82  = Particella 4
                cf_83  = Foglio 5    cf_84  = Particella 5
                cf_85  = Foglio 6    cf_86  = Particella 6

            Comune 2 group:
                cf_87  = Comune Catastale 2
                cf_88  = Foglio 7    cf_89  = Particella 7
                cf_90  = Foglio 8    cf_91  = Particella 8
                cf_92  = Foglio 9    cf_93  = Particella 9
                cf_94  = Foglio 10   cf_95  = Particella 10

        Pipeline stage IDs:
            48 = Fetch Owners
            49 = Unassigned
    */
    function flowlu()
    {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, $errno, $errno, $errfile, $errline);
        });

        try {
            $req = file_get_contents("php://input");

            Logger::log("FlowLu webhook received: $req\n", 'flowlu_webhooks.txt');

            if (empty($req)) {
                return ['success' => false, 'error' => 'Empty request body'];
            }

            $payload = json_decode($req, true);

            if (empty($payload)) {
                return ['success' => false, 'error' => 'Invalid JSON payload'];
            }

            $current  = $payload['current'] ?? [];
            $leadId   = (int) ($current['id'] ?? 0);
            $provincia = strtoupper(trim($current['cf_60'] ?? ''));
            $tipo      = strtoupper(trim($current['cf_28'] ?? '')) ?: env('CATASTO_DEFAULT_TIPO', 'F');

            Logger::log("FlowLu webhook: leadId=$leadId provincia=$provincia tipo=$tipo\n", 'flowlu_webhooks.txt');

            if (!$leadId) {
                return ['success' => false, 'error' => 'Missing lead_id'];
            }

            // Build pairs from Comune 1 (cf_73) — foglio/particella cf_74–cf_86
            $comune1 = trim($current['cf_73'] ?? '');
            $comune1Foglios     = ['cf_74', 'cf_76', 'cf_79', 'cf_81', 'cf_83', 'cf_85'];
            $comune1Particelle  = ['cf_75', 'cf_78', 'cf_80', 'cf_82', 'cf_84', 'cf_86'];

            // Build pairs from Comune 2 (cf_87) — foglio/particella cf_88–cf_95
            $comune2 = trim($current['cf_87'] ?? '');
            $comune2Foglios     = ['cf_88', 'cf_90', 'cf_92', 'cf_94'];
            $comune2Particelle  = ['cf_89', 'cf_91', 'cf_93', 'cf_95'];

            $pairs = [];

            foreach ([
                ['comune' => $comune1, 'foglios' => $comune1Foglios, 'particelle' => $comune1Particelle],
                ['comune' => $comune2, 'foglios' => $comune2Foglios, 'particelle' => $comune2Particelle],
            ] as $group) {
                if (empty($group['comune'])) continue;

                for ($i = 0; $i < count($group['foglios']); $i++) {
                    $foglio     = $current[$group['foglios'][$i]]     ?? null;
                    $particella = $current[$group['particelle'][$i]]  ?? null;

                    if ($foglio !== null && $foglio !== '' && $particella !== null && $particella !== '') {
                        $pairs[] = [
                            'comune'     => $group['comune'],
                            'foglio'     => (string) $foglio,
                            'particella' => (string) $particella,
                        ];
                    }
                }
            }

            Logger::log("FlowLu webhook: pairs=" . json_encode($pairs) . "\n", 'flowlu_webhooks.txt');

            if (empty($pairs)) {
                return ['success' => false, 'error' => "No foglio/particella pairs found for lead $leadId"];
            }

            // Call land registry for each pair — collect owners
            $allOwners = []; // keyed by "taxCode|foglio|particella" to track per-parcel

            foreach ($pairs as $pair) {
                $risultato = $this->callLandRegistry(
                    $pair['comune'],
                    $pair['foglio'],
                    $pair['particella'],
                    $provincia,
                    '',
                    $leadId,
                    $tipo
                );

                Logger::log("FlowLu webhook: risultato for foglio={$pair['foglio']} particella={$pair['particella']}: " . json_encode($risultato) . "\n", 'flowlu_webhooks.txt');

                if ($risultato === null) {
                    continue; // async — will be handled by callback
                }

                foreach ($risultato['immobili'] ?? [] as $immobile) {
                    foreach ($immobile['intestatari'] ?? [] as $owner) {
                        $taxCode = $owner['cf'] ?? null;
                        if (!$taxCode) continue;

                        $key = $taxCode . '|' . $pair['foglio'] . '|' . $pair['particella'];
                        if (isset($allOwners[$key])) continue;

                        $allOwners[$key] = [
                            'denominazione' => $owner['denominazione'] ?? '',
                            'cf'            => $taxCode,
                            'proprieta'     => $owner['proprieta']    ?? '',
                            'quota'         => $owner['quota']        ?? '',
                            'rendita'       => $immobile['rendita']   ?? '',
                            'categoria'     => $immobile['categoria'] ?? $immobile['qualita'] ?? '',
                            'indirizzo'     => $immobile['indirizzo'] ?? '',
                            'foglio'        => $pair['foglio'],
                            'particella'    => $pair['particella'],
                            'comune'        => $pair['comune'],
                        ];
                    }
                }
            }

            Logger::log("FlowLu webhook: allOwners=" . json_encode($allOwners) . "\n", 'flowlu_webhooks.txt');

            if (!empty($allOwners)) {
                $this->createOwnerRecords($leadId, $allOwners);
            }

            // Move opportunity to Unassigned stage
            $unassignedStage = (int) env('FLOWLU_UNASSIGNED_STAGE_ID', 49);
            $res = FlowLu::updateLead($leadId, ['pipeline_stage_id' => $unassignedStage]);
            Logger::log("FlowLu lead $leadId update response: " . json_encode($res) . "\n", 'flowlu_webhooks.txt');

            return ['success' => true, 'owners_found' => count($allOwners)];

        } catch (\Throwable $e) {
            $msg = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
            Logger::logError("FlowLu webhook error: $msg\nTrace: " . $e->getTraceAsString());
            return ['success' => false, 'error' => $msg];
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Create Record List entries + contacts for all owners.
     */
    private function createOwnerRecords(int $leadId, array $allOwners): void
    {
        $listId = (int) env('FLOWLU_LAND_OWNERS_LIST_ID', 0);

        if (!$listId) {
            Logger::log("FlowLu: FLOWLU_LAND_OWNERS_LIST_ID not set, skipping record creation\n", 'flowlu_webhooks.txt');
            return;
        }

        foreach ($allOwners as $owner) {
            $taxCode = $owner['cf'];

            // Find or create contact
            $contact = FlowLu::searchContactByTaxCode($taxCode);
            if ($contact === null) {
                $name      = $this->extractName($owner['denominazione']);
                $contactId = FlowLu::createContact($name, $taxCode);
                Logger::log("FlowLu webhook: created contact $contactId for taxCode=$taxCode\n", 'flowlu_webhooks.txt');
            } else {
                $contactId = isset($contact['id']) ? (int) $contact['id'] : null;
                Logger::log("FlowLu webhook: found existing contact $contactId for taxCode=$taxCode\n", 'flowlu_webhooks.txt');
            }

            if ($contactId) {
                FlowLu::linkContactToLead($contactId, $leadId);
            }

            // Create Land Owners record list entry
            $res = FlowLu::createLandOwnerRecord($listId, $leadId, $owner, $owner['foglio'], $owner['particella'], $owner['comune']);
            Logger::log("FlowLu webhook: created Land Owner record: " . json_encode($res) . "\n", 'flowlu_webhooks.txt');
        }
    }

    private function callLandRegistry(string $comune, string $foglio, string $particella, string $provincia = '', string $sezione = '', int $leadId = 0, string $tipo = ''): ?array
    {
        $url = 'https://catasto.openapi.it/richiesta/prospetto_catastale/';

        if (empty($provincia)) {
            $provincia = env('CATASTO_DEFAULT_PROVINCIA', 'RM');
        }

        if (empty($tipo)) {
            $tipo = env('CATASTO_DEFAULT_TIPO', 'F');
        }

        $data = [
            'tipo_catasto' => $tipo,
            'provincia'    => $provincia,
            'comune'       => $comune,
            'foglio'       => $foglio,
            'particella'   => $particella,
        ];

        $sezione = $sezione ?: env('CATASTO_DEFAULT_SEZIONE', '');
        if (!empty($sezione)) {
            $data['sezione'] = $sezione;
        }

        $callback = "?r=realstate&sub=prospetto_catastale" . ($leadId ? "&lead_id=$leadId" : '');
        $res = OpenApi::makeRequest($data, $url, $callback);

        if (is_string($res)) {
            $res = json_decode($res, true);
        }

        Logger::log("Land registry raw response for foglio=$foglio particella=$particella: " . json_encode($res) . "\n", 'flowlu_webhooks.txt');

        if (empty($res) || !($res['success'] ?? false)) {
            Logger::logError("Land registry API error for foglio=$foglio particella=$particella comune=$comune: " . json_encode($res));
            return null;
        }

        $stato     = $res['data']['stato'] ?? '';
        $risultato = $res['data']['risultato'] ?? null;

        if ($risultato !== null) {
            return $risultato;
        }

        if ($stato === 'in_erogazione') {
            Logger::log("Land registry async for foglio=$foglio particella=$particella (id={$res['data']['id']}). Results will arrive via callback.\n", 'flowlu_webhooks.txt');
            return null;
        }

        return null;
    }

    private function extractName(string $denominazione): string
    {
        $name = preg_replace('/\s+(nato|nata)\s+.*/i', '', $denominazione);
        $name = preg_replace('/\s+con\s+sede\s+.*/i', '', $name);
        return trim($name);
    }
}
