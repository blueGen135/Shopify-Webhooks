<?php

namespace App\Traits;

use App\Services\GorgiasService;
use Illuminate\Support\Facades\Log;

trait MessageHelper
{
    /**
     * Send a message to Gorgias ticket with optional attachments
     *
     * @param int $ticketId Gorgias ticket ID
     * @param string $messageBody Message body text
     * @param array $options Optional parameters:
     *   - 'messageType' => 'email' or 'internal-note' (default: 'email')
     *   - 'attachments' => array of file paths to upload
     *   - 'customerId' => Gorgias customer ID (optional)
     *   - 'customerEmail' => Customer email address (optional)
     *   - 'customerName' => Customer name (optional)
     * @return bool Success status
     */
    public function sendGorgiasMessage(int $ticketId, string $messageBody, array $options = []): bool
    {
        try {
            // Get current user info
            $user = auth()->user();

            // Check if user has Gorgias user ID - required to send messages
            if (empty($user->gorgias_user_id)) {
                throw new \Exception('You do not have permission to send messages. Please contact an administrator to link your Gorgias account.');
            }

            $senderEmail = $user->gorgias_email ?? $user->email ?? 'support@example.com';
            $senderName = $user->gorgias_name ?? $user->name ?? 'Support Agent';
            $messageType = $options['messageType'] ?? 'email';

            // Handle attachments if provided
            $attachments = [];
            if (!empty($options['attachments'])) {
                $gorgiasService = new GorgiasService();
                foreach ($options['attachments'] as $filePath) {
                    try {
                        $uploadedFile = $gorgiasService->uploadAttachment($filePath);
                        $attachments[] = [
                            'name' => $uploadedFile['name'] ?? basename($filePath),
                            'content_type' => $uploadedFile['content_type'] ?? 'application/octet-stream',
                            'url' => $uploadedFile['url'] ?? '',
                            'size' => $uploadedFile['size'] ?? 0,
                        ];
                    } catch (\Exception $e) {
                        Log::error('Failed to upload attachment: ' . $e->getMessage());
                    }
                }
            }

            // Prepare message data for Gorgias API
            $messageData = [
                'channel' => 'api',
                'from_agent' => true,
                'via' => 'api',
                'body_text' => $messageBody,
                'body_html' => nl2br(e($messageBody)),
                'sender' => [
                    'id' => $user->gorgias_user_id,
                ],
            ];

            // Add receiver if customer info provided
            if (!empty($options['customerId'])) {
                $messageData['receiver'] = [
                    'id' => $options['customerId'],
                ];
            }

            // Add source information
            $sourceType = $messageType === 'internal-note' ? 'internal-note' : 'api';
            $messageData['source'] = [
                'type' => $sourceType,
                'from' => [
                    'name' => $senderName,
                    'address' => $senderEmail,
                ],
            ];

            // Add 'to' field for email messages
            if ($messageType !== 'internal-note' && (!empty($options['customerEmail']) || !empty($options['customerName']))) {
                $messageData['source']['to'] = [
                    [
                        'name' => $options['customerName'] ?? 'Customer',
                        'address' => $options['customerEmail'] ?? '',
                    ]
                ];
            }

            // Add attachments if any
            if (!empty($attachments)) {
                $messageData['attachments'] = $attachments;
            }

            // Send message to Gorgias
            (new GorgiasService())->createTicketMessage($ticketId, $messageData);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send Gorgias message: ' . $e->getMessage());
            throw $e;
        }
    }
    public function isInternalNote($message)
    {
        return isset($message['source']) && isset($message['source']['type']) && $message['source']['type'] === 'internal-note';
    }

    public function isAgentMessage($message)
    {
        return isset($message['from_agent']) && $message['from_agent'] === true;
    }

    /**
     * Extract all image attachments from messages grouped by date
     */
    public function getImageAttachments()
    {
        $groupedAttachments = [];

        foreach ($this->messages as $message) {
            if (!$this->isAgentMessage($message) && !empty($message['attachments'])) {
                foreach ($message['attachments'] as $attachment) {
                    if (
                        isset($attachment['content_type']) &&
                        str_starts_with($attachment['content_type'], 'image/') &&
                        !empty($attachment['url'])
                    ) {
                        $date = $message['created_datetime'] ? \Carbon\Carbon::parse($message['created_datetime'])->format('Y-m-d') : 'unknown';

                        if (!isset($groupedAttachments[$date])) {
                            $groupedAttachments[$date] = [
                                'date' => $date,
                                'formatted_date' => $message['created_datetime'] ? \Carbon\Carbon::parse($message['created_datetime'])->format('jS M Y') : 'Unknown Date',
                                'images' => []
                            ];
                        }

                        $groupedAttachments[$date]['images'][] = [
                            'url' => $attachment['url'],
                            'name' => $attachment['name'] ?? 'Image',
                            'created_at' => $message['created_datetime'] ?? null,
                        ];
                    }
                }
            }
        }

        // Sort by date descending (newest first)
        krsort($groupedAttachments);

        return $groupedAttachments;
    }
}
