<?php

namespace App\Http\Controllers;

use App\Models\LandingContent;
use App\Models\Room;
use App\Models\PgBuilding;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $content = LandingContent::first();
        
        // Decode JSON arrays safely
        $facilities = json_decode($content?->facilities_json ?? '[]');
        $pricingPlans = json_decode($content?->pricing_plans_json ?? '[]');
        $testimonials = json_decode($content?->testimonials_json ?? '[]');
        $locations = json_decode($content?->locations_json ?? '[]');

        return view('landing', compact('content', 'facilities', 'pricingPlans', 'testimonials', 'locations'));
    }

    public function inquiry(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'required|email|max:255',
            'visitor_subject' => 'required|string|max:255',
            'visitor_message' => 'required|string',
        ]);

        \App\Models\Inquiry::create([
            'name' => $request->visitor_name,
            'email' => $request->visitor_email,
            'subject' => $request->visitor_subject,
            'message' => $request->visitor_message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thank you for reaching out! Our relationship manager will contact you shortly.');
    }
}
