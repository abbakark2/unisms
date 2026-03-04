<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\{Faculty, Department};

class ImportController extends Controller
{
    // Student import methods below here__________________________________________________________________________________
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $import = new StudentImport();
        Excel::import($import, $request->file('file'));

        $errorCount = count($import->errors);

        return response()->json([
            'success'       => true,
            'success_count' => $import->successCount,
            'error_count'   => $errorCount,
            'errors'        => $import->errors,
            'message'       => $this->buildSummaryMessage($import->successCount, $errorCount),
        ]);
    }

    private function buildSummaryMessage(int $success, int $errors): string
    {
        if ($errors === 0) {
            return "All {$success} student(s) were imported successfully.";
        }

        if ($success === 0) {
            return "Import failed. No students were saved. Please review the {$errors} error(s) below and fix your file.";
        }

        return "{$success} student(s) imported successfully. {$errors} row(s) had issues and were skipped — see details below.";
    }


    public function uploadMeta()
    {
        return response()->json([
            'faculties'   => Faculty::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
        ]);
    }

    public function downloadTemplate()
    {
        $headers = [
            'name', 'email', 'phone', 'date_of_birth',
            'faculty', 'department',
            'matric_number', 'admission_year', 'graduation_year',
            'level', 'gender', 'status', 'mode_entry'
        ];

        $sample = [
            'John Doe', 'john@example.com', '08012345678', '2000-05-15',
            'Engineering', 'Computer Science',
            'ENG/2020/001', '2020', '2024',
            '300', 'male', 'active', 'UTME'
        ];

        $callback = function () use ($headers, $sample) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fputcsv($handle, $sample);
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_upload_template.csv"',
        ]);
    }
    // Student import methods ABOVE here___________________________________ABOVE_______________________________________________
}
