<?php
// Subject model for managing A/L subject catalog

require_once __DIR__ . '/../config/Database.php';

class Subject {
    private $db;

    public function __construct() {
        $dbInstance = new Database();
        $this->db = $dbInstance->getConnection();
    }

    // Fetch all subjects with optional search
    public function getAll($search = '', $gradeFilter = '') {
        if ($this->db === null) return [];

        $sql = "SELECT c.*, CONCAT(t.first_name, ' ', t.last_name) AS teacher_name 
                FROM courses c 
                LEFT JOIN teachers t ON c.teacher_id = t.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (c.course_code LIKE :s1 OR c.course_name LIKE :s2 OR c.description LIKE :s3 OR t.first_name LIKE :s4 OR t.last_name LIKE :s5)";
            $searchTerm = '%' . $search . '%';
            $params[':s1'] = $searchTerm;
            $params[':s2'] = $searchTerm;
            $params[':s3'] = $searchTerm;
            $params[':s4'] = $searchTerm;
            $params[':s5'] = $searchTerm;
        }

        if (!empty($gradeFilter)) {
            $sql .= " AND c.grade = :grade";
            $params[':grade'] = $gradeFilter;
        }

        $sql .= " ORDER BY c.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get list of active teachers for dropdown selection
     */
    public function getActiveTeachers() {
        if ($this->db === null) return [];
        $stmt = $this->db->query("SELECT id, CONCAT(first_name, ' ', last_name, ' (', subject, ')') AS full_name FROM teachers WHERE status = 'Active' ORDER BY first_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Validate subject form data
     */
    public function validate($data, $id = 0) {
        $errors = [];

        if (empty($data['course_name']) || strlen(trim($data['course_name'])) < 2) {
            $errors[] = "Subject name must be at least 2 characters.";
        }

        if (empty($data['grade'])) {
            $errors[] = "Grade / Class is required.";
        }

        // Check subject code uniqueness if provided
        if (!empty($data['course_code'])) {
            $stmt = $this->db->prepare("SELECT id FROM courses WHERE course_code = :code AND id != :id LIMIT 1");
            $stmt->execute([':code' => trim($data['course_code']), ':id' => $id]);
            if ($stmt->fetch()) {
                $errors[] = "Subject code '{$data['course_code']}' is already assigned to another subject.";
            }
        }

        return $errors;
    }

    /**
     * Create new subject
     */
    public function create($data) {
        if ($this->db === null) return false;

        // Auto-generate subject code if empty
        if (empty($data['course_code'])) {
            $maxId = $this->db->query("SELECT MAX(id) FROM courses")->fetchColumn();
            $nextId = $maxId ? $maxId + 1 : 1;
            $data['course_code'] = 'SUB' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $sql = "INSERT INTO courses (course_code, course_name, description, teacher_id, grade, credits, duration)
                VALUES (:course_code, :course_name, :description, :teacher_id, :grade, :credits, :duration)";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':course_code' => $data['course_code'],
                ':course_name' => $data['course_name'],
                ':description' => $data['description'] ?? '',
                ':teacher_id' => !empty($data['teacher_id']) ? (int)$data['teacher_id'] : null,
                ':grade' => $data['grade'],
                ':credits' => !empty($data['credits']) ? (int)$data['credits'] : 3,
                ':duration' => !empty($data['duration']) ? $data['duration'] : '40 weeks'
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update existing subject
     */
    public function update($id, $data) {
        if ($this->db === null) return false;

        $sql = "UPDATE courses SET 
                    course_code = :course_code,
                    course_name = :course_name,
                    description = :description,
                    teacher_id = :teacher_id,
                    grade = :grade,
                    duration = :duration
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':course_code' => $data['course_code'],
                ':course_name' => $data['course_name'],
                ':description' => $data['description'] ?? '',
                ':teacher_id' => !empty($data['teacher_id']) ? (int)$data['teacher_id'] : null,
                ':grade' => $data['grade'],
                ':duration' => !empty($data['duration']) ? $data['duration'] : '40 weeks',
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete subject
     */
    public function delete($id) {
        if ($this->db === null) return false;
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
