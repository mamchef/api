<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\Chef\ChefStatusEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\Ticket\TicketStatusEnum;
use App\Enums\User\PaymentMethod;
use App\Enums\User\TransactionStatus;
use App\Enums\User\TransactionType;
use App\Http\Controllers\Controller;

use App\Http\Resources\V1\Admin\StatsResponseResponse;
use App\Models\Chef;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json(new StatsResponseResponse([
            'orders' => $this->getOrderStats(),
            'users' => $this->getUserStats(),
            'chefs' => $this->getChefStats(),
            'registrations' => $this->getRegistrationStats(),
            'tickets' => $this->getTicketStats(),
            'transactions' => $this->getTransactionStats(),
            'revenue' => $this->getRevenueStats(),
            'overview' => $this->getOverviewStats()
        ]));
    }

    public function orders(): JsonResponse
    {
        return response()->json(new StatsResponseResponse($this->getOrderStats()));
    }

    public function users(): JsonResponse
    {
        return response()->json(new StatsResponseResponse($this->getUserStats()));
    }

    public function chefs(): JsonResponse
    {
        return response()->json(new StatsResponseResponse($this->getChefStats()));
    }

    public function registrations(): JsonResponse
    {
        return response()->json(new StatsResponseResponse($this->getRegistrationStats()));
    }

    public function tickets(): JsonResponse
    {
        return response()->json(new StatsResponseResponse($this->getTicketStats()));
    }

    public function transactions(): JsonResponse
    {
        return response()->json(new StatsResponseResponse($this->getTransactionStats()));
    }

    public function revenue(): JsonResponse
    {
        return response()->json(new StatsResponseResponse($this->getRevenueStats()));
    }

    private function getOrderStats(): array
    {
        $totalOrders = Order::count();

        $ordersByStatus = [];
        foreach (OrderStatusEnum::cases() as $status) {
            $ordersByStatus[$status->value] = [
                'label' => $this->getOrderStatusLabel($status),
                'count' => Order::where('status', $status)->count()
            ];
        }

        $activeOrders = Order::whereIn('status', OrderStatusEnum::activeStatuses())->count();
        $completedOrders = Order::where('status', OrderStatusEnum::COMPLETED)->count();
        $cancelledOrders = Order::whereIn('status', OrderStatusEnum::canceledStatuses())->count();

        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $weekOrders = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
        )->count();
        $monthOrders = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return [
            'total' => $totalOrders,
            'by_status' => $ordersByStatus,
            'active' => $activeOrders,
            'completed' => $completedOrders,
            'cancelled' => $cancelledOrders,
            'today' => $todayOrders,
            'this_week' => $weekOrders,
            'this_month' => $monthOrders,
            'average_per_day' => $totalOrders > 0 ? round(
                $totalOrders / max(1, Carbon::now()->diffInDays(Order::min('created_at'))),
                2
            ) : 0
        ];
    }

    private function getUserStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::active()->count();

        $todayUsers = User::whereDate('created_at', Carbon::today())->count();
        $weekUsers = User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
        )->count();
        $monthUsers = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'inactive' => $totalUsers - $activeUsers,
            'today' => $todayUsers,
            'this_week' => $weekUsers,
            'this_month' => $monthUsers,
            'average_per_day' => $totalUsers > 0 ? round(
                $totalUsers / max(1, Carbon::now()->diffInDays(User::min('created_at'))),
                2
            ) : 0
        ];
    }

    private function getChefStats(): array
    {
        $totalChefs = Chef::count();

        $chefsByStatus = [];
        foreach (ChefStatusEnum::cases() as $status) {
            $chefsByStatus[$status->value] = [
                'label' => $this->getChefStatusLabel($status),
                'count' => Chef::where('status', $status)->count()
            ];
        }

        $todayChefs = Chef::whereDate('created_at', Carbon::today())->count();
        $weekChefs = Chef::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
        )->count();
        $monthChefs = Chef::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $approvedChefs = Chef::where('status', ChefStatusEnum::Approved)->count();
        $pendingApprovalChefs = Chef::where('status', ChefStatusEnum::NeedAdminApproval)->count();
        $rejectedChefs = Chef::where('status', ChefStatusEnum::Rejected)->count();

        return [
            'total' => $totalChefs,
            'by_status' => $chefsByStatus,
            'approved' => $approvedChefs,
            'pending_approval' => $pendingApprovalChefs,
            'rejected' => $rejectedChefs,
            'today' => $todayChefs,
            'this_week' => $weekChefs,
            'this_month' => $monthChefs,
            'average_per_day' => $totalChefs > 0 ? round(
                $totalChefs / max(1, Carbon::now()->diffInDays(Chef::min('created_at'))),
                2
            ) : 0
        ];
    }

    private function getRegistrationStats(): array
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $usersToday = User::whereDate('created_at', $today)->count();
        $chefsToday = Chef::whereDate('created_at', $today)->count();

        $usersThisWeek = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $chefsThisWeek = Chef::whereBetween('created_at', [$weekStart, $weekEnd])->count();

        $usersThisMonth = User::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $chefsThisMonth = Chef::whereBetween('created_at', [$monthStart, $monthEnd])->count();

        return [
            'today' => [
                'users' => $usersToday,
                'chefs' => $chefsToday,
                'total' => $usersToday + $chefsToday
            ],
            'this_week' => [
                'users' => $usersThisWeek,
                'chefs' => $chefsThisWeek,
                'total' => $usersThisWeek + $chefsThisWeek
            ],
            'this_month' => [
                'users' => $usersThisMonth,
                'chefs' => $chefsThisMonth,
                'total' => $usersThisMonth + $chefsThisMonth
            ]
        ];
    }

    private function getTicketStats(): array
    {
        $totalTickets = Ticket::count();

        $ticketsByStatus = [];
        foreach (TicketStatusEnum::cases() as $status) {
            $ticketsByStatus[$status->value] = [
                'label' => ucwords(str_replace('_', ' ', $status->value)),
                'count' => Ticket::where('status', $status)->count()
            ];
        }

        $openTickets = Ticket::whereIn('status', [
            TicketStatusEnum::USER_CREATED,
            TicketStatusEnum::UNDER_REVIEW,
            TicketStatusEnum::ADMIN_ANSWERED,
            TicketStatusEnum::USER_ANSWERED
        ])->count();

        $closedTickets = Ticket::whereIn('status', [
            TicketStatusEnum::COMPLETED,
            TicketStatusEnum::CLOSED
        ])->count();

        $todayTickets = Ticket::whereDate('created_at', Carbon::today())->count();

        return [
            'total' => $totalTickets,
            'by_status' => $ticketsByStatus,
            'open' => $openTickets,
            'closed' => $closedTickets,
            'today' => $todayTickets
        ];
    }

    private function getTransactionStats(): array
    {
        $totalTransactions = UserTransaction::count();
        $totalWalletBalance = UserTransaction::where('status', TransactionStatus::COMPLETED)->sum('amount');
        $totalIncome = UserTransaction::where('type', TransactionType::CHARGE_WALLET)
            ->where('status', TransactionStatus::COMPLETED)
            ->whereIn('payment_method', PaymentMethod::incomeEnumValues())
            ->sum('amount');

        $transactionsByStatus = [];
        foreach (TransactionStatus::cases() as $status) {
            $count = UserTransaction::where('status', $status)->count();
            $amount = UserTransaction::where('status', $status)->sum('amount');
            $transactionsByStatus[$status->value] = [
                'label' => $status->label(),
                'count' => $count,
                'amount' => round($amount, 2)
            ];
        }

        $transactionsByType = [];
        foreach (TransactionType::cases() as $type) {
            $count = UserTransaction::where('type', $type)->count();
            $amount = UserTransaction::where('type', $type)->where('status', TransactionStatus::COMPLETED)->sum(
                'amount'
            );
            $transactionsByType[$type->value] = [
                'label' => $type->label(),
                'count' => $count,
                'amount' => round($amount, 2)
            ];
        }

        $transactionsByPaymentMethod = [];
        foreach (PaymentMethod::cases() as $method) {
            $count = UserTransaction::where('payment_method', $method)->count();
            $amount = UserTransaction::where('payment_method', $method)->where(
                'status',
                TransactionStatus::COMPLETED
            )->sum('amount');
            $transactionsByPaymentMethod[$method->value] = [
                'label' => $method->label(),
                'count' => $count,
                'amount' => round($amount, 2)
            ];
        }

        $todayTransactions = UserTransaction::whereDate('created_at', Carbon::today())->count();
        $todayIncome = UserTransaction::whereDate('created_at', Carbon::today())
            ->where('type', TransactionType::CHARGE_WALLET)
            ->whereIn('payment_method', PaymentMethod::incomeEnumValues())
            ->where('status', TransactionStatus::COMPLETED)
            ->sum('amount');

        return [
            'total_transactions' => $totalTransactions,
            'total_wallet_balance' => round($totalWalletBalance, 2),
            'total_income' => round($totalIncome, 2),
            'by_status' => $transactionsByStatus,
            'by_type' => $transactionsByType,
            'by_payment_method' => $transactionsByPaymentMethod,
            'today' => [
                'count' => $todayTransactions,
                'income' => round($todayIncome, 2)
            ]
        ];
    }

    private function getRevenueStats(): array
    {
        $completedOrders = Order::where('status', OrderStatusEnum::COMPLETED)->get();

        $totalRevenue = $completedOrders->sum('total_amount');
        $totalSubtotal = $completedOrders->sum('subtotal');
        $totalDeliveryFees = $completedOrders->sum('delivery_cost');

        $averageOrderValue = $completedOrders->count() > 0 ? round($totalRevenue / $completedOrders->count(), 2) : 0;

        $todayRevenue = Order::where('status', OrderStatusEnum::COMPLETED)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $monthRevenue = Order::where('status', OrderStatusEnum::COMPLETED)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $paymentMethodRevenue = UserTransaction::where('type', TransactionType::ORDER_PAYMENT)
            ->where('status', TransactionStatus::COMPLETED)
            ->select('payment_method', DB::raw('SUM(ABS(amount)) as total'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(function ($item) {
                $method = $item->payment_method;
                if (!$method) {
                    return [

                    ];
                }
                return [
                    $method->value => [
                        'label' => $method->label(),
                        'amount' => round($item->total, 2)
                    ]
                ];
            })
            ->toArray();

        return [
            'total_revenue' => round($totalRevenue, 2),
            'total_subtotal' => round($totalSubtotal, 2),
            'total_delivery_fees' => round($totalDeliveryFees, 2),
            'average_order_value' => $averageOrderValue,
            'today_revenue' => round($todayRevenue, 2),
            'month_revenue' => round($monthRevenue, 2),
            'by_payment_method' => $paymentMethodRevenue
        ];
    }

    private function getOverviewStats(): array
    {
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalChefs = Chef::count();
        $totalRevenue = Order::where('status', OrderStatusEnum::COMPLETED)->sum('total_amount');
        $activeOrders = Order::whereIn('status', OrderStatusEnum::activeStatuses())->count();
        $pendingTickets = Ticket::whereIn('status', [
            TicketStatusEnum::USER_CREATED,
            TicketStatusEnum::UNDER_REVIEW,
            TicketStatusEnum::USER_ANSWERED
        ])->count();

        $growthData = $this->getGrowthData();

        return [
            'totals' => [
                'orders' => $totalOrders,
                'users' => $totalUsers,
                'chefs' => $totalChefs,
                'revenue' => round($totalRevenue, 2)
            ],
            'active' => [
                'orders' => $activeOrders,
                'tickets_pending' => $pendingTickets
            ],
            'growth' => $growthData
        ];
    }

    private function getGrowthData(): array
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthOrders = Order::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();

        $lastMonthOrders = Order::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $currentMonthUsers = User::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();

        $lastMonthUsers = User::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $orderGrowth = $lastMonthOrders > 0 ? round(
            (($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100,
            2
        ) : 0;
        $userGrowth = $lastMonthUsers > 0 ? round(
            (($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100,
            2
        ) : 0;

        return [
            'orders_growth_percentage' => $orderGrowth,
            'users_growth_percentage' => $userGrowth,
            'current_month_orders' => $currentMonthOrders,
            'last_month_orders' => $lastMonthOrders,
            'current_month_users' => $currentMonthUsers,
            'last_month_users' => $lastMonthUsers
        ];
    }

    private function getOrderStatusLabel(OrderStatusEnum $status): string
    {
        return match ($status) {
            OrderStatusEnum::PENDING_PAYMENT => 'Pending Payment',
            OrderStatusEnum::PAYMENT_PROCESSING => 'Payment Processing',
            OrderStatusEnum::PENDING => 'Pending',
            OrderStatusEnum::ACCEPTED => 'Accepted',
            OrderStatusEnum::REFUSED_BY_CHEF => 'Refused by Chef',
            OrderStatusEnum::REFUSED_BY_USER => 'Refused by User',
            OrderStatusEnum::DELIVERY_CHANGE_REQUESTED => 'Delivery Change Requested',
            OrderStatusEnum::READY_FOR_PICKUP => 'Ready for Pickup',
            OrderStatusEnum::READY_FOR_DELIVERY => 'Ready for Delivery',
            OrderStatusEnum::COMPLETED => 'Completed',
            OrderStatusEnum::CANCELLED => 'Cancelled',
            OrderStatusEnum::FAILED_PAYMENT => 'Failed Payment',
        };
    }

    private function getChefStatusLabel(ChefStatusEnum $status): string
    {
        return match ($status) {
            ChefStatusEnum::Registered => 'Registered',
            ChefStatusEnum::PersonalInfoFilled => 'Personal Info Filled',
            ChefStatusEnum::DocumentUploaded => 'Document Uploaded',
            ChefStatusEnum::ContractSigned => 'Contract Signed',
            ChefStatusEnum::NeedAdminApproval => 'Need Admin Approval',
            ChefStatusEnum::Approved => 'Approved',
            ChefStatusEnum::Rejected => 'Rejected',
            ChefStatusEnum::Pending => 'Pending',
        };
    }
}