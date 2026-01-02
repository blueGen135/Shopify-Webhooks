<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\GorgiasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GorgiasWebhookController extends Controller
{
  /**
   * Handle incoming Gorgias webhook for ticket events
   */
  public function handleTicket(Request $request)
  {
    try {
      $payload = $request->all();
      // Log the incoming webhook for debugging
      Log::info('Gorgias webhook received', [
        'event' => $request->header('X-Gorgias-Event'),
        'payload' => $payload
      ]);

      $data = $payload['payload']['ticket'] ?? [];

      if (!isset($data['id'])) {
        return response()->json([
          'success' => false,
          'message' => 'Invalid payload: missing ticket ID'
        ], 400);
      }

      // ignore if the system generated email
      if (isset($data['customer']['email']) && $data['customer']['email'] === 'mailer-daemon@googlemail.com') {
        Log::info('Ignoring system generated email ticket', [
          'gorgias_ticket_id' => $data['id']
        ]);

        return response()->json([
          'success' => true,
          'message' => 'Ignored system generated email ticket'
        ], 200);
      }

      $ticket = (new GorgiasService)->ticket($data['id']);

      // Sync ticket from Gorgias data
      $ticket = Ticket::syncFromGorgias($ticket);

      Log::info('Ticket synced successfully', [
        'ticket_id' => $ticket->id,
        'gorgias_ticket_id' => $ticket->gorgias_ticket_id
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Ticket synced successfully',
        'ticket_id' => $ticket->id
      ], 200);

    } catch (\Exception $e) {
      Log::error('Gorgias webhook error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Internal server error',
        'error' => config('app.debug') ? $e->getMessage() : null
      ], 500);
    }
  }

  /**
   * Verify webhook endpoint (for initial setup)
   */
  public function verify(Request $request)
  {
    return response()->json([
      'status' => 'ok',
      'message' => 'Gorgias webhook endpoint is active'
    ], 200);
  }
}
