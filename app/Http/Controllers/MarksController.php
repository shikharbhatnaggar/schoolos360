<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamEnrollment;
use App\Models\ExamSchedule;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MarksController extends Controller
{
    public function index(Request $request)
    {
        $currentSession = AcademicSession::current();
        $sessions = AcademicSession::orderByDesc('start_date')->get();

        $sessionId = $request->query('session_id', $currentSession ? $currentSession->id : null);
        $exams = Exam::when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))->get();

        $examId = $request->query('exam_id', $exams->first()?->id);
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $classId = $request->query('class_id', $classes->first()?->id);
        $sectionId = $request->query('section_id');

        $subjects = Subject::orderBy('name')->get();
        $subjectId = $request->query('subject_id', $subjects->first()?->id);

        $schedule = null;
        $studentsWithMarks = collect();

        if ($examId && $classId && $subjectId) {
            $schedule = ExamSchedule::where('exam_id', $examId)
                ->where('school_class_id', $classId)
                ->where('subject_id', $subjectId)
                ->first();

            if ($schedule) {
                // Get enrolled students
                $enrolledStudentIds = ExamEnrollment::where('exam_id', $examId)->pluck('student_id');

                $studentsQuery = Student::whereIn('id', $enrolledStudentIds)
                    ->where('school_class_id', $classId)
                    ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                    ->with(['section', 'marks' => function ($q) use ($schedule) {
                        $q->where('exam_schedule_id', $schedule->id);
                    }])
                    ->orderBy('roll_no')
                    ->orderBy('first_name');

                $studentsWithMarks = $studentsQuery->get();
            }
        }

        return view('marks.index', compact(
            'sessions',
            'sessionId',
            'exams',
            'examId',
            'classes',
            'classId',
            'sectionId',
            'subjects',
            'subjectId',
            'schedule',
            'studentsWithMarks'
        ));
    }

    public function saveManual(Request $request)
    {
        $validated = $request->validate([
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'marks' => 'required|array',
            'absent' => 'nullable|array',
            'remarks' => 'nullable|array',
        ]);

        $schedule = ExamSchedule::findOrFail($validated['exam_schedule_id']);
        $maxMarks = $schedule->max_marks;

        $count = 0;
        foreach ($validated['marks'] as $studentId => $obtained) {
            $isAbsent = isset($validated['absent'][$studentId]) && $validated['absent'][$studentId] == '1';
            $remark = $validated['remarks'][$studentId] ?? null;

            $score = null;
            if (!$isAbsent && $obtained !== null && $obtained !== '') {
                $score = min((float)$obtained, $maxMarks);
            }

            Mark::updateOrCreate(
                [
                    'exam_schedule_id' => $schedule->id,
                    'student_id' => $studentId,
                ],
                [
                    'marks_obtained' => $isAbsent ? null : $score,
                    'is_absent' => $isAbsent,
                    'remarks' => $remark,
                ]
            );
            $count++;
        }

        return back()->with('success', "Saved marks for {$count} student(s).");
    }

    public function downloadTemplate(Request $request)
    {
        $examId = $request->query('exam_id');
        $classId = $request->query('class_id');
        $subjectId = $request->query('subject_id');
        $sectionId = $request->query('section_id');

        $schedule = ExamSchedule::where('exam_id', $examId)
            ->where('school_class_id', $classId)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        $enrolledIds = ExamEnrollment::where('exam_id', $examId)->pluck('student_id');

        $students = Student::whereIn('id', $enrolledIds)
            ->where('school_class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->with(['section', 'marks' => fn($q) => $q->where('exam_schedule_id', $schedule->id)])
            ->orderBy('roll_no')
            ->orderBy('first_name')
            ->get();

        $filename = "marks_template_{$schedule->exam->name}_{$schedule->schoolClass->name}_{$schedule->subject->code}.csv";
        $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return new StreamedResponse(function () use ($students, $schedule) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'student_id',
                'admission_no',
                'roll_no',
                'student_name',
                'section',
                'max_marks',
                'marks_obtained',
                'is_absent',
                'remarks'
            ]);

            foreach ($students as $s) {
                $mark = $s->marks->first();
                fputcsv($handle, [
                    $s->id,
                    $s->admission_no,
                    $s->roll_no,
                    $s->full_name,
                    $s->section?->name,
                    $schedule->max_marks,
                    $mark?->marks_obtained !== null ? $mark->marks_obtained : '',
                    $mark && $mark->is_absent ? '1' : '0',
                    $mark?->remarks ?? ''
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $schedule = ExamSchedule::findOrFail($request->exam_schedule_id);
        $file = $request->file('csv_file');

        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        // Normalize header row to lower case
        $header = array_map('trim', array_map('strtolower', $header));

        $idIndex = array_search('student_id', $header);
        $admissionIndex = array_search('admission_no', $header);
        $marksIndex = array_search('marks_obtained', $header);
        $absentIndex = array_search('is_absent', $header);
        $remarksIndex = array_search('remarks', $header);

        if ($marksIndex === false || ($idIndex === false && $admissionIndex === false)) {
            fclose($handle);
            return back()->with('error', 'Invalid CSV format. Missing student_id/admission_no or marks_obtained header.');
        }

        $imported = 0;
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($row) || !isset($row[$marksIndex])) {
                    continue;
                }

                $studentId = null;
                if ($idIndex !== false && !empty($row[$idIndex])) {
                    $studentId = (int)$row[$idIndex];
                } elseif ($admissionIndex !== false && !empty($row[$admissionIndex])) {
                    $student = Student::where('admission_no', trim($row[$admissionIndex]))->first();
                    $studentId = $student?->id;
                }

                if (!$studentId) {
                    continue;
                }

                $isAbsent = false;
                if ($absentIndex !== false && isset($row[$absentIndex])) {
                    $val = strtolower(trim($row[$absentIndex]));
                    $isAbsent = in_array($val, ['1', 'true', 'yes', 'absent']);
                }

                $marks = null;
                if (!$isAbsent && trim($row[$marksIndex]) !== '') {
                    $marks = min((float)trim($row[$marksIndex]), $schedule->max_marks);
                }

                $remarks = ($remarksIndex !== false && isset($row[$remarksIndex])) ? trim($row[$remarksIndex]) : null;

                Mark::updateOrCreate(
                    [
                        'exam_schedule_id' => $schedule->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'marks_obtained' => $isAbsent ? null : $marks,
                        'is_absent' => $isAbsent,
                        'remarks' => $remarks,
                    ]
                );
                $imported++;
            }

            DB::commit();
            fclose($handle);

            return back()->with('success', "Successfully imported marks for {$imported} student(s) from CSV!");
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Error parsing CSV file: ' . $e->getMessage());
        }
    }
}

