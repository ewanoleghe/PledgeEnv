<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\BookingRequest;
use Stripe\PaymentIntent;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload, 
                $sigHeader, 
                $endpointSecret
            );
            
            Log::info('Stripe webhook event received', ['type' => $event->type]);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                
                default:
                    Log::info('Unhandled Stripe event type', ['type' => $event->type]);
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    protected function handlePaymentSucceeded(PaymentIntent $paymentIntent)
    {
        // Validate metadata exists
        if (!$paymentIntent->metadata->has('order_id')) {
            Log::error('Missing order_id in PaymentIntent metadata', [
                'payment_intent' => $paymentIntent->id,
                'metadata' => $paymentIntent->metadata->toArray()
            ]);
            return;
        }

        $orderId = $paymentIntent->metadata->get('order_id');
        $bookingRequestId = $paymentIntent->metadata->get('booking_request_id');

        Log::debug('Processing payment success', [
            'order_id' => $orderId,
            'booking_request_id' => $bookingRequestId,
            'payment_intent' => $paymentIntent->id
        ]);

        // Find booking request
        $bookingRequest = BookingRequest::where('order_id', $orderId)->first();

        if (!$bookingRequest) {
            Log::error('BookingRequest not found', [
                'order_id' => $orderId,
                'payment_intent' => $paymentIntent->id
            ]);
            return;
        }

        // Prevent duplicate processing
        if ($bookingRequest->payment_status === 'succeeded') {
            Log::warning('Payment already marked as succeeded', [
                'booking_request' => $bookingRequest->id,
                'payment_intent' => $paymentIntent->id
            ]);
            return;
        }

        try {
            // Update booking request
            $bookingRequest->update([
                'payment_status' => 'succeeded',
                'stripe_payment_intent' => $paymentIntent->id
            ]);

            Log::info('Successfully updated booking request', [
                'booking_request' => $bookingRequest->id,
                'payment_intent' => $paymentIntent->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update booking request', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'payment_intent' => $paymentIntent->id
            ]);
        }
    }
}