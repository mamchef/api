<?php

namespace App\Services;

use App\Models\Chef;
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
use DocuSign\eSign\Model\DateSigned;
use Exception;
use SplFileObject;
use Barryvdh\DomPDF\Facade\Pdf;

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
     * @param int $chefId
     * @return string
     * @throws Exception
     */
    public function sendPdfForSigning(
        int $chefId
    ): string
    {
        $chef = Chef::query()->with('chefStore')->findOrFail($chefId);
        try {


            // Generate PDF from Blade template
            $pdf = Pdf::loadView('contracts.partner-agreement_' . strtolower($chef->lang) ?? 'en', [
                'chef' => $chef
            ]);

            // Get PDF content as base64
            $pdfContent = $pdf->output();
            $base64FileContent = base64_encode($pdfContent);

            $document = new Document([
                'document_base64' => $base64FileContent,
                'name' => 'MamChef Partner Agreement.pdf',
                'file_extension' => 'pdf',
                'document_id' => '1',
            ]);

            // Create date signed tab for dynamic date
            $dateSignedTab = new DateSigned([
                'document_id' => '1',
                'page_number' => '1',
                'anchor_string' => '{{date_signed}}',
                'anchor_units' => 'pixels',
                'anchor_y_offset' => '0',
                'anchor_x_offset' => '0',
                'tab_label' => 'date_signed',
                'name' => 'Date Signed',
                'required' => 'true'
            ]);

            $signer = new Signer([
                'email' => $chef->email,
                'name' => $chef->getFullName(),
                'recipient_id' => '1',
                'routing_order' => '1',
            ]);

            $signHere = new SignHere([
                'document_id' => '1',
                'anchor_string' => '{{chef_signature}}',
                'anchor_units' => 'pixels',
                'anchor_y_offset' => '0',
                'anchor_x_offset' => '0',
                'tab_label' => 'signature',
                'name' => 'Signature',
                'required' => 'true'
            ]);

            $signer->setTabs(new Tabs([
                'sign_here_tabs' => [$signHere],
                'date_signed_tabs' => [$dateSignedTab]
            ]));

            $envelopeDefinition = new EnvelopeDefinition([
                'email_subject' => 'MamChef Partner Agreement - Please Sign',
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
     * Resend notification for an existing envelope
     * This doesn't create a new envelope, just resends the email notification
     *
     * @param string $envelopeId
     * @return bool
     * @throws Exception
     */
    public function resendContractNotification(string $envelopeId): bool
    {
        try {
            $envelopeApi = new EnvelopesApi($this->apiClient);

            // Update envelope to resend notification
            $envelopeApi->update(
                $this->args['account_id'],
                $envelopeId,
                ['resend_envelope' => 'true']
            );

            return true;
        } catch (Exception $e) {
            throw new Exception('Error resending contract notification: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF from Blade template with dynamic data
     * Uncomment and use this method after installing barryvdh/laravel-dompdf
     *
     * @param Chef $chef
     * @return string base64 encoded PDF content
     */
    /*
    private function generateContractPdf(Chef $chef): string
    {
        $pdf = Pdf::loadView('contracts.partner-agreement', [
            'chef' => $chef
        ]);

        return base64_encode($pdf->output());
    }
    */

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