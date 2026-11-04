<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BookingRequest;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class BookingRequestController extends Controller
{
    /**
     * Store a newly created booking request from student.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'wa_number' => 'required|string|max:20',
            'question' => 'required|string',
            'requested_schedule' => 'required|string|max:100',
        ]);

        BookingRequest::create([
            'user_id' => Auth::id(),
            'course_id' => $request->course_id,
            'wa_number' => $request->wa_number,
            'question' => $request->question,
            'requested_schedule' => $request->requested_schedule,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pemesanan kelas privat berhasil diajukan! Mentor kami akan menghubungi Anda segera melalui WhatsApp/Email.');
    }

    /**
     * Display a listing of booking requests in admin CMS.
     */
    public function adminIndex()
    {
        $bookings = BookingRequest::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified booking request in admin CMS.
     */
    public function adminShow(BookingRequest $booking)
    {
        $booking->load(['user', 'course']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Toggle the status of a booking request.
     */
    public function adminToggleStatus(BookingRequest $booking)
    {
        $booking->status = $booking->status === 'pending' ? 'solved' : 'pending';
        $booking->save();

        return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui!');
    }

    /**
     * Delete a booking request.
     */
    public function adminDestroy(BookingRequest $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Pemesanan berhasil dihapus!');
    }
}
