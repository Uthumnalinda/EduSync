<?php
// Teacher model for managing master academic staff

require_once __DIR__ . '/../config/Database.php';

class Teacher {
    private $db;

    public function __construct() {
        $dbInstance = new Database();
        $this->db = $dbInstance->getConnection();
    }

    // Get all teachers with search and filters
    public function getAll($search = '', $subjectFilter = '', $statusFilter = '') {
        if ($this->db === null) return [];

        $sql = "SELECT * FROM teachers WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :s1 OR last_name LIKE :s2 OR teacher_code LIKE :s3 OR nic LIKE :s4 OR email LIKE :s5 OR subject LIKE :s6)";
            $searchTerm = '%' . $search . '%';
            $params[':s1'] = $searchTerm;
            $params[':s2'] = $searchTerm;
            $params[':s3'] = $searchTerm;
            $params[':s4'] = $searchTerm;
            $params[':s5'] = $searchTerm;
            $params[':s6'] = $searchTerm;
        }

        if (!empty($subjectFilter)) {
            $sql .= " AND subject = :subject";
            $params[':subject'] = $subjectFilter;
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
     * Get single teacher by ID
     */
    public function getById($id) {
        if ($this->db === null) return null;
        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get list of unique subjects
     */
    public function getSubjectsList() {
        if ($this->db === null) return [];
        $stmt = $this->db->query("SELECT DISTINCT subject FROM teachers WHERE subject IS NOT NULL AND subject != '' ORDER BY subject ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get summary stats for KPI cards
     */
    public function getStats() {
        if ($this->db === null) {
            return [
                'total' => 0,
                'active' => 0,
                'subjects_count' => 0,
                'total_payroll' => 0
            ];
        }

        $total = $this->db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
        $active = $this->db->query("SELECT COUNT(*) FROM teachers WHERE status = 'Active'")->fetchColumn();
        $subjectsCount = $this->db->query("SELECT COUNT(DISTINCT subject) FROM teachers")->fetchColumn();
        $payroll = $this->db->query("SELECT SUM(salary) FROM teachers WHERE status = 'Active'")->fetchColumn();

        return [
            'total' => (int)$total,
            'active' => (int)$active,
            'subjects_count' => (int)$subjectsCount,
            'total_payroll' => (float)$payroll
        ];
    }

    /**
     * Validate teacher form data fields
     */
    public function validate($data, $id = 0) {
        $errors = [];

        // First Name & Last Name validation
        if (empty($data['first_name']) || strlen(trim($data['first_name'])) < 2) {
            $errors[] = "First name must be at least 2 characters.";
        }
        if (empty($data['last_name']) || strlen(trim($data['last_name'])) < 2) {
            $errors[] = "Last name must be at least 2 characters.";
        }

        // NIC validation & uniqueness
        if (empty($data['nic'])) {
            $errors[] = "NIC / National ID is required.";
        } else {
            $stmt = $this->db->prepare("SELECT id FROM teachers WHERE nic = :nic AND id != :id LIMIT 1");
            $stmt->execute([':nic' => trim($data['nic']), ':id' => $id]);
            if ($stmt->fetch()) {
                $errors[] = "NIC number '{$data['nic']}' is already registered to another teacher.";
            }
        }

        // Email validation & uniqueness
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please provide a valid email address.";
        } else {
            $stmt = $this->db->prepare("SELECT id FROM teachers WHERE email = :email AND id != :id LIMIT 1");
            $stmt->execute([':email' => trim($data['email']), ':id' => $id]);
            if ($stmt->fetch()) {
                $errors[] = "Email address '{$data['email']}' is already registered to another teacher.";
            }
        }

        // Phone validation (Must be exactly 10 digits)
        $cleanPhone = preg_replace('/[^0-9]/', '', trim($data['phone'] ?? ''));
        if (empty($cleanPhone) || strlen($cleanPhone) !== 10) {
            $errors[] = "Phone number must be exactly 10 digits (e.g. 0771234567).";
        }

        // Salary validation (Cannot be negative)
        if (!isset($data['salary']) || !is_numeric($data['salary'])) {
            $errors[] = "Salary must be a valid number.";
        } elseif ((float)$data['salary'] < 0) {
            $errors[] = "Salary cannot be negative. Please enter a valid non-negative amount.";
        }

        // Subject & Qualification validation
        if (empty($data['subject'])) {
            $errors[] = "Subject / Specialty is required.";
        }
        if (empty($data['qualification'])) {
            $errors[] = "Qualification is required.";
        }

        return $errors;
    }

    /**
     * Create new teacher
     */
    public function create($data) {
        if ($this->db === null) return false;

        // Auto-generate teacher_code if empty
        if (empty($data['teacher_code'])) {
            $nextId = $this->getNextAutoIncrement();
            $data['teacher_code'] = 'T' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $sql = "INSERT INTO teachers (teacher_code, first_name, last_name, nic, subject, qualification, email, phone, address, date_joined, salary, status)
                VALUES (:teacher_code, :first_name, :last_name, :nic, :subject, :qualification, :email, :phone, :address, :date_joined, :salary, :status)";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':teacher_code' => $data['teacher_code'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':nic' => $data['nic'],
                ':subject' => $data['subject'],
                ':qualification' => $data['qualification'],
                ':email' => $data['email'],
                ':phone' => $data['phone'],
                ':address' => $data['address'],
                ':date_joined' => $data['date_joined'],
                ':salary' => $data['salary'],
                ':status' => $data['status'] ?? 'Active'
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update existing teacher
     */
    public function update($id, $data) {
        if ($this->db === null) return false;

        $sql = "UPDATE teachers 
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    nic = :nic, 
                    subject = :subject, 
                    qualification = :qualification, 
                    email = :email, 
                    phone = :phone, 
                    address = :address, 
                    date_joined = :date_joined, 
                    salary = :salary, 
                    status = :status 
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':nic' => $data['nic'],
                ':subject' => $data['subject'],
                ':qualification' => $data['qualification'],
                ':email' => $data['email'],
                ':phone' => $data['phone'],
                ':address' => $data['address'],
                ':date_joined' => $data['date_joined'],
                ':salary' => $data['salary'],
                ':status' => $data['status']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete teacher by ID
     */
    public function delete($id) {
        if ($this->db === null) return false;
        $stmt = $this->db->prepare("DELETE FROM teachers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get next auto-increment ID for code generation
     */
    private function getNextAutoIncrement() {
        $stmt = $this->db->query("SELECT MAX(id) FROM teachers");
        $maxId = $stmt->fetchColumn();
        return ($maxId ? $maxId + 1 : 1);
    }
}
