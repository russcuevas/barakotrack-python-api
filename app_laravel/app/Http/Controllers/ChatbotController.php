<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function query(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:500'
        ]);

        $query = $validated['query'];
        $userName = auth()->check() ? auth()->user()->name : 'Student';

        // Try Python AI Microservice on localhost:5000
        try {
            $response = Http::timeout(2)->post('http://127.0.0.1:5000/chatbot', [
                'query' => $query,
                'user_name' => $userName
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            // Fallback response if Python service is offline
        }

        // Built-in PHP Intelligent Response Engine
        $q = strtolower($query);
        $message = "I am Brahmmy! How can I help you recover or report a lost item?";
        $suggestions = ["How to report a found item?", "How to report a lost item?", "Where is the lost and found office?", "Office Hours"];

        if (str_contains($q, 'found') || (str_contains($q, 'report') && str_contains($q, 'found')) || str_contains($q, 'surrender') || str_contains($q, 'turn in')) {
            $message = "🏢 **How to Report a Found Item:**\nIf you found an item on campus, you can go to the **Student Affairs Office (SAO)** (Ground Floor, Main Admin Building) to surrender the item so our SAO admin can register it into storage!";
        } elseif (str_contains($q, 'where') || str_contains($q, 'location') || str_contains($q, 'office')) {
            $message = "📍 **Lost & Found Office Location:** Ground Floor, Main Admin Building (Student Affairs Office & Campus Security Headquarters).";
        } elseif (str_contains($q, 'hour') || str_contains($q, 'time') || str_contains($q, 'open')) {
            $message = "⏰ **Office Hours:** Monday to Friday (8:00 AM - 5:00 PM), Saturday (8:00 AM - 12:00 PM).";
        } elseif (str_contains($q, 'claim') || str_contains($q, 'proof')) {
            $message = "🛡️ **Claiming Process:** Browse Found Items, click 'Submit Claim', describe your proof of ownership (serial #, stickers, contents), and SAO will verify your request!";
        } elseif (str_contains($q, 'report') || str_contains($q, 'lost')) {
            $message = "📝 **Reporting Guide:** Click 'Report Lost Item' in the menu, fill in the details, date, location, and photo so our CNN AI visual matcher can look for matches!";
        }

        return response()->json([
            'status' => 'success',
            'response' => [
                'message' => $message,
                'suggestions' => $suggestions
            ]
        ]);
    }
}
