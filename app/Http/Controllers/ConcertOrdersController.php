<?php

namespace App\Http\Controllers;

use App\Billing\NotEnoughTicketsException;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $ticketQuantity = request('ticket_quantity');
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(
            request(),
            [
                'email' => ['required', 'email'],
                'ticket_quantity' => ['required', 'integer', 'min:1'],
                'payment_token' => ['required'],
            ]
        );

        try {
            // Find some tickets
            $tickets = $concert->findTickets($ticketQuantity);

            // Charge the customer for the tickets
            $this->paymentGateway->charge($ticketQuantity * $concert->ticket_price, request('payment_token'));

            // Create an order for those tickets
            $order = $concert->createOrder(request('email'), $tickets);

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            $this->paymentGateway->chargeBack($ticketQuantity * $concert->ticket_price, request('payment_token'));

            return response()->json([], 422);
        }
    }
}
