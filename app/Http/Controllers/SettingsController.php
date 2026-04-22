<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            // General
            'system_name'        => Setting::get('system_name', 'MakaziLink v2'),
            'company_name'       => Setting::get('company_name', ''),
            'company_phone'      => Setting::get('company_phone', ''),
            'company_email'      => Setting::get('company_email', ''),
            'company_address'    => Setting::get('company_address', ''),
            'currency'           => Setting::get('currency', 'KES'),
            'timezone'           => Setting::get('timezone', 'Africa/Nairobi'),

            // Rent
            'default_notice_days'    => Setting::get('default_notice_days', 30),
            'late_payment_penalty'   => Setting::get('late_payment_penalty', 0),
            'rent_due_day'           => Setting::get('rent_due_day', 1),

            // Invoice
            'invoice_prefix'     => Setting::get('invoice_prefix', 'INV'),
            'receipt_prefix'     => Setting::get('receipt_prefix', 'RCP'),
            'invoice_due_days'   => Setting::get('invoice_due_days', 5),
            'invoice_notes'      => Setting::get('invoice_notes', ''),

            // Notifications
            'lease_alert_days'   => Setting::get('lease_alert_days', 30),
            'send_whatsapp'      => Setting::get('send_whatsapp', '0'),

            // Water
            'default_water_rate' => Setting::get('default_water_rate', 0),

            // MPesa
            'mpesa_environment'    => Setting::get('mpesa_environment', 'sandbox'),
            'mpesa_consumer_key'   => Setting::get('mpesa_consumer_key', ''),
            'mpesa_consumer_secret'=> Setting::get('mpesa_consumer_secret', ''),
            'mpesa_shortcode'      => Setting::get('mpesa_shortcode', '174379'),
            'mpesa_passkey'        => Setting::get('mpesa_passkey', ''),
            'mpesa_callback_url'   => Setting::get('mpesa_callback_url', ''),
        ];

        // System stats
        $stats = [
            'total_properties'  => Property::count(),
            'total_units'       => Unit::count(),
            'total_tenants'     => Tenant::count(),
            'total_payments'    => Payment::count(),
            'total_invoices'    => Invoice::count(),
            'total_maintenance' => MaintenanceRequest::count(),
        ];

        return view('settings.index', compact('settings', 'stats'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_name'          => 'required|string|max:255',
            'company_name'         => 'nullable|string|max:255',
            'company_phone'        => 'nullable|string|max:20',
            'company_email'        => 'nullable|email|max:255',
            'company_address'      => 'nullable|string|max:500',
            'currency'             => 'required|string|max:10',
            'timezone'             => 'required|string|max:50',
            'default_notice_days'  => 'required|integer|min:1',
            'late_payment_penalty' => 'required|numeric|min:0',
            'rent_due_day'         => 'required|integer|min:1|max:28',
            'invoice_prefix'       => 'required|string|max:10',
            'receipt_prefix'       => 'required|string|max:10',
            'invoice_due_days'     => 'required|integer|min:1',
            'invoice_notes'        => 'nullable|string|max:500',
            'lease_alert_days'     => 'required|integer|min:1',
            'default_water_rate'   => 'required|numeric|min:0',
        ]);

        $groups = [
            'system_name'          => 'general',
            'company_name'         => 'general',
            'company_phone'        => 'general',
            'company_email'        => 'general',
            'company_address'      => 'general',
            'currency'             => 'general',
            'timezone'             => 'general',
            'default_notice_days'  => 'rent',
            'late_payment_penalty' => 'rent',
            'rent_due_day'         => 'rent',
            'invoice_prefix'       => 'invoice',
            'receipt_prefix'       => 'invoice',
            'invoice_due_days'     => 'invoice',
            'invoice_notes'        => 'invoice',
            'lease_alert_days'     => 'notifications',
            'send_whatsapp'        => 'notifications',
            'default_water_rate'   => 'water',
            'mpesa_environment'     => 'mpesa',
            'mpesa_consumer_key'    => 'mpesa',
            'mpesa_consumer_secret' => 'mpesa',
            'mpesa_shortcode'       => 'mpesa',
            'mpesa_passkey'         => 'mpesa',
            'mpesa_callback_url'    => 'mpesa',
        ];

        foreach ($groups as $key => $group) {
            Setting::set($key, $request->input($key, ''), $group);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully.');
    }
}