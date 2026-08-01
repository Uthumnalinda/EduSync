<?php
/**
 * EDUsync School Management System - Class & Stream Allocation Service Model
 */

require_once __DIR__ . '/../config/Database.php';

class Enrollment {
    private $db;

    public function __construct() {
        $dbInstance = new Database();
        $this->db = $dbInstance->getConnection();
    }

    /**
     * Get all class allocations with optional search, stream, and class section filter
     */
    public function getAll($search = '', $streamFilter = '', $classFilter = '') {
        if ($this->db === null) return [];

        $sql = "SELECT e.*, 
                       s.student_code, s.adm_no, CONCAT(s.first_name, ' ', s.last_name) AS student_name, s.email,
                       c.course_name AS class_section, c.grade, c.duration AS stream_name,
                       CONCAT(t.first_name, ' ', t.last_name) AS class_teacher
                FROM enrollments e
                INNER JOIN students s ON e.student_id = s.id
                INNER JOIN courses c ON e.course_id = c.id
                LEFT JOIN teachers t ON c.teacher_id = t.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (s.first_name LIKE :s1 OR s.last_name LIKE :s2 OR s.student_code LIKE :s3 OR s.adm_no LIKE :s4 OR c.course_name LIKE :s5)";
            $searchTerm = '%' . $search . '%';
            $params[':s1'] = $searchTerm;
            $params[':s2'] = $searchTerm;
            $params[':s3'] = $searchTerm;
            $params[':s4'] = $searchTerm;
            $params[':s5'] = $searchTerm;
        }

        if (!empty($streamFilter)) {
            $sql .= " AND c.duration = :stream";
            $params[':stream'] = $streamFilter;
        }

        if (!empty($classFilter)) {
            $sql .= " AND c.course_name = :className";
            $params[':className'] = $classFilter;
        }

        $sql .= " ORDER BY e.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active students for dropdown
     */
    public function getActiveStudents() {
        if ($this->db === null) return [];
        $stmt = $this->db->query("SELECT id, CONCAT(student_code, ' - ', first_name, ' ', last_name, ' (', grade, ')') AS full_name FROM students WHERE status = 'Active' ORDER BY first_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get class sections from subjects table
     */
    public function getClassSections() {
        if ($this->db === null) return [];
        $stmt = $this->db->query("SELECT c.id, c.course_name, c.grade, c.duration AS stream_name, CONCAT(t.first_name, ' ', t.last_name) AS teacher_name 
                                  FROM courses c 
                                  LEFT JOIN teachers t ON c.teacher_id = t.id 
                                  ORDER BY c.grade ASC, c.course_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Allocate student to class
     */
    public function create($data) {
        if ($this->db === null) return false;

        // Check if student is already in this class
        $check = $this->db->prepare("SELECT id FROM enrollments WHERE student_id = :sid AND course_id = :cid LIMIT 1");
        $check->execute([':sid' => $data['student_id'], ':cid' => $data['course_id']]);
        if ($check->fetch()) {
            return false;
        }

        $sql = "INSERT INTO enrollments (student_id, course_id, enrollment_date, status) VALUES (:student_id, :course_id, :date, :status)";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':student_id' => (int)$data['student_id'],
                ':course_id' => (int)$data['course_id'],
                ':date' => !empty($data['enrollment_date']) ? $data['enrollment_date'] : date('Y-m-d'),
                ':status' => $data['status'] ?? 'Enrolled'
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update class allocation
     */
    public function update($id, $data) {
        if ($this->db === null) return false;

        $sql = "UPDATE enrollments SET student_id = :student_id, course_id = :course_id, status = :status WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':student_id' => (int)$data['student_id'],
                ':course_id' => (int)$data['course_id'],
                ':status' => $data['status'],
                ':id' => (int)$id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete allocation
     */
    public function delete($id) {
        if ($this->db === null) return false;
        $stmt = $this->db->prepare("DELETE FROM enrollments WHERE id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }
}
