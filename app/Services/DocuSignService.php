<?php

namespace App\Services;

use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Client\ApiException;
use DocuSign\eSign\Client\Auth\OAuth;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\Tabs;
use Exception;
use SplFileObject;

class DocuSignService
{
    private ApiClient $apiClient;
    private array $args;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->args = [
            'account_id' => env('DOCUSIGN_ACCOUNT_ID'),
            'base_path' => env('DOCUSIGN_BASE_URL'),
        ];
        $config = new Configuration();
        $config->setHost($this->args['base_path']);
        $config->addDefaultHeader('Authorization', 'Bearer ' . $this->getToken());
        $this->apiClient = new ApiClient($config);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getToken(): string
    {
        try {
            $privateKey = file_get_contents(storage_path(env('DOCUSIGN_KEY_PATH')), true);
            $config = new Configuration();
            $config->setHost(env('DOCUSIGN_BASE_URL'));

            $oAth = new OAuth();
            $oAth->setOAuthBasePath(env("DOCUSIGN_OAUTH_URL"));
            $apiClient = new ApiClient($config, $oAth);

            $response = $apiClient->requestJWTUserToken(
                env("DOCUSIGN_CLIENT_ID"),
                env("DOCUSIGN_IMPERSONATED_USER_ID"),
                $privateKey,
            );
            return $response[0]->getAccessToken();
        } catch (\Throwable $th) {
            throw new Exception('Failed to get DocuSign token: ' . $th->getMessage());
        }
    }

    /**
     * @param string $recipientName
     * @param string $recipientEmail
     * @return string
     * @throws Exception
     */
    public function sendPdfForSigning(
        string $recipientName,
        string $recipientEmail,
    ): string {
        try {
            if (empty($recipientName) || empty($recipientEmail)) {
                throw new Exception('Recipient name and email are required');
            }
            if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address');
            }

            $contentBytes = file_get_contents(storage_path(env("DOCUSIGN_CONTRACT_PATH")), true);
            $base64FileContent = base64_encode($contentBytes);

            $document = new Document([
                'document_base64' => $base64FileContent,
                'name' => 'Contract.pdf',
                'file_extension' => 'pdf',
                'document_id' => '1',
            ]);

            $signer = new Signer([
                'email' => $recipientEmail,
                'name' => $recipientName,
                'recipient_id' => '1',
                'routing_order' => '1',
            ]);

            $signHere = new SignHere([
                'anchor_string' => '**signature**',
                'anchor_units' => 'pixels',
                'anchor_y_offset' => '10',
                'anchor_x_offset' => '20',
            ]);

            $signer->setTabs(new Tabs([
                'sign_here_tabs' => [$signHere],
            ]));

            $envelopeDefinition = new EnvelopeDefinition([
                'email_subject' => env("DOCUSIGN_EMAIL_SUBJECT"),
                'documents' => [$document],
                'recipients' => new Recipients(['signers' => [$signer]]),
                'status' => 'sent',
            ]);

            $envelopeApi = new EnvelopesApi($this->apiClient);
            $results = $envelopeApi->createEnvelope($this->args['account_id'], $envelopeDefinition);
            return $results->getEnvelopeId();
        } catch (Exception $e) {
            throw new Exception('Error sending document: ' . $e->getMessage());
        }
    }

    /**
     * @param string $envelopeID
     * @return SplFileObject
     * @throws ApiException
     */
    public function downloadEnvelope(string $envelopeID): SplFileObject
    {
        $envelopeApi = new EnvelopesApi($this->apiClient);
        return $envelopeApi->getDocument(
            env("DOCUSIGN_ACCOUNT_ID"),
            '1',
            $envelopeID
        );
    }
}