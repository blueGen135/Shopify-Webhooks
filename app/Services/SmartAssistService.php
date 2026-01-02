<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Traits\MessageHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SmartAssist Service
 * 
 * This service provides an abstraction layer for AI-powered response generation.
 * It currently supports OpenAI and is designed to easily support additional providers
 * like Google Gemini in the future.
 * 
 * Configuration is managed through the Settings page (Smart Assist section) where
 * you can configure:
 * - Provider (OpenAI, Gemini, etc.)
 * - API Key
 * - Model (GPT-4o, GPT-5, etc.)
 * - System Prompt (Custom instructions for the AI)
 * 
 * Usage Example:
 * ```php
 * $smartAssist = new SmartAssistService();
 * $response = $smartAssist->generateResponse($messages, [
 *     'temperature' => 0.7,
 * ]);
 * ```
 */
class SmartAssistService
{
  use MessageHelper;

  private $provider;
  private $apiKey;
  private $model;

  public function __construct()
  {
    $this->provider = Helpers::setting('smart_assist_provider') ?? 'openai';
    $this->apiKey = Helpers::setting('smart_assist_api_key');
    $this->model = Helpers::setting('smart_assist_model') ?? 'gpt-4o';
  }

  /**
   * Generate a response based on conversation messages
   *
   * @param array $messages Array of conversation messages
   * @param array $options Additional options for generation
   * @return string Generated response
   * @throws \Exception
   */
  public function generateResponse(array $messages, array $options = []): string
  {
    if (!$this->apiKey) {
      throw new \Exception('Smart Assist API key is not configured. Please configure it in Settings.');
    }

    switch ($this->provider) {
      case 'openai':
        return $this->generateOpenAIResponse($messages, $options);
      case 'gemini':
        return $this->generateGeminiResponse($messages, $options);
      default:
        throw new \Exception("Unsupported AI provider: {$this->provider}");
    }
  }

  /**
   * Generate response using OpenAI
   *
   * @param array $messages
   * @param array $options
   * @return string
   * @throws \Exception
   */
  private function generateOpenAIResponse(array $messages, array $options = []): string
  {
    try {
      // Get system prompt from settings or options
      $systemPrompt = $options['system_prompt'] ?? Helpers::setting('smart_assist_system_prompt') ?? $this->getDefaultSystemPrompt();

      // Format messages for OpenAI
      $formattedMessages = [
        [
          'role' => 'system',
          'content' => $systemPrompt
        ]
      ];

      // Add conversation history
      foreach ($messages as $message) {
        if ($this->isInternalNote($message)) {
          continue; // Skip internal notes
        }

        $role = $this->determineMessageRole($message);
        $content = strip_tags($message['body_html'] ?? $message['body_text'] ?? '');

        if (!empty($content)) {
          $formattedMessages[] = [
            'role' => $role,
            'content' => $content,
          ];
        }
      }

      // Prepare API request payload
      $payload = [
        'model' => $this->model,
        'messages' => $formattedMessages,
      ];

      // Some newer models (like GPT-5) don't support temperature parameter
      // Only add temperature for models that support it (GPT-3.5, GPT-4, GPT-4o)
      if ($this->modelSupportsTemperature()) {
        $payload['temperature'] = $options['temperature'] ?? 0.7;
      }

      // Make API request to OpenAI
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Content-Type' => 'application/json',
      ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', $payload);

      if (!$response->successful()) {
        $error = $response->json('error.message') ?? 'Unknown error';
        Log::error('OpenAI API Error', [
          'status' => $response->status(),
          'error' => $error,
          'response' => $response->body()
        ]);
        throw new \Exception("OpenAI API Error: {$error}");
      }

      $responseData = $response->json();

      if (isset($responseData['choices'][0]['message']['content'])) {
        return trim($responseData['choices'][0]['message']['content']);
      }

      throw new \Exception('Invalid response format from OpenAI');

    } catch (\Exception $e) {
      Log::error('Smart Assist OpenAI Error', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      throw $e;
    }
  }

  /**
   * Generate response using Google Gemini (placeholder for future implementation)
   *
   * @param array $messages
   * @param array $options
   * @return string
   * @throws \Exception
   */
  private function generateGeminiResponse(array $messages, array $options = []): string
  {
    // Placeholder for Gemini implementation
    throw new \Exception('Gemini provider is not yet implemented. Please use OpenAI for now.');
  }

  /**
   * Determine the role of a message (user or assistant)
   *
   * @param array $message
   * @return string
   */
  private function determineMessageRole(array $message): string
  {
    if ($this->isAgentMessage($message)) {
      return 'assistant';
    }

    // Default to user
    return 'user';
  }

  /**
   * Check if the current model supports temperature parameter
   *
   * @return bool
   */
  private function modelSupportsTemperature(): bool
  {
    // GPT-5 models don't support custom temperature (only default 1)
    // GPT-4 and GPT-3.5 models support custom temperature
    $modelsWithoutTemperature = ['gpt-5', 'gpt-5-turbo'];

    return !in_array($this->model, $modelsWithoutTemperature);
  }

  /**
   * Get default system prompt for customer support
   *
   * @return string
   */
  private function getDefaultSystemPrompt(): string
  {
    return "You are a professional and empathetic customer support representative for an e-commerce company. 
Your role is to:
- Provide helpful, clear, and concise responses to customer inquiries
- Show empathy and understanding for customer concerns
- Offer solutions and next steps when applicable
- Maintain a professional yet friendly tone
- Be honest if you don't have enough information to help
- Suggest escalation to a supervisor if the issue is complex

Keep responses concise (2-3 paragraphs maximum) and actionable. Always end with a clear next step or question to continue helping the customer.";
  }

  /**
   * Test the API connection
   *
   * @return array
   */
  public function testConnection(): array
  {
    try {
      if (!$this->apiKey) {
        return [
          'success' => false,
          'message' => 'API key is not configured'
        ];
      }

      switch ($this->provider) {
        case 'openai':
          $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
          ])->timeout(10)->get('https://api.openai.com/v1/models');

          if ($response->successful()) {
            return [
              'success' => true,
              'message' => 'Connection successful'
            ];
          }

          return [
            'success' => false,
            'message' => 'Invalid API key or connection failed'
          ];

        default:
          return [
            'success' => false,
            'message' => 'Unsupported provider'
          ];
      }
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }
}
