<?php

namespace App\Services;

use App\Helpers\Helpers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GorgiasService
{
    protected string $domain;
    protected string $baseUrl;
    protected string $email;
    protected string $apiKey;

    public function __construct()
    {
        $this->domain = Helpers::setting('gorgias_domain');
        $this->email = Helpers::setting('gorgias_email');
        $this->apiKey = Helpers::setting('gorgias_api_key');
        $this->baseUrl = "https://{$this->domain}.gorgias.com/api";
    }

    /**
     * Get authorization header for Gorgias API requests.
     */
    protected function getAuthHeader(): string
    {
        $credentials = base64_encode("{$this->email}:{$this->apiKey}");
        return "Basic {$credentials}";
    }

    /**
     * Make a GET request to Gorgias API.
     */
    protected function get(string $endpoint): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->getAuthHeader(),
        ])->get("{$this->baseUrl}{$endpoint}");

        return $response->json();
    }

    /**
     * Make a POST request to Gorgias API.
     */
    protected function post(string $endpoint, array $data): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->getAuthHeader(),
        ])->post("{$this->baseUrl}{$endpoint}", $data);

        return $response->json();
    }

    /**
     * Make a PUT request to Gorgias API.
     */
    protected function put(string $endpoint, array $data): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->getAuthHeader(),
            'Content-Type' => 'application/json',
        ])->put("{$this->baseUrl}{$endpoint}", $data);

        return $response->json() ?? [];
    }

    /**
     * Make a DELETE request to Gorgias API.
     */
    protected function delete(string $endpoint, array $data = []): array
    {
        $request = Http::withHeaders([
            'Authorization' => $this->getAuthHeader(),
            'Content-Type' => 'application/json',
        ]);

        if (!empty($data)) {
            $response = $request->send('DELETE', "{$this->baseUrl}{$endpoint}", [
                'json' => $data
            ]);
        } else {
            $response = $request->delete("{$this->baseUrl}{$endpoint}");
        }

        return $response->json() ?? [];
    }

    // ============================================================
    // Users
    // ============================================================

    /**
     * Fetch all users from Gorgias.
     */
    public function users(): array
    {
        return $this->get('/users');
    }

    /**
     * Fetch a single user by ID from Gorgias.
     */
    public function user(int $userId): array
    {
        return $this->get("/users/{$userId}/");
    }

    // ============================================================
    // Tickets
    // ============================================================

    /**
     * Fetch all tickets from Gorgias.
     */
    public function tickets(): array
    {
        return $this->get('/tickets');
    }

    /**
     * Fetch a single ticket by ID from Gorgias.
     */
    public function ticket(int $ticketId): array
    {
        return $this->get("/tickets/{$ticketId}/");
    }

    /**
     * Fetch all messages from a ticket by ticket ID.
     */
    public function ticketMessages(int $ticketId): array
    {
        return $this->get("/tickets/{$ticketId}/messages");
    }

    /**
     * Fetch a single message from a ticket by ticket ID and message ID.
     */
    public function ticketMessage(int $ticketId, int $messageId): array
    {
        return $this->get("/tickets/{$ticketId}/messages/{$messageId}/");
    }

    /**
     * Create a new message in a ticket.
     *
     * @param int $ticketId Ticket ID
     * @param array $data Message data (channel, from_agent, source, via, body_html, body_text, etc.)
     */
    public function createTicketMessage(int $ticketId, array $data): array
    {
        return $this->post("/tickets/{$ticketId}/messages", $data);
    }

    /**
     * Update tags to a ticket in Gorgias.
     *
     * @param int $ticketId Ticket ID
     * @param array $data Tags data (names and/or ids)
     */
    public function updateTicketTags(int $ticketId, array $data)
    {
        return $this->put("/tickets/{$ticketId}/tags", $data);
    }

    /**
     * Delete tags from a ticket in Gorgias.
     *
     * @param int $ticketId Ticket ID
     * @param array $data Tags data (ids to remove)
     */
    public function deleteTicketTags(int $ticketId, array $data)
    {
        return $this->delete("/tickets/{$ticketId}/tags", $data);
    }

    /**
     * Update a message in a ticket in Gorgias.
     *
     * @param int $ticketId Ticket ID
     * @param int $messageId Message ID
     * @param array $data Message data to update
     */
    public function updateTicketMessage(int $ticketId, int $messageId, array $data): array
    {
        return $this->put("/tickets/{$ticketId}/messages/{$messageId}/", $data);
    }

    /**
     * Upload attachment to Gorgias.
     *
     * @param string $filePath Path to the file to upload
     * @param string $fileName Optional custom file name
     * @return array Response containing file URL, content_type, name, and size
     */
    public function uploadAttachment(string $filePath, string $fileName = ''): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $fileName = $fileName ?: basename($filePath);

        $response = Http::withHeaders([
            'Authorization' => $this->getAuthHeader(),
        ])
            ->attach(
                'file',
                fopen($filePath, 'r'),
                $fileName
            )
            ->post("{$this->baseUrl}/upload?type=public_attachment");

        if ($response->failed()) {
            Log::error('Gorgias attachment upload failed: ' . $response->body());
            throw new \Exception('Failed to upload attachment: ' . $response->body());
        }

        $result = $response->json();
        return is_array($result) && isset($result[0]) ? $result[0] : $result;
    }

    // ============================================================
    // Customers
    // ============================================================

    /**
     * Fetch all customers from Gorgias.
     */
    public function customers(): array
    {
        return $this->get('/customers');
    }

    /**
     * Fetch a single customer by ID from Gorgias.
     */
    public function customer(int $customerId): array
    {
        return $this->get("/customers/{$customerId}/");
    }

    /**
     * Create a customer in Gorgias.
     *
     * @param array $data Customer data (email, name, channels, timezone, language, data, external_id, etc.)
     */
    public function createCustomer(array $data): array
    {
        return $this->post('/customers', $data);
    }

    /**
     * Update a customer in Gorgias.
     *
     * @param int $customerId Customer ID
     * @param array $data Customer data to update
     */
    public function updateCustomer(int $customerId, array $data): array
    {
        return $this->put("/customers/{$customerId}/", $data);
    }

    // ============================================================
    // Tags
    // ============================================================

    /**
     * Fetch all tags from Gorgias with pagination support.
     * 
     * @param int $perPage Number of tags per page (default: 100, max: 100)
     * @param string|null $cursor Cursor for next page (from meta.next_cursor)
     * @param string $orderBy Field to order by (default: 'name')
     * @return array Response with data, meta (prev_cursor, next_cursor)
     */
    public function tags(int $perPage = 100, ?string $cursor = null, string $orderBy = 'name'): array
    {
        $params = [
            'limit' => min($perPage, 100), // Max 100 per Gorgias API
        ];

        if ($cursor) {
            $params['cursor'] = $cursor;
        }

        $queryString = http_build_query($params);
        return $this->get("/tags?{$queryString}");
    }

    /**
     * Fetch ALL tags from Gorgias by iterating through all pages.
     * 
     * @return array All tags combined from all pages
     */
    public function allTags(): array
    {
        $allTags = [];
        $cursor = null;

        do {
            $response = $this->tags(100, $cursor);

            if (isset($response['data']) && is_array($response['data'])) {
                $allTags = array_merge($allTags, $response['data']);
            }

            $cursor = $response['meta']['next_cursor'] ?? null;
        } while ($cursor);

        return $allTags;
    }

    /**
     * Fetch a single tag by ID from Gorgias.
     */
    public function tag(int $tagId): array
    {
        return $this->get("/tags/{$tagId}/");
    }

    /**
     * Create a tag in Gorgias.
     *
     * @param array $data Tag data (name, description, decoration with color, etc.)
     */
    public function createTag(array $data): array
    {
        return $this->post('/tags', $data);
    }

    /**
     * Update a tag in Gorgias.
     *
     * @param int $tagId Tag ID
     * @param array $data Tag data to update
     */
    public function updateTag(int $tagId, array $data): array
    {
        return $this->put("/tags/{$tagId}/", $data);
    }

    /**
     * Delete a tag in Gorgias.
     *
     * @param int $tagId Tag ID
     */
    public function deleteTag(int $tagId): array
    {
        return $this->delete("/tags/{$tagId}/");
    }

    // ============================================================
    // Custom Fields
    // ============================================================

    /**
     * Fetch custom fields from Gorgias.
     * 
     * @param string $objectType Object type to filter by (e.g., 'Ticket', 'Customer')
     * @return array Response with data array of custom field definitions
     */
    public function customFields(string $objectType = 'Ticket'): array
    {
        return $this->get("/custom-fields?object_type={$objectType}");
    }

    // ============================================================
    // Files & Attachments
    // ============================================================

    /**
     * Upload a file to Gorgias.
     *
     * @param string $filePath Path to the file to upload
     * @param string $fileLabel Unique label for the file
     * @param string $type Type of upload ('attachment', 'public_attachment', 'profile_picture', 'widget_picture')
     */
    public function upload(string $filePath, string $fileLabel, string $type = 'attachment'): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->getAuthHeader(),
            'Accept' => 'application/json',
        ])
            ->attach(
                'file',                             // field name required by Gorgias
                fopen($filePath, 'r'),
                $fileLabel                          // actual filename to display
            )
            ->post("{$this->baseUrl}/upload?type={$type}");

        return $response->json();
    }

    /**
     * Download a file from Gorgias attachment.
     *
     * @param string $attachmentPath The attachment path from Gorgias (e.g., Q9r0k7BmmJ92vg13/c5e9c7da-8688-4e2e-8ed3-803663a013fe-IMG_5582.HEIC)
     */
    public function downloadAttachment(string $attachmentPath): string
    {
        $response = Http::withHeaders([
            'Authorization' => $this->getAuthHeader(),
        ])->get("{$this->baseUrl}/attachment/download/{$attachmentPath}");

        return $response->body();
    }
}
