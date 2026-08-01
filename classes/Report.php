<?php
/**
 * EDUsync School Management System - A/L Academic Reports Model
 */

require_once __DIR__ . '/../config/Database.php';

class Report {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Get Overall Summary Metrics
     */
    public function getSummaryMetrics() {
        if ($this->db === null) return ['avg_score' => 0, 'a_pass_rate' => 0, 'total_students' => 0, 'total_marks' => 0];

        $stmtAvg = $this->db->query("SELECT AVG(marks_obtained) FROM marks");
        $avgScore = round((float)$stmtAvg->fetchColumn(), 1);

        $stmtTotal = $this->db->query("SELECT COUNT(*) FROM marks");
        $totalMarks = (int)$stmtTotal->fetchColumn();

        $stmtA = $this->db->query("SELECT COUNT(*) FROM marks WHERE grade = 'A'");
        $countA = (int)$stmtA->fetchColumn();
        $aPassRate = ($totalMarks > 0) ? round(($countA / $totalMarks) * 100, 1) : 0;

        $stmtSt = $this->db->query("SELECT COUNT(*) FROM students WHERE status = 'Active'");
        $totalStudents = (int)$stmtSt->fetchColumn();

        return [
            'avg_score' => $avgScore,
            'a_pass_rate' => $aPassRate,
            'total_students' => $totalStudents,
            'total_marks' => $totalMarks
        ];
    }

    /**
     * Get Individual Student Report Card Data
     */
    public function getStudentReportCard($studentId) {
        if ($this->db === null) return false;

        $stmtStudent = $this->db->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
        $stmtStudent->execute([':id' => $studentId]);
        $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

        if (!$student) return false;

        $stmtMarks = $this->db->prepare("
            SELECT m.*, c.course_code, c.course_name, c.grade AS subject_grade,
                   t.first_name AS teacher_first, t.last_name AS teacher_last
            FROM marks m
            JOIN courses c ON m.course_id = c.id
            LEFT JOIN teachers t ON c.teacher_id = t.id
            WHERE m.student_id = :student_id
            ORDER BY m.term ASC, c.course_name ASC
        ");
        $stmtMarks->execute([':student_id' => $studentId]);
        $marks = $stmtMarks->fetchAll(PDO::FETCH_ASSOC);

        $groupedMarks = ['Term 1' => [], 'Term 2' => [], 'Term 3' => []];
        $termTotals = ['Term 1' => 0, 'Term 2' => 0, 'Term 3' => 0];
        $termCounts = ['Term 1' => 0, 'Term 2' => 0, 'Term 3' => 0];
        $subjectsMap = [];

        foreach ($marks as $m) {
            $term = $m['term'];
            if (isset($groupedMarks[$term])) {
                $groupedMarks[$term][] = $m;
                $termTotals[$term] += (float)$m['marks_obtained'];
                $termCounts[$term]++;
            }

            $cid = $m['course_id'];
            if (!isset($subjectsMap[$cid])) {
                $subjectsMap[$cid] = [
                    'code' => $m['course_code'],
                    'name' => $m['course_name'],
                    't1_marks' => null, 't1_grade' => null, 't1_remarks' => null,
                    't2_marks' => null, 't2_grade' => null, 't2_remarks' => null,
                    't3_marks' => null, 't3_grade' => null, 't3_remarks' => null,
                    'scores' => []
                ];
            }
            if ($term === 'Term 1') {
                $subjectsMap[$cid]['t1_marks'] = $m['marks_obtained'];
                $subjectsMap[$cid]['t1_grade'] = $m['grade'];
                $subjectsMap[$cid]['t1_remarks'] = $m['remarks'];
            } elseif ($term === 'Term 2') {
                $subjectsMap[$cid]['t2_marks'] = $m['marks_obtained'];
                $subjectsMap[$cid]['t2_grade'] = $m['grade'];
                $subjectsMap[$cid]['t2_remarks'] = $m['remarks'];
            } elseif ($term === 'Term 3') {
                $subjectsMap[$cid]['t3_marks'] = $m['marks_obtained'];
                $subjectsMap[$cid]['t3_grade'] = $m['grade'];
                $subjectsMap[$cid]['t3_remarks'] = $m['remarks'];
            }
            $subjectsMap[$cid]['scores'][] = (float)$m['marks_obtained'];
        }

        return [
            'student' => $student,
            'marks_by_term' => $groupedMarks,
            'term_totals' => $termTotals,
            'term_counts' => $termCounts,
            'subject_matrix' => array_values($subjectsMap)
        ];
    }

    /**
     * Get Stream Performance Breakdown
     */
    public function getStreamPerformance() {
        if ($this->db === null) return [];

        $sql = "
            SELECT 
                CASE 
                    WHEN s.grade LIKE '%Physical Science%' THEN 'Physical Science (Maths)'
                    WHEN s.grade LIKE '%Bio Science%' THEN 'Biological Science'
                    WHEN s.grade LIKE '%Commerce%' THEN 'Commerce'
                    WHEN s.grade LIKE '%Technology%' THEN 'Technology'
                    WHEN s.grade LIKE '%Arts%' THEN 'Arts'
                    ELSE 'Other'
                END AS stream_name,
                COUNT(DISTINCT s.id) AS total_students,
                COUNT(m.id) AS total_evaluations,
                ROUND(AVG(m.marks_obtained), 1) AS avg_mark,
                SUM(CASE WHEN m.grade = 'A' THEN 1 ELSE 0 END) AS count_a,
                SUM(CASE WHEN m.grade IN ('A', 'B', 'C') THEN 1 ELSE 0 END) AS count_credit
            FROM students s
            LEFT JOIN marks m ON s.id = m.student_id
            GROUP BY stream_name
            ORDER BY stream_name ASC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Subject Grade Distribution Breakdown
     */
    public function getSubjectGradeDistribution() {
        if ($this->db === null) return [];

        $sql = "
            SELECT c.course_code, c.course_name, c.grade AS course_grade,
                   COUNT(m.id) AS total_evaluated,
                   ROUND(AVG(m.marks_obtained), 1) AS avg_score,
                   SUM(CASE WHEN m.grade = 'A' THEN 1 ELSE 0 END) AS count_a,
                   SUM(CASE WHEN m.grade = 'B' THEN 1 ELSE 0 END) AS count_b,
                   SUM(CASE WHEN m.grade = 'C' THEN 1 ELSE 0 END) AS count_c,
                   SUM(CASE WHEN m.grade = 'S' THEN 1 ELSE 0 END) AS count_s,
                   SUM(CASE WHEN m.grade = 'F' THEN 1 ELSE 0 END) AS count_f
            FROM courses c
            LEFT JOIN marks m ON c.id = m.course_id
            GROUP BY c.id
            HAVING total_evaluated > 0
            ORDER BY c.course_name ASC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
