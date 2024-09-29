<?php

namespace App\Http\Controllers;

use App\Models\Notification1;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreNotification1Request;
use App\Http\Requests\UpdateNotification1Request;

class Notification1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id; // Assurez-vous que l'utilisateur est authentifié

        $notifications = Notification1::where('user_id', $userId)->get();

        return response()->json([
            'status' => true,
            'notifications' => $notifications,
        ]);
    }


    public function unread()
    {
        $userId = auth()->user()->id;

        $unreadNotifications = Notification1::where('user_id', $userId)
            ->where('statut', 'non-lu')
            ->get();

        return response()->json([
            'status' => true,
            'unread_notifications' => $unreadNotifications,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification1::where('user_id', auth()->user()->id)->find($id);

        if ($notification) {
            $notification->statut = 'lu'; // Mettre à jour le statut
            $notification->save();
            return response()->json(['status' => true, 'message' => 'Notification marquée comme lue.']);
        }

        return response()->json(['status' => false, 'message' => 'Notification non trouvée.'], 404);
    }
}
