<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithHeadingRow
{
    public int $successCount = 0;
    public array $errors = [];

    // Lookup maps loaded once for the entire import
    private Collection $faculties;
    private Collection $departments;

    public function __construct()
    {
        // Load all faculties and departments once upfront — avoids N+1 queries
        // We store them keyed by lowercased name for forgiving matching
        $this->faculties = Faculty::all()->keyBy(fn($f) => strtolower(trim($f->name)));
        $this->departments = Department::all()->keyBy(fn($d) => strtolower(trim($d->name)));
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Row 1 is the heading

            // Skip completely empty rows (common in Excel files)
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $this->processRow($row->toArray(), $rowNumber);
        }
    }

    private function processRow(array $row, int $rowNumber): void
    {
        // --- Basic validation of required fields ---
        $matricNumber = trim($row['matric_number'] ?? '');

        if (empty($matricNumber)) {
            $this->errors[] = [
                'row'     => $rowNumber,
                'field'   => 'matric_number',
                'message' => 'Matric number is required and was missing.',
                'data'    => $row,
            ];
            return;
        }

        $name = trim($row['name'] ?? '');
        if (empty($name)) {
            $this->errors[] = [
                'row'     => $rowNumber,
                'field'   => 'name',
                'message' => "Row {$rowNumber}: Student name is required.",
                'data'    => $row,
            ];
            return;
        }

        // --- Resolve faculty and department by name ---
        $facultyId = $this->resolveFaculty($row['faculty'] ?? '', $rowNumber);
        $departmentId = $this->resolveDepartment($row['department'] ?? '', $rowNumber);

        if ($facultyId === null || $departmentId === null) {
            // Errors already recorded inside the resolve methods
            return;
        }

        // --- Resolve email: treat empty string as null ---
        $email = $this->sanitizeEmail($row['email'] ?? null);

        try {
            DB::transaction(function () use ($row, $matricNumber, $name, $email, $facultyId, $departmentId) {

                // Find existing student by matric number (our only reliable anchor)
                $existingStudent = Student::where('matric_number', $matricNumber)->first();

                if ($existingStudent) {
                    // --- UPDATE PATH ---
                    // Update the linked user record
                    $existingStudent->user->update(
                        $this->buildUserData($name, $email, $facultyId, $departmentId, $row)
                    );

                    // Update the student record
                    $existingStudent->update(
                        $this->buildStudentData($facultyId, $row)
                    );

                } else {
                    // --- INSERT PATH ---
                    $user = User::create(
                        $this->buildUserData($name, $email, $facultyId, $departmentId, $row)
                    );

                    Student::create(array_merge(
                        $this->buildStudentData($facultyId, $row),
                        [
                            'user_id'      => $user->id,
                            'matric_number' => $matricNumber,
                        ]
                    ));
                }
            });

            $this->successCount++;

        } catch (\Exception $e) {
            $this->errors[] = [
                'row'     => $rowNumber,
                'message' => "Failed to save student with matric number '{$matricNumber}': " . $this->friendlyError($e),
                'data'    => $row,
            ];
        }
    }

    // --- Data builders keep the logic clean and DRY ---

    private function buildUserData(string $name, ?string $email, int $facultyId, int $departmentId, array $row): array
    {
        return [
            'name'          => $name,
            'email'         => $email,
            'password'      => Hash::make('ChangeMe@123'), // forced reset on first login recommended
            'role_id'       => 4,
            'department_id' => $departmentId,
            'faculty_id'    => $facultyId,
            'phone'         => $this->sanitizePhone($row['phone'] ?? null),
            'dob'           => $this->sanitizeDate($row['date_of_birth'] ?? null),
            'is_active'     => 1,
        ];
    }

    private function buildStudentData(int $facultyId, array $row): array
    {
        return [
            'admission_year'  => $row['admission_year'] ?? null,
            'graduation_year' => $row['graduation_year'] ?? null,
            'status'          => $row['status'] ?? 'active',
            'mode_entry'      => $row['mode_entry'] ?? null,
            'level'           => $row['level'] ?? null,
            'gender'          => strtolower(trim($row['gender'] ?? '')),
        ];
    }

    // --- Lookup resolvers ---

    private function resolveFaculty(string $value, int $rowNumber): ?int
    {
        $key = strtolower(trim($value));

        if (empty($key)) {
            $this->errors[] = [
                'row'     => $rowNumber,
                'field'   => 'faculty',
                'message' => "Faculty name is required but was empty.",
            ];
            return null;
        }

        $faculty = $this->faculties->get($key);

        if (!$faculty) {
            $available = $this->faculties->keys()->implode(', ');
            $this->errors[] = [
                'row'     => $rowNumber,
                'field'   => 'faculty',
                'message' => "Faculty '{$value}' was not found. Available faculties are: {$available}.",
            ];
            return null;
        }

        return $faculty->id;
    }

    private function resolveDepartment(string $value, int $rowNumber): ?int
    {
        $key = strtolower(trim($value));

        if (empty($key)) {
            $this->errors[] = [
                'row'     => $rowNumber,
                'field'   => 'department',
                'message' => "Department name is required but was empty.",
            ];
            return null;
        }

        $department = $this->departments->get($key);

        if (!$department) {
            $available = $this->departments->keys()->implode(', ');
            $this->errors[] = [
                'row'     => $rowNumber,
                'field'   => 'department',
                'message' => "Department '{$value}' was not found. Available departments are: {$available}.",
            ];
            return null;
        }

        return $department->id;
    }

    // --- Sanitizers ---

    private function sanitizeEmail(?string $email): ?string
    {
        $email = trim($email ?? '');
        // Return null for empty or placeholder values
        if (empty($email) || strtolower($email) === 'n/a' || strtolower($email) === 'none') {
            return null;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? strtolower($email) : null;
    }

    private function sanitizePhone(?string $phone): ?string
    {
        $phone = trim($phone ?? '');
        return empty($phone) ? null : $phone;
    }

    private function sanitizeDate(?string $date): ?string
    {
        if (empty(trim($date ?? ''))) return null;
        try {
            return \Carbon\Carbon::parse($date)->toDateString();
        } catch (\Exception) {
            return null; // Don't fail the row over a bad date format
        }
    }

    private function isEmptyRow(Collection $row): bool
    {
        return $row->filter(fn($value) => !empty(trim($value ?? '')))->isEmpty();
    }

    // Translate database exceptions into plain English
    private function friendlyError(\Exception $e): string
    {
        if ($e instanceof \Illuminate\Database\QueryException) {
            if ($e->getCode() === '23000') {
                return 'A student with this email already exists in the system.';
            }
        }
        return 'An unexpected error occurred. Please check this row and try again.';
    }
}
