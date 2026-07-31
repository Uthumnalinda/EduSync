<?php
/**
 * EDUsync School Management System - Student Model / Service Class
 */

require_once __DIR__ . '/../config/Database.php';

class Student {
    private $db;

    public function __construct() {
        $dbInstance = new Database();
        $this->db = $dbInstance->getConnection();
    }

    /**
     * Get all students with optional search, grade filter, and status filter
     */
    public function getAll($search = '', $gradeFilter = '', $statusFilter = '') {
        $sql = "SELECT * FROM students WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :s1 OR last_name LIKE :s2 OR student_code LIKE :s3 OR adm_no LIKE :s4 OR email LIKE :s5)";
            $searchTerm = '%' . $search . '%';
            $params[':s1'] = $searchTerm;
            $params[':s2'] = $searchTerm;
            $params[':s3'] = $searchTerm;
            $params[':s4'] = $searchTerm;
            $params[':s5'] = $searchTerm;
        }

        if (!empty($gradeFilter)) {
            $sql .= " AND grade = :grade";
            $params[':grade'] = $gradeFilter;
        }

        if (!empty($statusFilter)) {
            $sql .= " AND status = :status";
            $params[':status'] = $statusFilter;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single student by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new student
     */
    public function create($data) {
        // Auto-generate student code and adm_no if empty
        if (empty($data['student_code'])) {
            $nextId = $this->getNextAutoIncrement();
            $data['student_code'] = 'S' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }
        if (empty($data['adm_no'])) {
            $data['adm_no'] = 'ADM' . date('Y') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }

        $sql = "INSERT INTO students (student_code, adm_no, first_name, last_name, dob, gender, email, phone, address, guardian_name, guardian_phone, grade, status)
                VALUES (:student_code, :adm_no, :first_name, :last_name, :dob, :gender, :email, :phone, :address, :guardian_name, :guardian_phone, :grade, :status)";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':student_code' => $data['student_code'],
                ':adm_no' => $data['adm_no'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':dob' => $data['dob'],
                ':gender' => $data['gender'],
                ':email' => $data['email'],
                ':phone' => $data['phone'],
                ':address' => $data['address'],
                ':guardian_name' => $data['guardian_name'],
                ':guardian_phone' => $data['guardian_phone'],
                ':grade' => $data['grade'],
                ':status' => $data['status'] ?? 'Active'
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update existing student
     */
    public function update($id, $data) {
        $sql = "UPDATE students SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    dob = :dob,
                    gender = :gender,
                    email = :email,
                    phone = :phone,
                    address = :address,
                    guardian_name = :guardian_name,
                    guardian_phone = :guardian_phone,
                    grade = :grade,
                    status = :status
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':dob' => $data['dob'],
                ':gender' => $data['gender'],
                ':email' => $data['email'],
                ':phone' => $data['phone'],
                ':address' => $data['address'],
                ':guardian_name' => $data['guardian_name'],
                ':guardian_phone' => $data['guardian_phone'],
                ':grade' => $data['grade'],
                ':status' => $data['status'],
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete student
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM students WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get distinct grades for filter dropdown
     */
    public function getGrades() {
        $stmt = $this->db->query("SELECT DISTINCT grade FROM students ORDER BY grade ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getNextAutoIncrement() {
        $stmt = $this->db->query("SELECT MAX(id) FROM students");
        $maxId = $stmt->fetchColumn();
        return ($maxId ? $maxId + 1 : 1);
    }
}
