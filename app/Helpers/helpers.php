<?php
use App\Models\Guardian;
use App\Models\Student;
use App\Models\UserFCMToken;
use Illuminate\Support\Str;

use App\Models\SchoolExamSchoolClass;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

if (!function_exists('getSchoolExamSchoolClassId')) {
    function getSchoolExamSchoolClassId($examId, $classId)
    {
        $record = SchoolExamSchoolClass::firstOrCreate([
            'school_exam_id' => $examId,
            'school_class_id' => $classId,
        ]);

        return $record->id;
    }
}

if (!function_exists('generateStudentAdmissionNumber')) {
    function generateStudentAdmissionNumber($examId, $classId)
    {

    }
}

if (!function_exists('generateRandomPassword')) {
    function generateRandomPassword($length = 6)
    {
        // Old logic (commented out):
        // $base = Str::random($length - 2);
        // $symbols = '!@#$%^&*';
        // return $base . $symbols[random_int(0, strlen($symbols) - 1)] . random_int(0, 9);

        // New logic: generate a numeric-only password
        $numbersOnly = '';
        for ($i = 0; $i < $length; $i++) {
            $numbersOnly .= random_int(0, 9);
        }
        return 123456;
    }
}

if (!function_exists('generateStdAdmission')) {
    function generateStdAdmission($length = 10)
    {
        return DB::transaction(function () {
            $year = now()->format('Y');
            $prefix = "STD-{$year}-";

            $lastStudent = Student::where('student_admission_number', 'LIKE', "{$prefix}%")
                ->orderBy('student_admission_number', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastStudent && preg_match('/\d+$/', $lastStudent->student_admission_number, $matches)) {
                $lastNumber = (int) $matches[0];
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }

            return $prefix . $newNumber;
        });
    }
}


if (!function_exists('sendFirebaseNotification')) {
    function sendFirebaseNotification($userType, $userId, $message, $titlee)
    {
        $title = $titlee;
        $body = $message;
        $tokens = UserFCMToken::where("user_id", $userId)->pluck("token");
        $messaging = app('firebase.messaging');

        $notification = Notification::create($title, $body);
        $cloudMessage = CloudMessage::new()
            ->withNotification($notification)
            ->withData([
                'custom_key' => 'custom_value',
            ]);

        try {
            $report = $messaging->sendMulticast($cloudMessage, $tokens);

            $failures = [];
            foreach ($report->failures() as $failure) {
                $failures[] = $failure;
            }
            return response()->json([
                'successCount' => $report->successes()->count(),
                'failureCount' => $report->failures()->count(),
                'failures' => array_map(fn($failure) => $failure->error()->getMessage(), $failures),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Push notification failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    if (!function_exists('generateGuardianInfo')) {
        function generateGuardianInfo($length = 10)
        {
            $userId = Auth::user()->id;
            $guardian = Guardian::with("user")->join("users", "users.id", "=", "guardians.user_id")->where("user_id", $userId)
                ->select("guardians.*")
                ->first();
            return $guardian;
        }
    }

    if (!function_exists('generatePageUrl')) {
        /**
         * Generate a page URL using APP_BASE_URL
         * 
         * @param string $slug The page slug or ID
         * @param string|null $ref The referral handle
         * @return string
         */
        function generatePageUrl($slug, $ref = null)
        {
            $baseUrl = env('APP_BASE_URL', 'http://localhost:8000');
            $url = rtrim($baseUrl, '/') . "/pages/{$slug}";
            
            if ($ref) {
                $url .= "?ref=" . urlencode($ref);
            }
            
            return $url;
        }
    }

}



