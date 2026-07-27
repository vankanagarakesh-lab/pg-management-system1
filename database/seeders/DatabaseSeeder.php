<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PgBuilding;
use App\Models\Room;
use App\Models\Payment;
use App\Models\PaymentConfig;
use App\Models\Complaint;
use App\Models\FoodPreference;
use App\Models\FoodMenu;
use App\Models\Notice;
use App\Models\LandingContent;
use App\Models\Inventory;
use App\Models\SystemNotification;
use App\Models\CommonAreaTask;
use App\Models\WorkReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed PG Buildings
        $pg1 = PgBuilding::create(['name' => 'Grand Palace PG (Boys)', 'address' => '12 Main Rd, Sector 4, Bangalore', 'status' => 'active']);
        $pg2 = PgBuilding::create(['name' => 'Elite Residency (Girls)', 'address' => '45 Park St, Sector 2, Bangalore', 'status' => 'active']);
        $pg3 = PgBuilding::create(['name' => 'Luxury Haven PG (Unisex)', 'address' => '88 Elite Dr, Sector 1, Bangalore', 'status' => 'inactive']);

        // 2. Seed Rooms
        $r101 = Room::create(['pg_building_id' => $pg1->id, 'number' => '101', 'type' => 'Single Sharing', 'rent' => 12000, 'capacity' => 1, 'occupied' => 1, 'assigned_to' => 'Ramesh Kumar', 'cleaning_status' => 'Cleaned']);
        $r102 = Room::create(['pg_building_id' => $pg1->id, 'number' => '102', 'type' => 'Double Sharing', 'rent' => 8000, 'capacity' => 2, 'occupied' => 1, 'assigned_to' => 'Ramesh Kumar', 'cleaning_status' => 'Dirty']);
        $r103 = Room::create(['pg_building_id' => $pg1->id, 'number' => '103', 'type' => 'Triple Sharing', 'rent' => 6000, 'capacity' => 3, 'occupied' => 0, 'assigned_to' => 'Sohan Lal', 'cleaning_status' => 'Cleaned']);
        $r104 = Room::create(['pg_building_id' => $pg1->id, 'number' => '104', 'type' => 'Single Sharing', 'rent' => 13000, 'capacity' => 1, 'occupied' => 0, 'assigned_to' => 'Sohan Lal', 'cleaning_status' => 'Dirty']);
        
        $r201 = Room::create(['pg_building_id' => $pg2->id, 'number' => '201', 'type' => 'Single Sharing', 'rent' => 14000, 'capacity' => 1, 'occupied' => 1, 'assigned_to' => 'Ramesh Kumar', 'cleaning_status' => 'Cleaned']);
        $r202 = Room::create(['pg_building_id' => $pg2->id, 'number' => '202', 'type' => 'Double Sharing', 'rent' => 9000, 'capacity' => 2, 'occupied' => 0, 'assigned_to' => 'Sohan Lal', 'cleaning_status' => 'Dirty']);

        Room::create(['pg_building_id' => $pg3->id, 'number' => '301', 'type' => 'Single Sharing', 'rent' => 15000, 'capacity' => 1, 'occupied' => 0, 'assigned_to' => 'Sohan Lal', 'cleaning_status' => 'Dirty']);

        // 3. Seed Users
        // Admin
        User::create([
            'name' => 'Rohan Sharma (Owner)',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'phone' => '9876543210',
            'role' => 'admin',
            'approval_status' => 'approved',
        ]);

        // Staff members
        User::create([
            'name' => 'Ramesh Kumar',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('staff'),
            'phone' => '8765432109',
            'role' => 'staff',
            'staff_role' => 'Housekeeping',
            'approval_status' => 'approved',
            'pg_building_id' => $pg1->id
        ]);

        User::create([
            'name' => 'Sohan Lal',
            'email' => 'housekeeping@gmail.com',
            'password' => Hash::make('staff'),
            'phone' => '8765432104',
            'role' => 'staff',
            'staff_role' => 'Housekeeping',
            'approval_status' => 'approved',
            'pg_building_id' => $pg1->id
        ]);

        User::create([
            'name' => 'Karan Johar',
            'email' => 'food@gmail.com',
            'password' => Hash::make('staff'),
            'phone' => '8765432108',
            'role' => 'staff',
            'staff_role' => 'Food Management',
            'approval_status' => 'approved',
            'pg_building_id' => $pg1->id
        ]);

        User::create([
            'name' => 'Amit Trivedi',
            'email' => 'maintenance@gmail.com',
            'password' => Hash::make('staff'),
            'phone' => '8765432107',
            'role' => 'staff',
            'staff_role' => 'Maintenance',
            'approval_status' => 'approved',
            'pg_building_id' => $pg1->id
        ]);

        // Approved Student 1
        User::create([
            'name' => 'Rahul Verma',
            'email' => 'student1@gmail.com',
            'password' => Hash::make('student'),
            'phone' => '7654321098',
            'room_number' => '101',
            'room_type' => 'Single Sharing',
            'year' => '3rd Year',
            'course' => 'B.Tech CSE',
            'college' => 'VIT University',
            'guardian_name' => 'Sanjay Verma',
            'guardian_phone' => '9000000001',
            'address' => 'Indore, MP',
            'approval_status' => 'approved',
            'pg_building_id' => $pg1->id,
            'room_id' => $r101->id
        ]);

        // Approved Student 2
        User::create([
            'name' => 'Priya Patel',
            'email' => 'student2@gmail.com',
            'password' => Hash::make('student'),
            'phone' => '6543210987',
            'room_number' => '201',
            'room_type' => 'Single Sharing',
            'year' => '2nd Year',
            'course' => 'MBA',
            'college' => 'IIM Bangalore',
            'guardian_name' => 'Mukesh Patel',
            'guardian_phone' => '9000000002',
            'address' => 'Ahmedabad, Gujarat',
            'approval_status' => 'approved',
            'pg_building_id' => $pg2->id,
            'room_id' => $r201->id
        ]);

        // Pending Student 3
        User::create([
            'name' => 'Aman Gupta',
            'email' => 'student3@gmail.com',
            'password' => Hash::make('student'),
            'phone' => '5432109876',
            'room_number' => '102',
            'room_type' => 'Double Sharing',
            'year' => '1st Year',
            'course' => 'BCA',
            'college' => 'Christ University',
            'guardian_name' => 'Alok Gupta',
            'guardian_phone' => '9000000003',
            'address' => 'Patna, Bihar',
            'approval_status' => 'pending',
            'pg_building_id' => $pg1->id,
            'room_id' => $r102->id
        ]);

        // 4. Seed Payments
        Payment::create([
            'student_email' => 'student1@gmail.com',
            'pg_building_id' => $pg1->id,
            'room_number' => '101',
            'month' => 'June 2026',
            'amount' => 12000,
            'status' => 'Paid',
            'payment_date' => '2026-06-05',
            'tx_id' => 'TXN8739183921',
            'method' => 'UPI (GPay)'
        ]);

        Payment::create([
            'student_email' => 'student1@gmail.com',
            'pg_building_id' => $pg1->id,
            'room_number' => '101',
            'month' => 'July 2026',
            'amount' => 12000,
            'status' => 'Due',
        ]);

        Payment::create([
            'student_email' => 'student2@gmail.com',
            'pg_building_id' => $pg2->id,
            'room_number' => '201',
            'month' => 'July 2026',
            'amount' => 14000,
            'status' => 'Paid',
            'payment_date' => '2026-07-02',
            'tx_id' => 'TXN9928172653',
            'method' => 'PhonePe'
        ]);

        // 5. Seed Payment Configuration
        PaymentConfig::create([
            'account_holder' => 'Grand Palace Hostels Pvt Ltd',
            'qr_code' => 'payments/mock_qr.jpg',
            'phonepe' => '9876543210',
            'gpay' => '9876543210',
            'paytm' => '9876543210',
            'other' => 'Scan QR at reception'
        ]);

        // 6. Seed Complaints
        Complaint::create([
            'student_email' => 'student1@gmail.com',
            'student_name' => 'Rahul Verma',
            'room_number' => '101',
            'title' => 'AC Remote not working',
            'description' => 'The batteries seem dead or the remote sensor is broken. Need replacement.',
            'status' => 'Resolved',
            'priority' => 'Medium',
            'category' => 'Electrical',
            'materials_used' => 'AAA Batteries x2',
            'repair_expense' => 60,
            'verification_status' => 'Verified',
            'assigned_to' => 'Ramesh Kumar',
            'created_date' => '2026-07-01',
            'resolved_date' => '2026-07-02',
            'reply' => 'Replaced remote batteries.'
        ]);

        Complaint::create([
            'student_email' => 'student2@gmail.com',
            'student_name' => 'Priya Patel',
            'room_number' => '201',
            'title' => 'Bathroom cleaning required',
            'description' => 'The toilet flush is leaking slightly, and periodic deep cleaning is pending.',
            'status' => 'Pending',
            'priority' => 'High',
            'category' => 'Plumbing',
            'verification_status' => 'Pending',
            'assigned_to' => 'Ramesh Kumar',
            'created_date' => '2026-07-03',
        ]);

        Complaint::create([
            'student_email' => 'student1@gmail.com',
            'student_name' => 'Rahul Verma',
            'room_number' => '101',
            'title' => 'WiFi disconnecting repeatedly',
            'description' => 'WiFi router keeps restarting in block A second floor.',
            'status' => 'Pending',
            'priority' => 'Emergency',
            'category' => 'Wi-Fi',
            'verification_status' => 'Pending',
            'assigned_to' => 'Amit Trivedi',
            'created_date' => '2026-07-04',
        ]);

        // 7. Seed Food Preferences
        FoodPreference::create([
            'email' => 'student1@gmail.com',
            'name' => 'Rahul Verma',
            'room' => '101',
            'date' => '2026-07-04',
            'morning' => true,
            'afternoon' => false,
            'evening' => true
        ]);

        FoodPreference::create([
            'email' => 'student2@gmail.com',
            'name' => 'Priya Patel',
            'room' => '201',
            'date' => '2026-07-04',
            'morning' => false,
            'afternoon' => true,
            'evening' => true
        ]);

        // 8. Seed Food Menus
        $menuDays = [
            'monday' => ['breakfast' => 'Idli, Vada, Chutney', 'lunch' => 'Rice, Dal, Bhindi Sabji, Curd', 'dinner' => 'Roti, Paneer Masala, Salad'],
            'tuesday' => ['breakfast' => 'Poha, Sev, Tea', 'lunch' => 'Veg Pulav, Raita, Papad', 'dinner' => 'Roti, Mixed Veg, Dal Fry'],
            'wednesday' => ['breakfast' => 'Aloo Paratha, Curd', 'lunch' => 'Rice, Dal Tadka, Aloo Gobhi', 'dinner' => 'Roti, Egg Curry / Kadhai Paneer'],
            'thursday' => ['breakfast' => 'Upma, Coconut Chutney', 'lunch' => 'Rajma, Rice, Salad', 'dinner' => 'Roti, Veg Kofta, Dal'],
            'friday' => ['breakfast' => 'Bread Butter Toast, Omelette', 'lunch' => 'Chole Bhature, Lassi', 'dinner' => 'Roti, Dal Makhani, Dum Aloo'],
            'saturday' => ['breakfast' => 'Puri Sabji', 'lunch' => 'Jeera Rice, Kadhi, Khichdi', 'dinner' => 'Roti, Sev Tamatar Sabji'],
            'sunday' => ['breakfast' => 'Masala Dosa, Sambhar', 'lunch' => 'Veg Biryani, Salan, Sweet', 'dinner' => 'Roti, Paneer Butter Masala, Ice Cream']
        ];
        foreach ($menuDays as $day => $meals) {
            FoodMenu::create([
                'day' => $day,
                'breakfast' => $meals['breakfast'],
                'lunch' => $meals['lunch'],
                'dinner' => $meals['dinner']
            ]);
        }

        // 9. Seed Notices
        Notice::create([
            'date' => '2026-07-03',
            'title' => 'Wi-Fi Maintenance Scheduled',
            'content' => 'The primary internet line will undergo maintenance on Sunday, July 5th from 2:00 AM to 5:00 AM. Expect brief downtime.',
            'target' => 'all'
        ]);

        Notice::create([
            'date' => '2026-07-01',
            'title' => 'Rent Payment Due Reminder',
            'content' => 'Dear Tenants, please clear your July rent dues by July 7th to avoid late fees. Contact admin for queries.',
            'target' => 'student'
        ]);

        // 10. Seed Inventory
        Inventory::create(['item' => 'Bed Sheets', 'count' => 45, 'status' => 'In Stock']);
        Inventory::create(['item' => 'LED Bulbs', 'count' => 12, 'status' => 'Low Stock']);
        Inventory::create(['item' => 'Cleaning Liquid (Ltr)', 'count' => 20, 'status' => 'In Stock']);
        Inventory::create(['item' => 'Wi-Fi Routers', 'count' => 3, 'status' => 'In Stock']);

        // 11. Seed Landing Content
        $facilities = [
            ['name' => 'High-Speed Wi-Fi', 'desc' => 'Premium dedicated 300 Mbps fiber line in every room.', 'icon' => 'fa-wifi'],
            ['name' => 'Organic & Healthy Meals', 'desc' => 'Delicious homelike vegetarian/non-vegetarian meals cooked daily.', 'icon' => 'fa-utensils'],
            ['name' => 'Daily Housekeeping', 'desc' => 'Professional cleaning staff maintaining rooms and common areas.', 'icon' => 'fa-broom'],
            ['name' => '24/7 Security & CCTV', 'desc' => 'Secure biometric access control and smart camera surveillance.', 'icon' => 'fa-shield-halved'],
            ['name' => 'Power Backup & Hot Water', 'desc' => 'Full generator backup and solar water heating systems.', 'icon' => 'fa-bolt'],
            ['name' => 'Laundry Facilities', 'desc' => 'Self-service automatic washing machines and drying areas.', 'icon' => 'fa-shirt']
        ];
        $pricing = [
            ['name' => 'Triple Sharing', 'price' => '₹6,000 / month', 'desc' => 'Economical option for students with attached washroom and basic furniture.', 'features' => ['3 Single Beds', 'Attached Washroom', 'Unlimited Wi-Fi', '3 Meals/Day', 'Daily Housekeeping']],
            ['name' => 'Double Sharing', 'price' => '₹8,000 / month', 'desc' => 'Balanced privacy and comfort with dedicated wardrobes and study table.', 'features' => ['2 Single Beds', 'Attached Washroom', 'Unlimited Wi-Fi', '3 Meals/Day', 'Daily Housekeeping', 'Study Table']],
            ['name' => 'Single Occupancy', 'price' => '₹12,000 / month', 'desc' => 'Premium private room with maximum personal space, smart TV and AC.', 'features' => ['1 King Bed', 'Attached Washroom', 'Private Balcony', 'High-Speed Wi-Fi', '3 Meals/Day', 'Daily Housekeeping', 'AC & Smart TV']]
        ];
        $testimonials = [
            ['name' => 'Vikram Sethi', 'role' => 'Student, VIT', 'review' => 'The facilities here are phenomenal. High speed internet helps with my online exams and the food tastes just like home. Housekeeping is very regular.', 'photo' => ''],
            ['name' => 'Ananya Deshmukh', 'role' => 'Software Engineer', 'review' => 'Extremely safe and secure PG for women. The admin is highly professional and all maintenance issues raised via dashboard are fixed in hours.', 'photo' => '']
        ];
        $locations = [
            ['city' => 'Bangalore (HSR Layout)', 'area' => 'Prime campus location, walking distance to metro and restaurants.'],
            ['city' => 'Bangalore (Koramangala)', 'area' => 'Active tech hub, proximity to tech parks and shopping markets.']
        ];

        LandingContent::create([
            'seo_title' => 'Premium PG Accommodations | PG Management System',
            'seo_description' => 'Find luxury single, double, and triple sharing rooms with prime amenities, secure access, and professional housekeeping.',
            'seo_keywords' => 'PG near me, PG in Bangalore, Luxury PG, Student Hostel, Working Professionals PG, PG Booking',
            'banner_title' => 'Experience Premium Living',
            'banner_subtitle' => 'Modern, fully furnished accommodations designed for students and working professionals.',
            'banner_image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=1200&q=80',
            'about_text' => 'Our PG Management System delivers state-of-the-art living options across prime urban locations. We specialize in providing a clean, hygienic, secure, and smart living environment equipped with high-speed internet, power backup, organic meals, and round-the-clock housekeeping.',
            'contact_phone' => '+91 98765 43210',
            'contact_email' => 'contact@pgmanagement.com',
            'contact_address' => '12 Main Rd, Sector 4, HSR Layout, Bangalore - 560102',
            'facilities_json' => json_encode($facilities),
            'pricing_plans_json' => json_encode($pricing),
            'testimonials_json' => json_encode($testimonials),
            'locations_json' => json_encode($locations)
        ]);

        // 12. Seed System Notifications
        SystemNotification::create([
            'date' => '2026-07-04',
            'text' => 'New student registration: Aman Gupta (Email: student3@gmail.com) is pending approval.',
            'type' => 'admin',
            'read' => false
        ]);
        SystemNotification::create([
            'date' => '2026-07-03',
            'text' => 'Complaint registered by Priya Patel regarding Bathroom cleaning.',
            'type' => 'staff',
            'read' => false
        ]);
        SystemNotification::create([
            'date' => '2026-07-04',
            'text' => 'Welcome to your premium PG housing dashboard! Keep an eye on this feed for updates.',
            'type' => 'student',
            'read' => false
        ]);

        // 13. Seed Common Area Cleaning Tasks
        CommonAreaTask::create(['pg_building_id' => $pg1->id, 'area_name' => 'Lobby / Reception', 'status' => 'Cleaned', 'last_cleaned_at' => date('Y-m-d H:i'), 'assigned_to' => 'Ramesh Kumar']);
        CommonAreaTask::create(['pg_building_id' => $pg1->id, 'area_name' => 'First Floor Corridor', 'status' => 'Pending', 'assigned_to' => 'Ramesh Kumar']);
        CommonAreaTask::create(['pg_building_id' => $pg1->id, 'area_name' => 'Second Floor Corridor', 'status' => 'Pending', 'assigned_to' => 'Sohan Lal']);
        CommonAreaTask::create(['pg_building_id' => $pg1->id, 'area_name' => 'Hostel Mess Kitchen', 'status' => 'Cleaned', 'last_cleaned_at' => date('Y-m-d H:i'), 'assigned_to' => 'Ramesh Kumar']);
        CommonAreaTask::create(['pg_building_id' => $pg1->id, 'area_name' => 'Dining Hall', 'status' => 'Pending', 'assigned_to' => 'Sohan Lal']);
        CommonAreaTask::create(['pg_building_id' => $pg1->id, 'area_name' => 'Lawn & Parking Area', 'status' => 'Cleaned', 'last_cleaned_at' => date('Y-m-d H:i'), 'assigned_to' => 'Sohan Lal']);
    }
}
