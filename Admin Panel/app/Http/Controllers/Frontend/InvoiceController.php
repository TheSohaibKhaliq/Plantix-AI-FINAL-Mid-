<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * InvoiceController
 *
 * Generates and streams a downloadable HTML invoice for a customer order.
 * No external PDF library is required – the invoice is a print-friendly
 * HTML document. Users may print-to-PDF from their browser.
 */
class InvoiceController extends Controller
{
    /**
     * Stream an HTML invoice as a downloadable file.
     * Route: GET /orders/{id}/invoice
     */
    public function download(int $id): StreamedResponse
    {
        $user  = auth('web')->user();
        $order = Order::with([
                         'user',
                         'vendor',
                         'items.product',
                         'coupon',
                         'statusHistory',
                     ])
                     ->forCustomer($user->id)
                     ->findOrFail($id);

        $html = view('customer.invoice', compact('order'))->render();

        return response()->streamDownload(
            static function () use ($html) { echo $html; },
            "invoice-{$order->order_number}.html",
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }
}
