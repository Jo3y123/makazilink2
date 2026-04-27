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
        // ── Conversational ─────────────────────────────────
        if ($this->matches($message, ['who are you', 'what are you', 'what can you do', 'about you', 'your name'])) {
            return "I am the MakaziLink Assistant 🤖 — your built-in property management helper.\n\nI can answer questions about properties, units, tenants, leases, payments, invoices, maintenance, water readings and more.\n\nType **help** to browse topics or just ask me anything!";
        }
 
        if ($this->matches($message, ['hello', 'hey', 'good morning', 'good afternoon', 'good evening', 'habari', 'mambo', 'sasa']) || $message === 'hi') {
            $greetings = [
                "Hello {$user->name}! 👋 How can I help you today?",
                "Hey {$user->name}! What would you like to know?",
                "Hi there {$user->name}! I am ready to help. Type **help** to see what I can do.",
            ];
            return $greetings[array_rand($greetings)];
        }
 
        if ($this->matches($message, ['thank you', 'thanks', 'asante', 'thank u', 'thx', 'great', 'awesome', 'good job', 'well done', 'nice'])) {
            return "You are welcome! 😊 Is there anything else I can help you with?";
        }
 
        if ($this->matches($message, ['bye', 'goodbye', 'see you', 'later', 'kwaheri'])) {
            return "Goodbye {$user->name}! Have a great day! 👋";
        }
 
        if ($this->matches($message, ['what time', 'current time', 'time now'])) {
            return "The current time is **" . now()->format('h:i A') . "**.";
        }
 
        if ($this->matches($message, ['what date', 'today date', 'today is', 'current date', 'what day'])) {
            return "Today is **" . now()->format('l, d F Y') . "**.";
        }
 
        if ($this->matches($message, ['help', 'topics', 'menu', 'options', 'what can', 'commands'])) {
            return $this->getHelpMessage($role);
        }
 
        // ── How-to Guides ───────────────────────────────────
        if ($this->matches($message, ['add a tenant', 'add tenant', 'register tenant', 'how to add tenant', 'how do i add tenant', 'how do i register tenant', 'create tenant'])) {
            return "To add a tenant:\n\n1. Click **Tenants** in the sidebar\n2. Click **Add Tenant**\n3. Fill in name, email, phone and ID number\n4. Click **Register Tenant**\n\nA login account is automatically created for the tenant with the default password **password**.";
        }
 
        if ($this->matches($message, ['create invoice', 'create an invoice', 'generate invoice', 'how to create invoice', 'how do i create invoice', 'new invoice', 'make invoice'])) {
            return "To create an invoice:\n\n1. Click **Invoices** in the sidebar\n2. Click **New Invoice**\n3. Select the tenant's lease\n4. Set the billing period and due date\n5. The rent amount fills automatically\n6. Add water, garbage or other charges if needed\n7. Click **Create Invoice**";
        }
 
        if ($this->matches($message, ['record payment', 'record a payment', 'add payment', 'how to record payment', 'how do i record payment', 'new payment'])) {
            return "To record a payment:\n\n1. Click **Payments** in the sidebar\n2. Click **Record Payment**\n3. Select the invoice (optional)\n4. Select the tenant\n5. Enter the amount and payment method\n6. For M-Pesa, enter the transaction ID\n7. Click **Record Payment**\n\nA receipt is automatically generated.";
        }
 
        if ($this->matches($message, ['add property', 'add a property', 'create property', 'how to add property', 'how do i add property', 'new property'])) {
            return "To add a property:\n\n1. Click **Properties** in the sidebar\n2. Click **Add Property**\n3. Enter the property name, type, address, town and county\n4. Upload a photo (optional)\n5. Click **Add Property**\n\nAfter adding the property you can add units to it.";
        }
 
        if ($this->matches($message, ['add unit', 'add a unit', 'create unit', 'how to add unit', 'how do i add unit', 'new unit'])) {
            return "To add a unit:\n\n1. Click **Units** in the sidebar\n2. Click **Add Unit**\n3. Select the property\n4. Enter the unit number, type, rent and deposit\n5. Tick **Has water meter** if applicable\n6. Upload a photo (optional)\n7. Click **Add Unit**";
        }
 
        if ($this->matches($message, ['create lease', 'create a lease', 'add lease', 'how to create lease', 'how do i create lease', 'new lease'])) {
            return "To create a lease:\n\n1. Click **Leases** in the sidebar\n2. Click **New Lease**\n3. Select the tenant and vacant unit\n4. Set the start date, monthly rent and deposit\n5. Set the end date (or leave blank for open ended)\n6. Click **Create Lease**\n\nThe unit status automatically changes to Occupied.";
        }
 
        if ($this->matches($message, ['log maintenance', 'add maintenance', 'submit maintenance', 'how to log maintenance', 'how do i log maintenance', 'new maintenance', 'log a repair', 'log maintenance request'])) {
            return "To log a maintenance request:\n\n1. Click **Maintenance** in the sidebar\n2. Click **New Request**\n3. Select the unit and optionally the tenant\n4. Enter the title and description\n5. Select category and priority\n6. Upload photos if available\n7. Click **Submit Request**";
        }
 
        if ($this->matches($message, ['record water', 'add water reading', 'record meter reading', 'how to record water', 'how do i record water', 'new water reading'])) {
            return "To record a water reading:\n\n1. Click **Water Readings** in the sidebar\n2. Click **Record Reading**\n3. Select the unit with a water meter\n4. Enter the previous and current readings\n5. Enter the rate per unit (KES)\n6. The amount is calculated automatically\n7. Click **Record Reading**";
        }
 
        if ($this->matches($message, ['send receipt', 'whatsapp receipt', 'send via whatsapp', 'how to send receipt', 'how do i send receipt', 'send whatsapp', 'receipt via whatsapp', 'via whatsapp'])) {
            return "To send a receipt via WhatsApp:\n\n1. Click **Payments** in the sidebar\n2. Click the eye icon on a payment\n3. Click the green **Send via WhatsApp** button\n4. WhatsApp will open with the message pre-filled\n5. Press Send in WhatsApp\n\nNote: The tenant's phone number must be saved correctly.";
        }
 
        if ($this->matches($message, ['download pdf', 'download invoice', 'print invoice', 'how to download', 'how do i download', 'pdf invoice', 'pdf receipt'])) {
            return "To download a PDF:\n\n• For invoices: Go to **Invoices** → click the eye icon → click **Download PDF**\n• For receipts: Go to **Payments** → click the eye icon → click **Download PDF**\n• For tenant statement: Go to **Tenants** → click the document icon next to a tenant\n\nYou can also click **Print Receipt** to print directly from the browser.";
        }
 
        if ($this->matches($message, ['deactivate user', 'disable user', 'block user', 'how to deactivate', 'how do i deactivate'])) {
            return "To deactivate a user:\n\n1. Click **Users** in the sidebar\n2. Click the edit icon on the user\n3. Uncheck **Account is active**\n4. Click **Save Changes**\n\nThe user will not be able to log in until reactivated.";
        }
 
        if ($this->matches($message, ['bulk invoice', 'generate all invoices', 'bulk generate', 'how to bulk', 'how do i bulk'])) {
            return "To bulk generate invoices:\n\n1. Click **Invoices** in the sidebar\n2. Click **Bulk Generate**\n3. Set the billing period start and end dates\n4. Set the due date\n5. Click **Generate All Invoices**\n\nThe system will create invoices for all active leases and skip tenants who already have an invoice for that period.";
        }
 
        if ($this->matches($message, ['send reminders', 'bulk whatsapp', 'whatsapp reminders', 'how to send reminders'])) {
            return "To send bulk WhatsApp reminders:\n\n1. Click **Payments** in the sidebar\n2. Click the green **Send Reminders** button\n3. You will see all tenants with unpaid invoices\n4. Click **Send Reminder** next to each tenant\n5. WhatsApp opens with the reminder pre-filled\n6. Press Send in WhatsApp";
        }
 
        if ($this->matches($message, ['change settings', 'update settings', 'how to change settings', 'system settings', 'how do i change'])) {
            return "To update system settings:\n\n1. Click **Settings** in the sidebar\n2. Update the fields you want to change:\n   • System/Company name\n   • Logo upload\n   • Currency and timezone\n   • Rent due day\n   • Invoice prefix\n   • M-Pesa credentials\n3. Click **Save Settings**\n\nChanges take effect immediately across the whole system.";
        }
 
        if ($this->matches($message, ['add user', 'create user', 'new user', 'how to add user', 'how do i add user'])) {
            return "To add a new user:\n\n1. Click **Users** in the sidebar\n2. Click **Add User**\n3. Enter name, email, phone and role\n4. Set a password\n5. Click **Save**\n\nAvailable roles: Admin, Agent, Accountant, Caretaker, Tenant.";
        }
 
        if ($this->matches($message, ['upload logo', 'change logo', 'add logo', 'how to upload logo', 'how do i upload logo'])) {
            return "To upload a logo:\n\n1. Click **Settings** in the sidebar\n2. Scroll to the **General** section\n3. Click **Choose File** under System Logo\n4. Select your logo image (PNG or SVG recommended)\n5. Click **Save Settings**\n\nThe logo will appear in the sidebar across the whole system.";
        }
 
        // ── Daily / Weekly Summary ──────────────────────────
        if ($this->matches($message, ['daily summary', 'today summary', 'summary today', 'show me everything today'])) {
            $todayPayments = Payment::whereDate('payment_date', today())->where('status', 'confirmed')->sum('amount');
            $todayCount    = Payment::whereDate('payment_date', today())->where('status', 'confirmed')->count();
            $openMaint     = MaintenanceRequest::where('status', 'open')->count();
            $vacant        = Unit::where('status', 'vacant')->count();
            return "📊 Today's Summary — " . now()->format('d M Y') . "\n\n• Payments received: **{$todayCount}** (KES " . number_format($todayPayments) . ")\n• Open maintenance: **{$openMaint}**\n• Vacant units: **{$vacant}**";
        }
 
        if ($this->matches($message, ['weekly summary', 'this week summary', 'week summary'])) {
            $weekPayments = Payment::whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'confirmed')->sum('amount');
            $weekCount    = Payment::whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'confirmed')->count();
            $newTenants   = Tenant::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
            return "📊 This Week's Summary\n\n• Payments received: **{$weekCount}** (KES " . number_format($weekPayments) . ")\n• New tenants: **{$newTenants}**\n• Week: " . now()->startOfWeek()->format('d M') . " — " . now()->endOfWeek()->format('d M Y');
        }
 
        if ($this->matches($message, ['monthly summary', 'this month summary', 'month summary', 'show me everything'])) {
            $revenue  = Payment::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->where('status', 'confirmed')->sum('amount');
            $occupied = Unit::where('status', 'occupied')->count();
            $vacant   = Unit::where('status', 'vacant')->count();
            $unpaid   = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count();
            $maint    = MaintenanceRequest::whereIn('status', ['open', 'in_progress'])->count();
            return "📊 Monthly Summary — " . now()->format('F Y') . "\n\n• Revenue: **KES " . number_format($revenue) . "**\n• Occupied units: **{$occupied}**\n• Vacant units: **{$vacant}**\n• Unpaid invoices: **{$unpaid}**\n• Open maintenance: **{$maint}**";
        }
 
        // ── ADMIN / AGENT ──────────────────────────────────
        if (in_array($role, ['admin', 'agent'])) {
 
            if ($this->matches($message, ['total propert', 'how many propert', 'number of propert', 'list propert', 'all propert', 'propert count'])) {
                $count = Property::count();
                $props = Property::all()->map(fn($p) => "• {$p->name} — {$p->town}")->join("\n");
                return "There are **{$count}** properties:\n\n{$props}";
            }
 
            if ($this->matches($message, ['property type', 'type of propert', 'propert by type'])) {
                $types = Property::selectRaw('type, count(*) as count')->groupBy('type')->get();
                $list  = $types->map(fn($t) => "• " . ucfirst(str_replace('_', ' ', $t->type)) . ": {$t->count}")->join("\n");
                return "Properties by type:\n\n{$list}";
            }
 
            if ($this->matches($message, ['vacant', 'empty unit', 'available unit', 'free unit', 'unoccupied', 'not occupied'])) {
                $units = Unit::with('property')->where('status', 'vacant')->get();
                if ($units->isEmpty()) return "There are no vacant units currently. All units are occupied! 🎉";
                $list = $units->map(fn($u) => "• {$u->unit_number} — {$u->property->name} (KES " . number_format($u->rent_amount) . ")")->join("\n");
                return "**{$units->count()}** vacant unit(s):\n\n{$list}";
            }
 
            if ($this->matches($message, ['occupied unit', 'rented unit', 'taken unit', 'filled unit', 'tenanted'])) {
                $count = Unit::where('status', 'occupied')->count();
                return "There are currently **{$count}** occupied units.";
            }
 
            if ($this->matches($message, ['total unit', 'how many unit', 'number of unit', 'all unit', 'unit count', 'unit summary'])) {
                $total    = Unit::count();
                $occupied = Unit::where('status', 'occupied')->count();
                $vacant   = Unit::where('status', 'vacant')->count();
                $maint    = Unit::where('status', 'under_maintenance')->count();
                return "Unit summary:\n\n• Total: **{$total}**\n• Occupied: **{$occupied}**\n• Vacant: **{$vacant}**\n• Under Maintenance: **{$maint}**";
            }
 
            if ($this->matches($message, ['occupancy rate', 'occupancy percent', 'how full', 'percentage occupied'])) {
                $total    = Unit::count();
                $occupied = Unit::where('status', 'occupied')->count();
                $rate     = $total > 0 ? round(($occupied / $total) * 100) : 0;
                return "Current occupancy rate is **{$rate}%** ({$occupied} out of {$total} units occupied).";
            }
 
            if ($this->matches($message, ['under maintenance unit', 'unit under maintenance', 'maintenance unit', 'unit being repaired'])) {
                $units = Unit::with('property')->where('status', 'under_maintenance')->get();
                if ($units->isEmpty()) return "No units are currently under maintenance. ✅";
                $list = $units->map(fn($u) => "• {$u->unit_number} — {$u->property->name}")->join("\n");
                return "**{$units->count()}** unit(s) under maintenance:\n\n{$list}";
            }
 
            if ($this->matches($message, ['unit with water meter', 'water meter unit', 'metered unit'])) {
                $count = Unit::where('has_water_meter', true)->count();
                return "**{$count}** unit(s) have water meters installed.";
            }
 
            if ($this->matches($message, ['total tenant', 'how many tenant', 'number of tenant', 'all tenant', 'tenant count'])) {
                $count = Tenant::count();
                return "There are currently **{$count}** registered tenants.";
            }
 
            if ($this->matches($message, ['new tenant', 'tenant this month', 'tenant added this month', 'recent tenant'])) {
                $count = Tenant::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                return "**{$count}** new tenant(s) were registered this month.";
            }
 
            if ($this->matches($message, ['tenant without lease', 'tenant no lease', 'unassigned tenant', 'tenant not assigned'])) {
                $count = Tenant::doesntHave('leases')->count();
                return "**{$count}** tenant(s) do not have any lease assigned.";
            }
 
            if ($this->matches($message, ['active lease', 'current lease', 'how many lease', 'lease count'])) {
                $count = Lease::where('status', 'active')->count();
                return "There are **{$count}** active leases.";
            }
 
            if ($this->matches($message, ['expiring lease', 'lease expir', 'lease ending', 'lease expire', 'lease about to end'])) {
                $leases = Lease::with('tenant.user', 'unit')
                    ->where('status', 'active')
                    ->whereNotNull('end_date')
                    ->whereDate('end_date', '<=', now()->addDays(30))
                    ->get();
                if ($leases->isEmpty()) return "No leases are expiring within the next 30 days. ✅";
                $list = $leases->map(fn($l) => "• {$l->tenant->user->name} — Unit {$l->unit->unit_number} (expires {$l->end_date->format('d M Y')})")->join("\n");
                return "**{$leases->count()}** lease(s) expiring within 30 days:\n\n{$list}";
            }
 
            if ($this->matches($message, ['terminated lease', 'cancelled lease', 'ended lease'])) {
                $count = Lease::where('status', 'terminated')->count();
                return "There are **{$count}** terminated leases on record.";
            }
 
            if ($this->matches($message, ['open ended lease', 'no end date', 'permanent lease', 'lease no expiry'])) {
                $count = Lease::where('status', 'active')->whereNull('end_date')->count();
                return "**{$count}** active lease(s) have no end date (open ended).";
            }
        }
 
        // ── ADMIN / ACCOUNTANT ─────────────────────────────
        if (in_array($role, ['admin', 'accountant'])) {
 
            if ($this->matches($message, ['this month revenue', 'revenue this month', 'current month revenue', 'how much this month', 'collected this month'])) {
                $amount = Payment::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->where('status', 'confirmed')->sum('amount');
                return "This month's revenue is **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['last month revenue', 'previous month revenue', 'how much last month'])) {
                $amount = Payment::whereMonth('payment_date', now()->subMonth()->month)->whereYear('payment_date', now()->subMonth()->year)->where('status', 'confirmed')->sum('amount');
                return "Last month's revenue was **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['total revenue', 'all time revenue', 'total income', 'total collected', 'overall revenue'])) {
                $amount = Payment::where('status', 'confirmed')->sum('amount');
                return "Total all-time revenue collected is **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['today payment', 'payment today', 'how much today', 'collected today', 'revenue today'])) {
                $amount = Payment::whereDate('payment_date', today())->where('status', 'confirmed')->sum('amount');
                $count  = Payment::whereDate('payment_date', today())->where('status', 'confirmed')->count();
                return "Today's payments: **{$count}** transaction(s) totalling **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['this week payment', 'payment this week', 'weekly payment', 'week revenue'])) {
                $amount = Payment::whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'confirmed')->sum('amount');
                return "This week's payments total **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['mpesa', 'm-pesa', 'mpesa payment', 'paid via mpesa', 'mobile money'])) {
                $amount = Payment::where('payment_method', 'mpesa')->where('status', 'confirmed')->sum('amount');
                $count  = Payment::where('payment_method', 'mpesa')->where('status', 'confirmed')->count();
                return "M-Pesa payments: **{$count}** transactions totalling **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['cash payment', 'paid cash', 'cash total', 'paid in cash'])) {
                $amount = Payment::where('payment_method', 'cash')->where('status', 'confirmed')->sum('amount');
                $count  = Payment::where('payment_method', 'cash')->where('status', 'confirmed')->count();
                return "Cash payments: **{$count}** transactions totalling **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['bank payment', 'bank transfer', 'paid via bank', 'bank deposit'])) {
                $amount = Payment::where('payment_method', 'bank_transfer')->where('status', 'confirmed')->sum('amount');
                $count  = Payment::where('payment_method', 'bank_transfer')->where('status', 'confirmed')->count();
                return "Bank transfer payments: **{$count}** transactions totalling **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['total payment', 'how many payment', 'number of payment', 'all payment', 'payment count'])) {
                $count  = Payment::where('status', 'confirmed')->count();
                $amount = Payment::where('status', 'confirmed')->sum('amount');
                return "There are **{$count}** confirmed payments totalling **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['unpaid invoice', 'outstanding invoice', 'pending invoice', 'not paid invoice', 'who hasnt paid'])) {
                $count  = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count();
                $amount = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance');
                return "**{$count}** unpaid invoice(s) with total outstanding balance of **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['overdue invoice', 'late invoice', 'overdue payment', 'late payment'])) {
                $count  = Invoice::where('status', 'overdue')->count();
                $amount = Invoice::where('status', 'overdue')->sum('balance');
                return "**{$count}** overdue invoice(s) with total balance of **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['paid invoice', 'cleared invoice', 'fully paid', 'invoice paid'])) {
                $count = Invoice::where('status', 'paid')->count();
                return "**{$count}** invoice(s) have been fully paid.";
            }
 
            if ($this->matches($message, ['partial payment', 'partial invoice', 'half paid', 'partly paid'])) {
                $count  = Invoice::where('status', 'partial')->count();
                $amount = Invoice::where('status', 'partial')->sum('balance');
                return "**{$count}** invoice(s) are partially paid with remaining balance of **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['total invoice', 'how many invoice', 'number of invoice', 'all invoice', 'invoice count', 'invoice summary'])) {
                $total  = Invoice::count();
                $paid   = Invoice::where('status', 'paid')->count();
                $unpaid = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count();
                return "Invoice summary:\n\n• Total: **{$total}**\n• Paid: **{$paid}**\n• Unpaid: **{$unpaid}**";
            }
 
            if ($this->matches($message, ['outstanding balance', 'total outstanding', 'total owed', 'total debt', 'total arrears', 'how much is owed'])) {
                $amount = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance');
                return "Total outstanding balance across all tenants is **KES " . number_format($amount) . "**.";
            }
        }
 
        // ── ADMIN / CARETAKER ──────────────────────────────
        if (in_array($role, ['admin', 'caretaker'])) {
 
            if ($this->matches($message, ['total maintenance', 'all maintenance', 'maintenance summary', 'maintenance request', 'maintenance status'])) {
                $open       = MaintenanceRequest::where('status', 'open')->count();
                $inProgress = MaintenanceRequest::where('status', 'in_progress')->count();
                $resolved   = MaintenanceRequest::where('status', 'resolved')->count();
                $closed     = MaintenanceRequest::where('status', 'closed')->count();
                return "Maintenance summary:\n\n• Open: **{$open}**\n• In Progress: **{$inProgress}**\n• Resolved: **{$resolved}**\n• Closed: **{$closed}**";
            }
 
            if ($this->matches($message, ['open maintenance', 'pending maintenance', 'unresolved maintenance', 'pending repair'])) {
                $requests = MaintenanceRequest::with('unit.property')->where('status', 'open')->get();
                if ($requests->isEmpty()) return "No open maintenance requests. Everything is running smoothly! ✅";
                $list = $requests->map(fn($r) => "• {$r->title} — Unit {$r->unit->unit_number}, {$r->unit->property->name}")->join("\n");
                return "**{$requests->count()}** open maintenance request(s):\n\n{$list}";
            }
 
            if ($this->matches($message, ['urgent maintenance', 'emergency maintenance', 'urgent repair'])) {
                $requests = MaintenanceRequest::with('unit')->where('priority', 'urgent')->whereIn('status', ['open', 'in_progress'])->get();
                if ($requests->isEmpty()) return "No urgent maintenance requests currently. ✅";
                $list = $requests->map(fn($r) => "• {$r->title} — Unit {$r->unit->unit_number}")->join("\n");
                return "**{$requests->count()}** urgent maintenance request(s):\n\n{$list}";
            }
 
            if ($this->matches($message, ['high priority maintenance', 'high priority repair', 'high maintenance'])) {
                $count = MaintenanceRequest::where('priority', 'high')->whereIn('status', ['open', 'in_progress'])->count();
                return "There are **{$count}** high priority maintenance requests.";
            }
 
            if ($this->matches($message, ['plumbing', 'plumbing issue', 'plumbing request', 'pipe', 'water leak'])) {
                $count = MaintenanceRequest::where('category', 'plumbing')->whereIn('status', ['open', 'in_progress'])->count();
                return "There are **{$count}** open plumbing requests.";
            }
 
            if ($this->matches($message, ['electrical', 'electrical issue', 'electrical request', 'power issue'])) {
                $count = MaintenanceRequest::where('category', 'electrical')->whereIn('status', ['open', 'in_progress'])->count();
                return "There are **{$count}** open electrical requests.";
            }
 
            if ($this->matches($message, ['maintenance this month', 'repair this month', 'maintenance in month'])) {
                $count = MaintenanceRequest::whereMonth('created_at', now()->month)->count();
                return "**{$count}** maintenance request(s) were submitted this month.";
            }
 
            if ($this->matches($message, ['resolved maintenance', 'fixed maintenance', 'completed maintenance', 'repaired'])) {
                $count = MaintenanceRequest::where('status', 'resolved')->count();
                return "**{$count}** maintenance request(s) have been resolved.";
            }
 
            if ($this->matches($message, ['maintenance cost', 'repair cost', 'total repair cost'])) {
                $amount = MaintenanceRequest::whereNotNull('cost')->sum('cost');
                return "Total maintenance costs recorded: **KES " . number_format($amount) . "**.";
            }
 
            if ($this->matches($message, ['water reading', 'meter reading', 'water this month', 'water readings this month'])) {
                $count = WaterReading::whereMonth('reading_date', now()->month)->count();
                $total = WaterReading::whereMonth('reading_date', now()->month)->sum('amount_charged');
                return "**{$count}** water reading(s) recorded this month, total charged: **KES " . number_format($total) . "**.";
            }
 
            if ($this->matches($message, ['total water', 'all water reading', 'water reading total', 'total water readings'])) {
                $count = WaterReading::count();
                $total = WaterReading::sum('amount_charged');
                return "Total water readings: **{$count}**, total amount charged: **KES " . number_format($total) . "**.";
            }
 
            if ($this->matches($message, ['unit with meter', 'metered unit', 'water meter', 'units with water meter'])) {
                $count = Unit::where('has_water_meter', true)->count();
                return "**{$count}** unit(s) have water meters installed.";
            }
        }
 
        // ── TENANT ─────────────────────────────────────────
        if ($role === 'tenant') {
            $tenant = $user->tenant;
 
            if (!$tenant) {
                return "Your tenant profile is not set up yet. Please contact your landlord or caretaker to get set up in the system.";
            }
 
            $lease = $tenant->activeLease;
 
            if ($this->matches($message, ['my rent', 'rent amount', 'how much is my rent', 'monthly rent', 'how much do i pay'])) {
                if (!$lease) return "You do not have an active lease. Contact your landlord.";
                return "Your monthly rent is **KES " . number_format($lease->monthly_rent) . "**.";
            }
 
            if ($this->matches($message, ['due date', 'when is rent due', 'next payment', 'next due', 'when should i pay', 'when do i pay', 'rent due date'])) {
                if (!$lease) return "You do not have an active lease. Contact your landlord.";
                $due = $lease->next_due_date ? $lease->next_due_date->format('d M Y') : 'Not set';
                return "Your next rent due date is **{$due}**.";
            }
 
            if ($this->matches($message, ['my balance', 'how much do i owe', 'outstanding balance', 'what do i owe', 'amount owed', 'arrears'])) {
                $balance = Invoice::where('tenant_id', $tenant->id)->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance');
                if ($balance == 0) return "You have no outstanding balance. You are all clear! ✅";
                return "Your current outstanding balance is **KES " . number_format($balance) . "**.";
            }
 
            if ($this->matches($message, ['am i up to date', 'am i clear', 'have i paid everything', 'all paid', 'no balance'])) {
                $balance = Invoice::where('tenant_id', $tenant->id)->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance');
                if ($balance == 0) return "Yes, you are fully up to date! No outstanding balance. ✅";
                return "You have an outstanding balance of **KES " . number_format($balance) . "**. Please make a payment to clear it.";
            }
 
            if ($this->matches($message, ['paid this month', 'have i paid this month', 'did i pay this month', 'payment this month'])) {
                $payment = Payment::where('tenant_id', $tenant->id)->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->where('status', 'confirmed')->first();
                if (!$payment) return "No payment has been recorded for you this month (" . now()->format('F Y') . ").";
                return "Yes! You paid **KES " . number_format($payment->amount) . "** on " . $payment->payment_date->format('d M Y') . " via " . ucfirst(str_replace('_', ' ', $payment->payment_method)) . ". Receipt: {$payment->receipt_number}";
            }
 
            if ($this->matches($message, ['my lease', 'my contract', 'lease detail', 'my agreement', 'lease info'])) {
                if (!$lease) return "You do not have an active lease. Contact your landlord.";
                $end = $lease->end_date ? $lease->end_date->format('d M Y') : 'Open ended';
                return "Your lease details:\n\n• Start: **" . $lease->start_date->format('d M Y') . "**\n• End: **{$end}**\n• Rent: **KES " . number_format($lease->monthly_rent) . "**\n• Status: **" . ucfirst($lease->status) . "**";
            }
 
            if ($this->matches($message, ['when does my lease expire', 'lease expiry', 'when does lease end', 'lease end date'])) {
                if (!$lease) return "You do not have an active lease.";
                if (!$lease->end_date) return "Your lease is open ended with no expiry date.";
                return "Your lease expires on **" . $lease->end_date->format('d M Y') . "** (" . $lease->days_until_expiry . " days remaining).";
            }
 
            if ($this->matches($message, ['my unit', 'my room', 'my apartment', 'which unit am i in', 'my flat', 'unit number'])) {
                if (!$lease) return "You are not assigned to a unit yet.";
                return "You are in unit **" . $lease->unit->unit_number . "** at **" . $lease->unit->property->name . "**, " . $lease->unit->property->address . ".";
            }
 
            if ($this->matches($message, ['my property', 'where do i live', 'my building', 'my address', 'property address'])) {
                if (!$lease) return "You are not assigned to a property yet.";
                return "You live at **" . $lease->unit->property->name . "**, " . $lease->unit->property->address . ", " . $lease->unit->property->town . ".";
            }
 
            if ($this->matches($message, ['my payment history', 'past payment', 'my payment', 'payments i made', 'payment record'])) {
                $payments = Payment::where('tenant_id', $tenant->id)->where('status', 'confirmed')->latest()->take(5)->get();
                if ($payments->isEmpty()) return "No payment history found yet.";
                $list = $payments->map(fn($p) => "• {$p->receipt_number} — KES " . number_format($p->amount) . " on " . $p->payment_date->format('d M Y') . " via " . ucfirst(str_replace('_', ' ', $p->payment_method)))->join("\n");
                return "Your last {$payments->count()} payment(s):\n\n{$list}";
            }
 
            if ($this->matches($message, ['total paid', 'how much have i paid', 'total payment made', 'total i have paid', 'all i have paid'])) {
                $total = Payment::where('tenant_id', $tenant->id)->where('status', 'confirmed')->sum('amount');
                return "You have paid a total of **KES " . number_format($total) . "** so far.";
            }
 
            if ($this->matches($message, ['my deposit', 'deposit amount', 'how much deposit', 'security deposit'])) {
                if (!$lease) return "You do not have an active lease.";
                return "Your security deposit is **KES " . number_format($lease->deposit_paid) . "**.";
            }
 
            if ($this->matches($message, ['my invoice', 'my bill', 'unpaid invoice', 'pending invoice', 'pending bill', 'outstanding bill'])) {
                $invoices = Invoice::where('tenant_id', $tenant->id)->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->get();
                if ($invoices->isEmpty()) return "You have no pending invoices. You are all clear! ✅";
                $list = $invoices->map(fn($i) => "• {$i->invoice_number} — KES " . number_format($i->balance) . " (due " . $i->due_date->format('d M Y') . ")")->join("\n");
                return "Your pending invoice(s):\n\n{$list}";
            }
 
            if ($this->matches($message, ['my maintenance', 'my repair request', 'maintenance i submitted', 'my repair'])) {
                $requests = MaintenanceRequest::where('tenant_id', $tenant->id)->latest()->take(5)->get();
                if ($requests->isEmpty()) return "You have not submitted any maintenance requests yet.";
                $list = $requests->map(fn($r) => "• {$r->title} — " . ucfirst($r->status))->join("\n");
                return "Your maintenance request(s):\n\n{$list}";
            }
 
            if ($this->matches($message, ['notice period', 'how much notice', 'vacate notice', 'notice to vacate'])) {
                if (!$lease) return "You do not have an active lease.";
                return "Your lease requires **{$lease->notice_days} days** notice before vacating.";
            }
 
            if ($this->matches($message, ['landlord', 'contact landlord', 'property owner', 'who is my landlord', 'landlord contact'])) {
                if (!$lease) return "You do not have an active lease.";
                $owner = $lease->unit->property->owner;
                return "Your property is managed by **{$owner->name}**.\n\nContact:\n• Phone: {$owner->phone}\n• Email: {$owner->email}";
            }
        }
 
        // ── FALLBACK ───────────────────────────────────────
        return "I did not understand that. Type **help** to browse topics or try rephrasing your question.";
    }
 
    private function matches(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        return false;
    }
 
    private function getHelpMessage(string $role): string
    {
        $messages = [
            'admin' => "Here is what you can ask me:\n\n**Properties & Units**\n• Total properties / property types\n• Vacant units / occupied units / total units\n• Occupancy rate / units under maintenance\n\n**Tenants & Leases**\n• Total tenants / new tenants this month\n• Active leases / expiring leases\n• Tenants without lease\n\n**Finance**\n• This month revenue / last month revenue\n• Total revenue / today payments\n• Unpaid invoices / overdue invoices\n• Outstanding balance / M-Pesa payments\n\n**Maintenance**\n• Maintenance summary / open maintenance\n• Urgent maintenance / plumbing / electrical\n• Maintenance cost\n\n**Water**\n• Water readings this month\n• Units with water meter\n\n**Summaries**\n• Daily summary / weekly summary / monthly summary\n\n**How-to Guides**\n• How do I add a tenant?\n• How do I create an invoice?\n• How do I record a payment?\n• How do I add a property?\n• How do I add a unit?\n• How do I create a lease?\n• How do I log a maintenance request?\n• How do I record a water reading?\n• How do I send a receipt via WhatsApp?\n• How do I download a PDF?\n• How do I deactivate a user?\n• How do I bulk generate invoices?\n• How do I upload a logo?",
 
            'agent' => "Here is what you can ask me:\n\n**Properties & Units**\n• Total properties / property types\n• Vacant units / occupied units / total units\n• Occupancy rate\n\n**Tenants & Leases**\n• Total tenants / new tenants this month\n• Active leases / expiring leases\n\n**How-to Guides**\n• How do I add a tenant?\n• How do I create a lease?\n• How do I add a property?\n• How do I add a unit?",
 
            'accountant' => "Here is what you can ask me:\n\n**Revenue**\n• This month / last month / total revenue\n• Today payments / this week payments\n\n**Payments**\n• Total payments / M-Pesa / cash / bank\n\n**Invoices**\n• Total / unpaid / overdue / paid invoices\n• Outstanding balance / partial payments\n\n**Summaries**\n• Monthly summary / weekly summary\n\n**How-to Guides**\n• How do I create an invoice?\n• How do I record a payment?\n• How do I download a PDF?\n• How do I bulk generate invoices?\n• How do I send reminders?",
 
            'caretaker' => "Here is what you can ask me:\n\n**Maintenance**\n• Maintenance summary / open maintenance\n• Urgent / high priority maintenance\n• Plumbing / electrical\n• Maintenance this month / resolved / cost\n\n**Water**\n• Water readings this month / total\n• Units with water meter\n\n**Units**\n• Units under maintenance / total units\n\n**How-to Guides**\n• How do I log a maintenance request?\n• How do I record a water reading?",
 
            'tenant' => "Here is what you can ask me:\n\n**Rent & Payments**\n• My rent amount / when is rent due\n• My balance / total I have paid\n• Have I paid this month? / Am I up to date?\n• My payment history\n\n**Lease**\n• My lease details / when does it expire\n• My notice period / my deposit\n\n**My Home**\n• My unit / my property address\n• Landlord contact\n\n**Bills**\n• My pending invoices\n\n**Maintenance**\n• My maintenance requests",
        ];
 
        return $messages[$role] ?? "Ask me anything about the system. Type a question below.";
    }
}
 