<?php
/**
 * EDUsync School Management System - Term Test Marks & Report Card Service Model
 * Implements Authentic Sri Lankan Grading (A: 75+, B: 65+, C: 55+, S: 35+, F: <35)
 */

require_once __DIR__ . '/../config/Database.php';

class Mark {
    private $db;

    public function __construct() {
        $dbInstance = new Database();
        $this->db = $dbInstance->getConnection();
        $this->ensureTableExists();
    }

    /**
     * Auto-create marks table if it doesn't exist
     */
    private function ensureTableExists() {
        if ($this->db === null) return;
        $sql = "CREATE TABLE IF NOT EXISTS `marks` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `student_id` INT NOT NULL,
            `course_id` INT NOT NULL,
            `term` ENUM('Term 1', 'Term 2', 'Term 3') NOT NULL DEFAULT 'Term 1',
            `marks_obtained` DECIMAL(5,2) NOT NULL,
            `grade` VARCHAR(5) NOT NULL,
            `remarks` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $this->db->exec($sql);

        // Seed initial marks if table is empty
        $count = $this->db->query("SELECT COUNT(*) FROM marks")->fetchColumn();
        if ($count == 0) {
            $this->seedMarks();
        }
    }

    /**
     * Calculate Sri Lankan Grade based on score
     */
    public static function calculateGrade($score) {
        if ($score >= 75) return 'A';
        if ($score >= 65) return 'B';
        if ($score >= 55) return 'C';
        if ($score >= 35) return 'S';
        return 'F';
    }

    /**
     * Seed initial Sri Lankan Term Marks
     */
    private function seedMarks() {
        $sampleData = [
            ['student_id' => 1, 'course_id' => 1, 'term' => 'Term 1', 'marks_obtained' => 85.0, 'grade' => 'A', 'remarks' => 'Excellent performance in Mathematics'],
            ['student_id' => 1, 'course_id' => 3, 'term' => 'Term 1', 'marks_obtained' => 78.0, 'grade' => 'A', 'remarks' => 'Very good understanding of Biology'],
            ['student_id' => 2, 'course_id' => 2, 'term' => 'Term 1', 'marks_obtained' => 68.0, 'grade' => 'B', 'remarks' => 'Good essay writing skills'],
            ['student_id' => 3, 'course_id' => 1, 'term' => 'Term 1', 'marks_obtained' => 58.0, 'grade' => 'C', 'remarks' => 'Satisfactory performance'],
            ['student_id' => 4, 'course_id' => 4, 'term' => 'Term 1', 'marks_obtained' => 92.0, 'grade' => 'A', 'remarks' => 'Top scorer in Physics']
        ];

        $stmt = $this->db->prepare("INSERT INTO marks (student_id, course_id, term, marks_obtained, grade, remarks) VALUES (:student_id, :course_id, :term, :marks_obtained, :grade, :remarks)");
        foreach ($sampleData as $d) {
            $stmt->execute($d);
        }
    }

    /**
     * Get all marks records with optional filters
     */
    public function getAll($search = '', $termFilter = '', $courseFilter = '') {
        if ($this->db === null) return [];

        $sql = "SELECT m.*, 
                       s.student_code, s.adm_no, CONCAT(s.first_name, ' ', s.last_name) AS student_name, s.grade AS student_grade,
                       c.course_name AS subject_name, c.course_code AS subject_code
                FROM marks m
                INNER JOIN students s ON m.student_id = s.id
                INNER JOIN courses c ON m.course_id = c.id
                WHERE 1=1";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND (s.first_name LIKE :s1 OR s.last_name LIKE :s2 OR s.student_code LIKE :s3 OR c.course_name LIKE :s4)";
            $searchTerm = '%' . $search . '%';
            $params[':s1'] = $searchTerm;
            $params[':s2'] = $searchTerm;
            $params[':s3'] = $searchTerm;
            $params[':s4'] = $searchTerm;
        }

        if (!empty($termFilter)) {
            $sql .= " AND m.term = :term";
            $params[':term'] = $termFilter;
        }

        if (!empty($courseFilter)) {
            $sql .= " AND m.course_id = :course_id";
            $params[':course_id'] = (int)$courseFilter;
        }

        $sql .= " ORDER BY m.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get performance summary stats
     */
    public function getSummaryStats() {
        if ($this->db === null) {
            return ['avg_score' => 0, 'a_pass_rate' => 0, 'total_evaluated' => 0];
        }

        $avgScore = $this->db->query("SELECT AVG(marks_obtained) FROM marks")->fetchColumn() ?: 0;
        $totalEvaluated = $this->db->query("SELECT COUNT(*) FROM marks")->fetchColumn() ?: 0;
        $aCount = $this->db->query("SELECT COUNT(*) FROM marks WHERE grade = 'A'")->fetchColumn() ?: 0;

        $aPassRate = $totalEvaluated > 0 ? round(($aCount / $totalEvaluated) * 100, 1) : 0;

        return [
            'avg_score' => round($avgScore, 1),
            'a_pass_rate' => $aPassRate,
            'total_evaluated' => $totalEvaluated
        ];
    }

    /**
     * Create new term test mark
     */
    public function create($data) {
        if ($this->db === null) return false;

        $score = (float)($data['marks_obtained'] ?? 0);
        $grade = self::calculateGrade($score);

        $sql = "INSERT INTO marks (student_id, course_id, term, marks_obtained, grade, remarks) VALUES (:student_id, :course_id, :term, :marks_obtained, :grade, :remarks)";
        try {
            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute([
                ':student_id' => (int)$data['student_id'],
                ':course_id' => (int)$data['course_id'],
                ':term' => $data['term'] ?? 'Term 1',
                ':marks_obtained' => $score,
                ':grade' => $grade,
                ':remarks' => $data['remarks'] ?? ''
            ]);

            if ($res) {
                // Insert real-time notification
                try {
                    $st = $this->db->prepare("SELECT first_name, last_name FROM students WHERE id = :id");
                    $st->execute([':id' => (int)$data['student_id']]);
                    $stData = $st->fetch();

                    $cs = $this->db->prepare("SELECT course_name FROM courses WHERE id = :id");
                    $cs->execute([':id' => (int)$data['course_id']]);
                    $csData = $cs->fetch();

                    if ($stData && $csData) {
                        $stName = trim($stData['first_name'] . ' ' . $stData['last_name']);
                        $termName = $data['term'] ?? 'Term 1';
                        $notifStmt = $this->db->prepare("INSERT INTO notifications (title, message, type, is_read, ref_student_id) VALUES ('Term Marks Published', :msg, 'blue', 0, :sid)");
                        $notifStmt->execute([
                            ':msg' => "$stName scored $score% ($grade) in {$csData['course_name']} ($termName)",
                            ':sid' => (int)$data['student_id']
                        ]);
                    }
                } catch (Exception $e) {}
            }

            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update mark
     */
    public function update($id, $data) {
        if ($this->db === null) return false;

        $score = (float)($data['marks_obtained'] ?? 0);
        $grade = self::calculateGrade($score);

        $sql = "UPDATE marks SET student_id = :student_id, course_id = :course_id, term = :term, marks_obtained = :marks_obtained, grade = :grade, remarks = :remarks WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':student_id' => (int)$data['student_id'],
                ':course_id' => (int)$data['course_id'],
                ':term' => $data['term'],
                ':marks_obtained' => $score,
                ':grade' => $grade,
                ':remarks' => $data['remarks'] ?? '',
                ':id' => (int)$id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete mark
     */
    public function delete($id) {
        if ($this->db === null) return false;
        $stmt = $this->db->prepare("DELETE FROM marks WHERE id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }
}
