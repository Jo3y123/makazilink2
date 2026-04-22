<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\WaterReading;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);
        $message  = strtolower(trim($request->message));
        $user     = auth()->user();
        $role     = $user->role;
        $response = $this->processMessage($message, $user, $role);
        return response()->json(['reply' => $response]);
    }

    private function processMessage(string $message, $user, string $role): string
    {
        // Greetings
        if ($this->matches($message, ['hello', 'hey', 'good morning', 'good afternoon', 'good evening']) || $message === 'hi') {
            return "Hello {$user->name}! I am your MakaziLink assistant. Type **help** to see what I can do.";
        }

        // Help
        if ($this->matches($message, ['help', 'what can you do', 'commands', 'options', 'menu'])) {
            return $this->getHelpMessage($role);
        }

        // ── ADMIN / AGENT ──────────────────────────────────
        if (in_array($role, ['admin', 'agent'])) {

            // Properties
            if ($this->matches($message, ['total propert', 'how many propert', 'number of propert', 'list propert'])) {
                $count = Property::count();
                $props = Property::all()->map(fn($p) => "• {$p->name} — {$p->town}")->join("\n");
                return "There are **{$count}** properties:\n\n{$props}";
            }

            if ($this->matches($message, ['property type', 'type of propert'])) {
                $types = Property::selectRaw('type, count(*) as count')->groupBy('type')->get();
                $list  = $types->map(fn($t) => "• " . ucfirst(str_replace('_', ' ', $t->type)) . ": {$t->count}")->join("\n");
                return "Properties by type:\n\n{$list}";
            }

            // Units
            if ($this->matches($message, ['vacant', 'empty unit', 'available unit'])) {
                $units = Unit::with('property')->where('status', 'vacant')->get();
                if ($units->isEmpty()) return "There are no vacant units currently.";
                $list = $units->map(fn($u) => "• {$u->unit_number} — {$u->property->name} (KES " . number_format($u->rent_amount) . ")")->join("\n");
                return "**{$units->count()}** vacant unit(s):\n\n{$list}";
            }

            if ($this->matches($message, ['occupied unit', 'rented unit', 'taken unit'])) {
                $count = Unit::where('status', 'occupied')->count();
                return "There are currently **{$count}** occupied units.";
            }

            if ($this->matches($message, ['total unit', 'how many unit', 'number of unit', 'all unit'])) {
                $total    = Unit::count();
                $occupied = Unit::where('status', 'occupied')->count();
                $vacant   = Unit::where('status', 'vacant')->count();
                $maintenance = Unit::where('status', 'under_maintenance')->count();
                return "Unit summary:\n\n• Total: **{$total}**\n• Occupied: **{$occupied}**\n• Vacant: **{$vacant}**\n• Under Maintenance: **{$maintenance}**";
            }

            if ($this->matches($message, ['occupancy rate', 'occupancy percent'])) {
                $total    = Unit::count();
                $occupied = Unit::where('status', 'occupied')->count();
                $rate     = $total > 0 ? round(($occupied / $total) * 100) : 0;
                return "Current occupancy rate is **{$rate}%** ({$occupied} out of {$total} units occupied).";
            }

            if ($this->matches($message, ['under maintenance unit', 'unit under maintenance', 'maintenance unit'])) {
                $units = Unit::with('property')->where('status', 'under_maintenance')->get();
                if ($units->isEmpty()) return "No units are currently under maintenance.";
                $list = $units->map(fn($u) => "• {$u->unit_number} — {$u->property->name}")->join("\n");
                return "**{$units->count()}** unit(s) under maintenance:\n\n{$list}";
            }

            if ($this->matches($message, ['unit with water meter', 'water meter unit'])) {
                $count = Unit::where('has_water_meter', true)->count();
                return "**{$count}** unit(s) have water meters installed.";
            }

            // Tenants
            if ($this->matches($message, ['total tenant', 'how many tenant', 'number of tenant', 'all tenant'])) {
                $count = Tenant::count();
                return "There are currently **{$count}** registered tenants.";
            }

            if ($this->matches($message, ['new tenant', 'tenant this month', 'tenant added this month'])) {
                $count = Tenant::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                return "**{$count}** new tenant(s) were registered this month.";
            }

            if ($this->matches($message, ['tenant without lease', 'tenant no lease', 'unassigned tenant'])) {
                $count = Tenant::doesntHave('leases')->count();
                return "**{$count}** tenant(s) do not have any lease assigned.";
            }

            if ($this->matches($message, ['list tenant', 'show tenant', 'all tenant name'])) {
                $tenants = Tenant::with('user')->take(10)->get();
                $list    = $tenants->map(fn($t) => "• {$t->user->name} — {$t->user->phone}")->join("\n");
                return "Tenants (showing first 10):\n\n{$list}";
            }

            // Leases
            if ($this->matches($message, ['active lease', 'current lease', 'how many lease'])) {
                $count = Lease::where('status', 'active')->count();
                return "There are **{$count}** active leases.";
            }

            if ($this->matches($message, ['expiring lease', 'lease expir', 'lease ending', 'lease expire'])) {
                $leases = Lease::with('tenant.user', 'unit')
                    ->where('status', 'active')
                    ->whereNotNull('end_date')
                    ->whereDate('end_date', '<=', now()->addDays(30))
                    ->get();
                if ($leases->isEmpty()) return "No leases are expiring within the next 30 days.";
                $list = $leases->map(fn($l) => "• {$l->tenant->user->name} — Unit {$l->unit->unit_number} (expires {$l->end_date->format('d M Y')})")->join("\n");
                return "**{$leases->count()}** lease(s) expiring within 30 days:\n\n{$list}";
            }

            if ($this->matches($message, ['terminated lease', 'cancelled lease', 'ended lease'])) {
                $count = Lease::where('status', 'terminated')->count();
                return "There are **{$count}** terminated leases on record.";
            }

            if ($this->matches($message, ['expired lease'])) {
                $count = Lease::where('status', 'expired')->count();
                return "There are **{$count}** expired leases on record.";
            }

            if ($this->matches($message, ['open ended lease', 'no end date lease', 'permanent lease'])) {
                $count = Lease::where('status', 'active')->whereNull('end_date')->count();
                return "**{$count}** active lease(s) have no end date (open ended).";
            }
        }

        // ── ADMIN / ACCOUNTANT ─────────────────────────────
        if (in_array($role, ['admin', 'accountant'])) {

            if ($this->matches($message, ['this month revenue', 'revenue this month', 'current month revenue', 'how much this month'])) {
                $amount = Payment::whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->where('status', 'confirmed')->sum('amount');
                return "This month's revenue is **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['last month revenue', 'previous month revenue', 'how much last month'])) {
                $amount = Payment::whereMonth('payment_date', now()->subMonth()->month)
                    ->whereYear('payment_date', now()->subMonth()->year)
                    ->where('status', 'confirmed')->sum('amount');
                return "Last month's revenue was **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['total revenue', 'all time revenue', 'total income', 'total collected'])) {
                $amount = Payment::where('status', 'confirmed')->sum('amount');
                return "Total all-time revenue collected is **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['today payment', 'payment today', 'how much today'])) {
                $amount = Payment::whereDate('payment_date', today())->where('status', 'confirmed')->sum('amount');
                $count  = Payment::whereDate('payment_date', today())->where('status', 'confirmed')->count();
                return "Today's payments: **{$count}** transaction(s) totalling **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['this week payment', 'payment this week', 'weekly payment'])) {
                $amount = Payment::whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->where('status', 'confirmed')->sum('amount');
                return "This week's payments total **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['mpesa payment', 'mpesa total', 'paid via mpesa', 'm-pesa'])) {
                $amount = Payment::where('payment_method', 'mpesa')->where('status', 'confirmed')->sum('amount');
                $count  = Payment::where('payment_method', 'mpesa')->where('status', 'confirmed')->count();
                return "M-Pesa payments: **{$count}** transactions totalling **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['cash payment', 'paid cash', 'cash total'])) {
                $amount = Payment::where('payment_method', 'cash')->where('status', 'confirmed')->sum('amount');
                $count  = Payment::where('payment_method', 'cash')->where('status', 'confirmed')->count();
                return "Cash payments: **{$count}** transactions totalling **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['bank payment', 'bank transfer', 'paid via bank'])) {
                $amount = Payment::where('payment_method', 'bank_transfer')->where('status', 'confirmed')->sum('amount');
                $count  = Payment::where('payment_method', 'bank_transfer')->where('status', 'confirmed')->count();
                return "Bank transfer payments: **{$count}** transactions totalling **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['total payment', 'how many payment', 'number of payment', 'all payment'])) {
                $count  = Payment::where('status', 'confirmed')->count();
                $amount = Payment::where('status', 'confirmed')->sum('amount');
                return "There are **{$count}** confirmed payments totalling **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['unpaid invoice', 'outstanding invoice', 'pending invoice', 'not paid invoice'])) {
                $count  = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count();
                $amount = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance');
                return "**{$count}** unpaid invoice(s) with total outstanding balance of **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['overdue invoice', 'late invoice', 'overdue payment'])) {
                $count  = Invoice::where('status', 'overdue')->count();
                $amount = Invoice::where('status', 'overdue')->sum('balance');
                return "**{$count}** overdue invoice(s) with total balance of **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['paid invoice', 'cleared invoice', 'fully paid invoice'])) {
                $count = Invoice::where('status', 'paid')->count();
                return "**{$count}** invoice(s) have been fully paid.";
            }

            if ($this->matches($message, ['partial payment', 'partial invoice', 'half paid'])) {
                $count  = Invoice::where('status', 'partial')->count();
                $amount = Invoice::where('status', 'partial')->sum('balance');
                return "**{$count}** invoice(s) are partially paid with remaining balance of **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['total invoice', 'how many invoice', 'number of invoice', 'all invoice'])) {
                $total   = Invoice::count();
                $paid    = Invoice::where('status', 'paid')->count();
                $unpaid  = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count();
                return "Invoice summary:\n\n• Total: **{$total}**\n• Paid: **{$paid}**\n• Unpaid: **{$unpaid}**";
            }

            if ($this->matches($message, ['outstanding balance', 'total outstanding', 'total owed', 'total debt'])) {
                $amount = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance');
                return "Total outstanding balance across all tenants is **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['invoice this month', 'invoice created this month'])) {
                $count  = Invoice::whereMonth('created_at', now()->month)->count();
                $amount = Invoice::whereMonth('created_at', now()->month)->sum('total_amount');
                return "**{$count}** invoice(s) created this month totalling **KES " . number_format($amount) . "**.";
            }
        }

        // ── ADMIN / CARETAKER ──────────────────────────────
        if (in_array($role, ['admin', 'caretaker'])) {

            if ($this->matches($message, ['total maintenance', 'all maintenance', 'maintenance summary', 'maintenance request'])) {
                $open       = MaintenanceRequest::where('status', 'open')->count();
                $inProgress = MaintenanceRequest::where('status', 'in_progress')->count();
                $resolved   = MaintenanceRequest::where('status', 'resolved')->count();
                $closed     = MaintenanceRequest::where('status', 'closed')->count();
                return "Maintenance summary:\n\n• Open: **{$open}**\n• In Progress: **{$inProgress}**\n• Resolved: **{$resolved}**\n• Closed: **{$closed}**";
            }

            if ($this->matches($message, ['open maintenance', 'pending maintenance', 'unresolved maintenance'])) {
                $requests = MaintenanceRequest::with('unit.property')
                    ->where('status', 'open')->get();
                if ($requests->isEmpty()) return "No open maintenance requests.";
                $list = $requests->map(fn($r) => "• {$r->title} — Unit {$r->unit->unit_number}, {$r->unit->property->name}")->join("\n");
                return "**{$requests->count()}** open maintenance request(s):\n\n{$list}";
            }

            if ($this->matches($message, ['urgent maintenance', 'emergency maintenance', 'urgent repair'])) {
                $requests = MaintenanceRequest::with('unit')
                    ->where('priority', 'urgent')
                    ->whereIn('status', ['open', 'in_progress'])->get();
                if ($requests->isEmpty()) return "No urgent maintenance requests currently.";
                $list = $requests->map(fn($r) => "• {$r->title} — Unit {$r->unit->unit_number}")->join("\n");
                return "**{$requests->count()}** urgent maintenance request(s):\n\n{$list}";
            }

            if ($this->matches($message, ['high priority maintenance', 'high maintenance'])) {
                $count = MaintenanceRequest::where('priority', 'high')->whereIn('status', ['open', 'in_progress'])->count();
                return "There are **{$count}** high priority maintenance requests.";
            }

            if ($this->matches($message, ['plumbing', 'plumbing issue', 'plumbing request'])) {
                $count = MaintenanceRequest::where('category', 'plumbing')->whereIn('status', ['open', 'in_progress'])->count();
                return "There are **{$count}** open plumbing requests.";
            }

            if ($this->matches($message, ['electrical', 'electrical issue', 'electrical request'])) {
                $count = MaintenanceRequest::where('category', 'electrical')->whereIn('status', ['open', 'in_progress'])->count();
                return "There are **{$count}** open electrical requests.";
            }

            if ($this->matches($message, ['maintenance this month', 'repair this month'])) {
                $count = MaintenanceRequest::whereMonth('created_at', now()->month)->count();
                return "**{$count}** maintenance request(s) were submitted this month.";
            }

            if ($this->matches($message, ['resolved maintenance', 'fixed maintenance', 'completed maintenance'])) {
                $count = MaintenanceRequest::where('status', 'resolved')->count();
                return "**{$count}** maintenance request(s) have been resolved.";
            }

            if ($this->matches($message, ['maintenance cost', 'repair cost', 'total repair cost'])) {
                $amount = MaintenanceRequest::whereNotNull('cost')->sum('cost');
                return "Total maintenance costs recorded: **KES " . number_format($amount) . "**.";
            }

            if ($this->matches($message, ['water reading', 'meter reading', 'water this month'])) {
                $count = WaterReading::whereMonth('reading_date', now()->month)->count();
                $total = WaterReading::whereMonth('reading_date', now()->month)->sum('amount_charged');
                return "**{$count}** water reading(s) recorded this month, total charged: **KES " . number_format($total) . "**.";
            }

            if ($this->matches($message, ['total water', 'all water reading', 'water reading total'])) {
                $count = WaterReading::count();
                $total = WaterReading::sum('amount_charged');
                return "Total water readings: **{$count}**, total amount charged: **KES " . number_format($total) . "**.";
            }

            if ($this->matches($message, ['unit with meter', 'metered unit', 'water meter'])) {
                $count = Unit::where('has_water_meter', true)->count();
                return "**{$count}** unit(s) have water meters installed.";
            }
        }

        // ── TENANT ─────────────────────────────────────────
        if ($role === 'tenant') {
            $tenant = $user->tenant;

            if (!$tenant) {
                return "Your tenant profile is not set up yet. Please contact your landlord.";
            }

            $lease = $tenant->activeLease;

            if ($this->matches($message, ['my rent', 'rent amount', 'how much is my rent', 'monthly rent'])) {
                if (!$lease) return "You do not have an active lease. Contact your landlord.";
                return "Your monthly rent is **KES " . number_format($lease->monthly_rent) . "**.";
            }

            if ($this->matches($message, ['due date', 'when is rent due', 'next payment', 'next due', 'when should i pay'])) {
                if (!$lease) return "You do not have an active lease. Contact your landlord.";
                $due = $lease->next_due_date ? $lease->next_due_date->format('d M Y') : 'Not set';
                return "Your next rent due date is **{$due}**.";
            }

            if ($this->matches($message, ['my balance', 'how much do i owe', 'outstanding balance', 'what do i owe'])) {
                $balance = Invoice::where('tenant_id', $tenant->id)
                    ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
                    ->sum('balance');
                return "Your current outstanding balance is **KES " . number_format($balance) . "**.";
            }

            if ($this->matches($message, ['my lease', 'my contract', 'lease detail', 'my agreement'])) {
                if (!$lease) return "You do not have an active lease. Contact your landlord.";
                $end = $lease->end_date ? $lease->end_date->format('d M Y') : 'Open ended';
                return "Your lease details:\n\n• Start: **" . $lease->start_date->format('d M Y') . "**\n• End: **{$end}**\n• Rent: **KES " . number_format($lease->monthly_rent) . "**\n• Status: **" . ucfirst($lease->status) . "**";
            }

            if ($this->matches($message, ['when does my lease expire', 'lease expiry', 'when does lease end', 'lease end date'])) {
                if (!$lease) return "You do not have an active lease.";
                if (!$lease->end_date) return "Your lease is open ended with no expiry date.";
                return "Your lease expires on **" . $lease->end_date->format('d M Y') . "** (" . $lease->days_until_expiry . " days remaining).";
            }

            if ($this->matches($message, ['my unit', 'my room', 'my apartment', 'which unit am i in'])) {
                if (!$lease) return "You are not assigned to a unit yet.";
                return "You are in unit **" . $lease->unit->unit_number . "** at **" . $lease->unit->property->name . "**, " . $lease->unit->property->address . ".";
            }

            if ($this->matches($message, ['my property', 'where do i live', 'my building', 'my address'])) {
                if (!$lease) return "You are not assigned to a property yet.";
                return "You live at **" . $lease->unit->property->name . "**, " . $lease->unit->property->address . ", " . $lease->unit->property->town . ".";
            }

            if ($this->matches($message, ['my payment history', 'past payment', 'my payment', 'payments i made'])) {
                $payments = Payment::where('tenant_id', $tenant->id)
                    ->where('status', 'confirmed')->latest()->take(5)->get();
                if ($payments->isEmpty()) return "No payment history found.";
                $list = $payments->map(fn($p) => "• {$p->receipt_number} — KES " . number_format($p->amount) . " on " . $p->payment_date->format('d M Y') . " via " . ucfirst(str_replace('_', ' ', $p->payment_method)))->join("\n");
                return "Your last {$payments->count()} payment(s):\n\n{$list}";
            }

            if ($this->matches($message, ['total paid', 'how much have i paid', 'total payment made'])) {
                $total = Payment::where('tenant_id', $tenant->id)->where('status', 'confirmed')->sum('amount');
                return "You have paid a total of **KES " . number_format($total) . "** so far.";
            }

            if ($this->matches($message, ['my deposit', 'deposit amount', 'how much deposit'])) {
                if (!$lease) return "You do not have an active lease.";
                return "Your security deposit is **KES " . number_format($lease->deposit_paid) . "**.";
            }

            if ($this->matches($message, ['my invoice', 'my bill', 'unpaid invoice', 'pending invoice'])) {
                $invoices = Invoice::where('tenant_id', $tenant->id)
                    ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->get();
                if ($invoices->isEmpty()) return "You have no pending invoices. You are all clear!";
                $list = $invoices->map(fn($i) => "• {$i->invoice_number} — KES " . number_format($i->balance) . " (due " . $i->due_date->format('d M Y') . ")")->join("\n");
                return "Your pending invoice(s):\n\n{$list}";
            }

            if ($this->matches($message, ['my maintenance', 'my repair request', 'maintenance i submitted'])) {
                $requests = MaintenanceRequest::where('tenant_id', $tenant->id)->latest()->take(5)->get();
                if ($requests->isEmpty()) return "You have not submitted any maintenance requests.";
                $list = $requests->map(fn($r) => "• {$r->title} — " . ucfirst($r->status))->join("\n");
                return "Your maintenance request(s):\n\n{$list}";
            }

            if ($this->matches($message, ['notice period', 'how much notice', 'vacate notice'])) {
                if (!$lease) return "You do not have an active lease.";
                return "Your lease requires **{$lease->notice_days} days** notice before vacating.";
            }

            if ($this->matches($message, ['landlord', 'contact landlord', 'property owner'])) {
                if (!$lease) return "You do not have an active lease.";
                $owner = $lease->unit->property->owner;
                return "Your property is managed by **{$owner->name}**. Contact: {$owner->phone} / {$owner->email}";
            }
        }

        // ── FALLBACK ───────────────────────────────────────
        return "I did not understand that. Type **help** to see what I can assist you with.";
    }

    private function matches(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains(' ' . $message . ' ', ' ' . $keyword . ' ') ||
                str_starts_with($message, $keyword) ||
                str_ends_with($message, $keyword) ||
                $message === $keyword) {
                return true;
            }
        }
        return false;
    }

    private function getHelpMessage(string $role): string
    {
        $messages = [
            'admin' => "Here is what you can ask me:\n\n**Properties**\n• Total properties\n• Property types\n\n**Units**\n• Vacant units\n• Occupied units\n• Total units\n• Occupancy rate\n• Units under maintenance\n\n**Tenants**\n• Total tenants\n• New tenants this month\n• Tenants without lease\n\n**Leases**\n• Active leases\n• Expiring leases\n• Terminated leases\n\n**Finance**\n• This month revenue\n• Last month revenue\n• Total revenue\n• Today payments\n• Unpaid invoices\n• Overdue invoices\n• Outstanding balance\n• M-Pesa payments\n• Cash payments\n\n**Maintenance**\n• Maintenance summary\n• Open maintenance\n• Urgent maintenance\n• Plumbing issues\n• Electrical issues\n• Maintenance cost\n\n**Water**\n• Water readings this month\n• Total water readings",

            'agent' => "Here is what you can ask me:\n\n**Properties**\n• Total properties\n• Property types\n\n**Units**\n• Vacant units\n• Occupied units\n• Total units\n• Occupancy rate\n\n**Tenants**\n• Total tenants\n• New tenants this month\n• Tenants without lease\n\n**Leases**\n• Active leases\n• Expiring leases\n• Terminated leases",

            'accountant' => "Here is what you can ask me:\n\n**Revenue**\n• This month revenue\n• Last month revenue\n• Total revenue\n• Today payments\n• This week payments\n\n**Payments**\n• Total payments\n• M-Pesa payments\n• Cash payments\n• Bank payments\n\n**Invoices**\n• Total invoices\n• Unpaid invoices\n• Overdue invoices\n• Paid invoices\n• Partial payments\n• Outstanding balance",

            'caretaker' => "Here is what you can ask me:\n\n**Maintenance**\n• Maintenance summary\n• Open maintenance\n• Urgent maintenance\n• High priority maintenance\n• Plumbing issues\n• Electrical issues\n• Maintenance this month\n• Resolved maintenance\n• Maintenance cost\n\n**Water**\n• Water readings this month\n• Total water readings\n• Units with water meters\n\n**Units**\n• Units under maintenance\n• Total units",

            'tenant' => "Here is what you can ask me:\n\n**Rent**\n• My rent amount\n• When is rent due\n• My balance\n• Total I have paid\n\n**Lease**\n• My lease details\n• When does my lease expire\n• My notice period\n• My deposit\n\n**Unit & Property**\n• My unit\n• My property address\n• Landlord contact\n\n**Payments & Invoices**\n• My payment history\n• My pending invoices\n\n**Maintenance**\n• My maintenance requests",
        ];

        return $messages[$role] ?? "Ask me anything about the system.";
    }
}