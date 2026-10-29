<form action="#" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
    @csrf

    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Book Service Appointment</h3>

    <!-- Date Picker -->
    <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Select Date</label>
        <input type="date" name="booking_date" required 
               class="w-full p-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary text-slate-800" />
    </div>

    <!-- Time Slot Selection -->
    <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Select Preferred Time</label>
        <div class="grid grid-cols-3 gap-2">
            <label class="cursor-pointer">
                <input type="radio" name="time_slot" value="09:00 AM" class="peer hidden" checked />
                <div class="p-2 text-center text-xs font-medium border border-slate-200 rounded-lg peer-checked:bg-tealPrimary peer-checked:text-white peer-checked:border-tealPrimary transition">09:00 AM</div>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="time_slot" value="01:00 PM" class="peer hidden" />
                <div class="p-2 text-center text-xs font-medium border border-slate-200 rounded-lg peer-checked:bg-tealPrimary peer-checked:text-white peer-checked:border-tealPrimary transition">01:00 PM</div>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="time_slot" value="04:00 PM" class="peer hidden" />
                <div class="p-2 text-center text-xs font-medium border border-slate-200 rounded-lg peer-checked:bg-tealPrimary peer-checked:text-white peer-checked:border-tealPrimary transition">04:00 PM</div>
            </label>
        </div>
    </div>

    <!-- Service Address -->
    <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Service Address</label>
        <textarea name="address" rows="2" placeholder="Enter your full home address..." required 
                  class="w-full p-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary text-slate-800"></textarea>
    </div>

    <!-- Special Instructions / Notes -->
    <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Special Instructions (Optional)</label>
        <input type="text" name="notes" placeholder="e.g. Ring the bell twice, beware of dog..." 
               class="w-full p-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary text-slate-800" />
    </div>

    <!-- Price Summary & Submit Button -->
    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
        <div>
            <span class="text-xs text-slate-400 block">Total Price</span>
            <span class="text-xl font-extrabold text-slate-900">$150</span>
        </div>
        <button type="submit" class="bg-tealPrimary hover:bg-tealHover text-white px-6 py-3 rounded-xl text-sm font-bold transition">
            Confirm Booking
        </button>
    </div>
</form>