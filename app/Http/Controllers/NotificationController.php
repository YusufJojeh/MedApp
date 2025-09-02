<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display user's notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Notification::where('user_id', $user->id);

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        // Get notifications with pagination
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get notification statistics
        $stats = [
            'total' => Notification::where('user_id', $user->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->unread()->count(),
            'read' => Notification::where('user_id', $user->id)->read()->count(),
            'appointments' => Notification::where('user_id', $user->id)->where('type', 'appointment')->count(),
        ];

        // Get notification types for filter
        $types = Notification::where('user_id', $user->id)
            ->distinct()
            ->pluck('type');

        if ($request->ajax()) {
            return response()->json([
                'notifications' => $notifications,
                'stats' => $stats,
                'types' => $types
            ]);
        }

        return view('notifications.index', compact('notifications', 'stats', 'types'));
    }

    /**
     * Get unread notifications count (for navbar)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = Notification::where('user_id', $user->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for dropdown)
     */
    public function getRecent()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->markAsUnread();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Delete all read notifications
     */
    public function deleteRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->read()
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get notification settings
     */
    public function getSettings()
    {
        $user = Auth::user();

        // Get user's notification preferences
        $settings = DB::table('users')
            ->where('id', $user->id)
            ->select([
                'email_notifications',
                'sms_notifications',
                'push_notifications',
                'appointment_reminders',
                'payment_notifications',
                'system_notifications'
            ])
            ->first();

        return response()->json(['settings' => $settings]);
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'appointment_reminders' => 'boolean',
            'payment_notifications' => 'boolean',
            'system_notifications' => 'boolean',
        ]);

        DB::table('users')
            ->where('id', $user->id)
            ->update($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Test notification (for admin)
     */
    public function testNotification(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'priority' => 'in:low,normal,high,urgent',
            'user_id' => 'required|exists:users,id'
        ]);

        $notificationService = app(\App\Services\NotificationService::class);

        $notification = $notificationService->createNotification(
            $validated['user_id'],
            $validated['type'],
            $validated['title'],
            $validated['message'],
            [
                'priority' => $validated['priority'] ?? 'normal',
                'icon' => '🧪',
                'color' => 'blue'
            ]
        );

        return response()->json([
            'success' => true,
            'notification' => $notification
        ]);
    }

    /**
     * Display the notification settings page.
     *
     * @return \Illuminate\View\View
     */
    public function showSettingsPage()
    {
        return view('notifications.settings');
    }

    /**
     * Get notification statistics (for admin)
     */
    public function getStats()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::unread()->count(),
            'notifications_today' => Notification::whereDate('created_at', today())->count(),
            'notifications_this_week' => Notification::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'notifications_by_type' => Notification::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
            'notifications_by_priority' => Notification::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get(),
        ];

        return response()->json(['stats' => $stats]);
    }
}
