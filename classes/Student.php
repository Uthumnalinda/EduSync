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
     * Validate student form data fields
     */
    public function validate($data, $id = 0) {
        $errors = [];

        // First Name & Last Name
        if (empty($data['first_name']) || strlen(trim($data['first_name'])) < 2) {
            $errors[] = "First name must be at least 2 characters.";
        }
        if (empty($data['last_name']) || strlen(trim($data['last_name'])) < 2) {
            $errors[] = "Last name must be at least 2 characters.";
        }

        // Email validation & uniqueness
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please provide a valid student email address.";
        } else {
            $stmt = $this->db->prepare("SELECT id FROM students WHERE email = :email AND id != :id LIMIT 1");
            $stmt->execute([':email' => trim($data['email']), ':id' => $id]);
            if ($stmt->fetch()) {
                $errors[] = "Email address '{$data['email']}' is already registered to another student.";
            }
        }

        // Student Phone validation (10 Digits)
        if (!empty($data['phone'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', trim($data['phone']));
            if (strlen($cleanPhone) !== 10) {
                $errors[] = "Student phone number must be exactly 10 digits (e.g. 0771234567).";
            }
        }

        // Guardian Name & Phone validation (10 Digits)
        if (empty($data['guardian_name']) || strlen(trim($data['guardian_name'])) < 2) {
            $errors[] = "Guardian name is required.";
        }
        if (empty($data['guardian_phone'])) {
            $errors[] = "Guardian phone number is required.";
        } else {
            $cleanGPhone = preg_replace('/[^0-9]/', '', trim($data['guardian_phone']));
            if (strlen($cleanGPhone) !== 10) {
                $errors[] = "Guardian phone number must be exactly 10 digits (e.g. 0771234567).";
            }
        }

        // Date of Birth validation
        if (empty($data['dob']) || strtotime($data['dob']) >= time()) {
            $errors[] = "Please enter a valid past Date of Birth.";
        }

        // Grade validation
        if (empty($data['grade'])) {
            $errors[] = "Grade is required.";
        }

        return $errors;
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
        if ($this->db === null) return [];
        $stmt = $this->db->query("SELECT DISTINCT grade FROM students ORDER BY grade ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get current active Academic Year setting
     */
    public function getAcademicYear() {
        if ($this->db === null) return '2024/2025';
        $stmt = $this->db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'academic_year' LIMIT 1");
        $stmt->execute();
        $val = $stmt->fetchColumn();
        return $val ?: '2024/2025';
    }

    /**
     * Advance to Next Academic Year (Rollover Logic)
     * - Teachers: Remain 100% active (untouched)
     * - Grade 13 Students: Status changed to 'Completed A/L' (School Leavers)
     * - Grade 12 Students: Promoted to Grade 13
     */
    public function advanceAcademicYear($nextYear) {
        if ($this->db === null) return false;
        try {
            $this->db->beginTransaction();

            // 1. Change Grade 13 active students to 'Completed A/L'
            $stmt13 = $this->db->prepare("UPDATE students SET status = 'Completed A/L' WHERE grade LIKE '%Grade 13%' AND status = 'Active'");
            $stmt13->execute();

            // 2. Promote Grade 12 active students to Grade 13
            $stmt12 = $this->db->prepare("UPDATE students SET grade = REPLACE(grade, 'Grade 12', 'Grade 13') WHERE grade LIKE '%Grade 12%' AND status = 'Active'");
            $stmt12->execute();

            // 3. Update active academic year in system_settings
            $stmtSet = $this->db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('academic_year', :ny) ON DUPLICATE KEY UPDATE setting_value = :ny2");
            $stmtSet->execute([':ny' => $nextYear, ':ny2' => $nextYear]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    /**
     * Revert / Reset Academic Year to Previous Session
     */
    public function revertAcademicYear($prevYear = '2024/2025') {
        if ($this->db === null) return false;
        try {
            $this->db->beginTransaction();

            // 1. Revert 'Completed A/L' status back to 'Active'
            $stmtResetStatus = $this->db->prepare("UPDATE students SET status = 'Active' WHERE status = 'Completed A/L'");
            $stmtResetStatus->execute();

            // 2. Revert Grade 13 back to Grade 12 where applicable
            $stmtResetGrade = $this->db->prepare("UPDATE students SET grade = REPLACE(grade, 'Grade 13', 'Grade 12') WHERE grade LIKE '%Grade 13%'");
            $stmtResetGrade->execute();

            // 3. Reset system_settings academic_year
            $stmtSet = $this->db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('academic_year', :py) ON DUPLICATE KEY UPDATE setting_value = :py2");
            $stmtSet->execute([':py' => $prevYear, ':py2' => $prevYear]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    private function getNextAutoIncrement() {
        $stmt = $this->db->query("SELECT MAX(id) FROM students");
        $maxId = $stmt->fetchColumn();
        return ($maxId ? $maxId + 1 : 1);
    }
}
