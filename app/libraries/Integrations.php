<?php
/**
 * File: Integrations.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class Integrations {
    /** @var IntegrationService */
    static $googleDriveIntegration = null;
    /** @var IntegrationService */
    static $webhookIntegration = null;
    /** @var IntegrationService */
    static $freshbooksIntegration = null;

    protected static function init() {
        self::$googleDriveIntegration = IntegrationService::where('service_id', IntegrationService::SERVICE_GOOGLE_DRIVE)->first();
        self::$webhookIntegration = IntegrationService::where('service_id', IntegrationService::SERVICE_WEBHOOK)->first();
        self::$freshbooksIntegration = IntegrationService::where('service_id', IntegrationService::SERVICE_FRESHBOOKS)->first();
    }

    public static function GoogleDriveStorePricelist($companyId, $selectedUserId, $pricelistInfo) {
        if (self::$googleDriveIntegration == null) {
            self::init();
        }

        if (!self::$googleDriveIntegration->isIntegrated($companyId)) {
            return;
        }
        
        $company = Company::where('company_id', $companyId)->first();
        if (!$company) {
            return;
        }

        $selectedUser = User::where('user_id', $selectedUserId)->first();
        if (!$selectedUser) {
            return;
        }

        $integration = self::$googleDriveIntegration->getIntegration($companyId);
        $google = Google::getClient();
        $google->setAccessToken($integration->token);
        if ($google->isAccessTokenExpired()) {
            $rawToken = json_decode($integration->token);
            $google->refreshToken($rawToken->refresh_token);
            $integration->token = $google->getAccessToken();
            $integration->save();
        }

        try {
            /** @var Google_Service_Drive $drive */
            $drive = Google::make('Drive');
            $drive->getClient()->setAccessToken($integration->token);
            // checking for folder
            $folders = $drive->files->listFiles([
                'q' => "title='Fresh Tariffs' and mimeType='application/vnd.google-apps.folder' and 'root' in parents and trashed=false"
            ]);

            if ($folders->count() === 0) {
                // need to create new folder
                /** @var Google_Service_Drive_DriveFile $folder */
                $folder = Google::make('Drive_DriveFile');
                $folder->setTitle('Fresh Tariffs');
                $folder->setMimeType('application/vnd.google-apps.folder');
                $request = $drive->files->insert($folder, [
                    'fields' => 'id'
                ]);
                $folderId = $request->id;
            } else {
                $folderId = $folders[0]->id;
            }

            /** @var Google_Service_Drive_DriveFile $file */
            $file = Google::make('Drive_DriveFile');

            $filename = Utils::formatTemplate($company->email_subject, [
                'username' => $selectedUser->username,
                'company' => $company->name,
                'list-type' => $pricelistInfo->price_type,
                'date' => $pricelistInfo->effective_date
            ]);

            $file->setTitle($filename . '.xls');
            $file->setMimeType('application/vnd.ms-excel');
            /** @var Google_Service_Drive_ParentReference $parent */
            $parent = Google::make('Drive_ParentReference');
            $parent->setId($folderId);
            $file->setParents([$parent]);

            $drive->files->insert($file, [
                'data' => file_get_contents(public_path('pricelists/') . $pricelistInfo->filename),
                'uploadType' => 'multipart',
                'mimeType' => 'application/vnd.ms-excel'
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return;
        }
    }

    public static function WebhookStorePricelist($companyId, $selectedUserId, $pricelistInfo) {
        if (self::$webhookIntegration == null) {
            self::init();
        }

        if (!self::$webhookIntegration->isIntegrated($companyId)) {
            return;
        }

        $company = Company::where('company_id', $companyId)->first();
        if (!$company) {
            return;
        }

        $selectedUser = User::where('user_id', $selectedUserId)->first();
        if (!$selectedUser) {
            return;
        }

        $integration = self::$webhookIntegration->getIntegration($companyId);
        $webhookURL = $integration->token;
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->post($webhookURL, [
                'timeout' => 3,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Fresh Tariffs API/1.0'
                ],
                'body' => json_encode([
                    'status' => 'ok',
                    'price_id' => (string)$pricelistInfo->price_id,
                    'price_type' => $pricelistInfo->price_type,
                    'effective_date' => $pricelistInfo->effective_date,
                    'public_url' => URL::to('/pricelists/' . $pricelistInfo->filename)
                ]),
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return;
        }
    }

    public static function FreshbooksTokenGetContacts($companyId) {
        if (self::$freshbooksIntegration == null) {
            self::init();
        }

        if (!self::$freshbooksIntegration->isIntegrated($companyId)) {
            return false;
        }

        $company = Company::where('company_id', $companyId)->first();
        if (!$company) {
            return false;
        }

        $integration = self::$freshbooksIntegration->getIntegration($companyId);
        $token = json_decode($integration->token);
        $apiURL = sprintf('https://%s.freshbooks.com/api/2.1/xml-in', $token->domain);
        $token = $token->access_token;
        $request = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<request method="client.list">
    <page>1</page>
    <per_page>100</per_page>
    <folder>active</folder>
</request>
XML;
        try {
            $result = Freshbooks::tokenPost($apiURL, $token, $request);

            /*
             * This array will be used for customers creation and have following structure:
             * [
             *   'username' => '',
             *   'email' => '',
             *   'email_cc' => ''
             * ]
            */
            $importedContacts = [];

            foreach ($result->clients->client as $client) {
                $contactUsername = null;
                if (!empty($client->organization)) {
                    $contactUsername = (string) $client->organization;
                } elseif (!empty($client->first_name)) {
                    $contactUsername = sprintf('%s %s', $client->first_name, $client->last_name);
                } else {
                    $contactUsername = (string) $client->email;
                }

                $contactEmail = (string) $client->email;
                $contactEmailCC = null;

                if (!empty($client->contacts)) {
                    $contactEmailCC = (string) $client->contacts->contact[0]->email;
                }

                $importedContacts[] = [
                    'username' => $contactUsername,
                    'email' => $contactEmail,
                    'email_cc' => $contactEmailCC
                ];
            }

            return $importedContacts;
        } catch (Exception $e) {
            Log::error($e);
            return false;
        }
    }
}